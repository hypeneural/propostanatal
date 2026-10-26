#!/usr/bin/env python3
"""Offline catalog audit for all distinct media assets from approved Natal 2026 V4.

Does not download anything, connect to the CRM, change a file, or generate PII.
Pass --media-dir to check local copies (never commit those copies).
"""
from __future__ import annotations

import argparse
import hashlib
import json
import sys
from pathlib import Path

TYPES = {"video", "image", "attachment", "audio"}


def extract_assets(manifest: dict) -> list[dict]:
    if manifest.get("schema_version") != 4:
        raise ValueError("Expected a version 4 approved Natal manifest")
    unique: dict[str, dict] = {}
    for variant in ("with_name", "without_name"):
        stages = manifest.get("variants", {}).get(variant, {}).get("stages", [])
        if len(stages) != 19:
            raise ValueError(f"Expected 19 stages for {variant}")
        for stage in stages:
            for msg in stage.get("messages", []):
                asset = msg.get("asset")
                if not isinstance(asset, dict):
                    continue
                basename, sha, size = asset.get("basename"), asset.get("sha256"), asset.get("bytes")
                if msg.get("type") not in TYPES:
                    raise ValueError(f"Unexpected asset type in {stage.get('role')}")
                if not isinstance(basename, str) or basename in ("", ".", "..") or "/" in basename or "\\" in basename:
                    raise ValueError("Unexpected or unsafe asset basename")
                if not isinstance(sha, str) or len(sha) != 64 or not isinstance(size, int) or size < 1:
                    raise ValueError("Asset SHA/size missing")
                item = {
                    "basename": basename, "type": msg["type"], "sha256_expected": sha,
                    "size_expected": size, "roles": [stage["role"]], "variants": [variant],
                    "risk": "P0_DOCUMENT_D10" if stage["role"] == "document_d10" else
                    ("T0_VISUAL_REVIEW" if stage["role"] == "initial" else "MEDIA_EDITORIAL_REVIEW")
                }
                if basename in unique:
                    previous = unique[basename]
                    if any(previous[k] != item[k] for k in ("type", "sha256_expected", "size_expected")):
                        raise ValueError("Contradictory asset manifest under same basename")
                    if stage["role"] not in previous["roles"]:
                        previous["roles"].append(stage["role"])
                    if variant not in previous["variants"]:
                        previous["variants"].append(variant)
                else:
                    unique[basename] = item
    return list(unique.values())


def sha256_file(path: Path) -> str:
    digest = hashlib.sha256()
    with path.open("rb") as reader:
        for block in iter(lambda: reader.read(1048576), b""):
            digest.update(block)
    return digest.hexdigest()


def inventory(manifest: dict, media_dir: Path | None) -> list[dict]:
    assets = extract_assets(manifest)
    for item in assets:
        if media_dir is None:
            item["status"] = "MANIFEST_ONLY_NO_LOCAL_MEDIA"
            continue
        path = media_dir / item["basename"]
        if not path.is_file():
            item["status"] = "MISSING_LOCAL_FILE_NOT_PROOF_OF_CDN_ABSENCE"
            continue
        if path.is_symlink():
            item["status"] = "SKIPPED_SYMLINK"
            continue
        size = path.stat().st_size
        sha = sha256_file(path)
        item["size_actual"] = size
        item["sha256_actual"] = sha
        item["status"] = "MATCH" if sha == item["sha256_expected"] and size == item["size_expected"] else "MISMATCH"
    return assets


def test() -> None:
    sample = {"schema_version": 4, "variants": {}}
    for variant in ("with_name", "without_name"):
        sample["variants"][variant] = {"stages": [
            {"role": "initial", "messages": [{"type": "image", "asset": {
                "basename": "natal-image.png", "sha256": "a" * 64, "bytes": 200
            }}]}] + [{"role": f"r{i}", "messages": []} for i in range(18)]
        }
    rows = inventory(sample, None)
    assert len(rows) == 1 and set(rows[0]["variants"]) == {"with_name", "without_name"}
    assert rows[0]["status"] == "MANIFEST_ONLY_NO_LOCAL_MEDIA"
    print("SELF-TEST OK: 19 roles/variant, deduplication, safe manifest-only mode.")


def main() -> int:
    p = argparse.ArgumentParser(description=__doc__)
    p.add_argument("--manifest", type=Path)
    p.add_argument("--media-dir", type=Path, help="Private directory holding local media copies; optional")
    p.add_argument("--self-test", action="store_true")
    p.add_argument("--strict", action="store_true", help="Exit 2 unless every local file matches the approved V4")
    opts = p.parse_args()
    if opts.self_test:
        test()
        return 0
    if not opts.manifest:
        p.error("--manifest is required")
    try:
        manifest = json.loads(opts.manifest.read_text(encoding="utf-8-sig"))
        rows = inventory(manifest, opts.media_dir)
    except (ValueError, KeyError, OSError) as err:
        print(f"AUDIT_FAILED: {type(err).__name__}: {err}", file=sys.stderr)
        return 3
    counts = {status: sum(row["status"] == status for row in rows) for status in set(r["status"] for r in rows)}
    print(json.dumps({
        "result": "OFFLINE_ASSET_AUDIT", "distinct_assets": len(rows), "counts": counts,
        "assets": rows,
        "limits": [
            "Matching hash and size do not prove good content, audio, engagement, provider delivery or WhatsApp rendering.",
            "A missing local copy does not mean that the CDN or CRM lost the file.",
            "Document D+10 remains blocked for publication even when SHA matches.",
        ]
    }, ensure_ascii=False, indent=2))
    return 2 if opts.strict and any(r["status"] != "MATCH" for r in rows) else 0


if __name__ == "__main__":
    sys.exit(main())
