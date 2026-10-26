#!/usr/bin/env python3
"""Check existing LOCAL video files against the Git V4 manifest; sample 15/50/85%.

No downloads, no OCR, no external requests, no CRM connection, no FFmpeg required.
Frames stay under ignored reports/media-inspection/, never commit frames of real people.
Requires opencv-python-headless ONLY for visual frame samples; sha256 works without it.
"""
from __future__ import annotations
import argparse
import hashlib
import json
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]


def checksum(path: Path) -> str:
    h = hashlib.sha256()
    with path.open("rb") as stream:
        for block in iter(lambda: stream.read(1024 * 1024), b""):
            h.update(block)
    return h.hexdigest()


def list_videos(manifest: dict) -> list[dict]:
    found = {}
    for variant in ("with_name", "without_name"):
        for stage in manifest.get("variants", {}).get(variant, {}).get("stages", []):
            for message in stage.get("messages", []):
                asset = message.get("asset") or {}
                if message.get("type") != "video" or not asset.get("basename"):
                    continue
                key = asset["basename"]
                row = {"role": stage["role"], "file": key, "sha256_expected": asset.get("sha256"), "size_expected": asset.get("bytes")}
                if key in found and found[key]["sha256_expected"] != row["sha256_expected"]:
                    raise ValueError("Same media name has contradictory SHA values between campaign variants.")
                found[key] = row
    return list(found.values())


def inspect_local(manifest: dict, media_dir: Path, output: Path, with_frames: bool) -> tuple[list[dict], int]:
    items = list_videos(manifest)
    code = 0
    cv2 = None
    if with_frames:
        try:
            import cv2 as cv2_module
            cv2 = cv2_module
        except ImportError:
            raise RuntimeError("OpenCV unavailable; install locally with python -m pip install opencv-python-headless, or omit --frames")
        output.mkdir(parents=True, exist_ok=True)
    for item in items:
        path = media_dir / item["file"]
        if not path.is_file():
            item["status"] = "MISSING_LOCAL_FILE"
            code = 2
            continue
        digest = checksum(path)
        item["sha256_actual"] = digest
        item["size_actual"] = path.stat().st_size
        expected = item.get("sha256_expected")
        item["status"] = "MATCH" if digest == expected and item["size_actual"] == item.get("size_expected") else "MISMATCH"
        if item["status"] != "MATCH":
            code = 2
            # IMPORTANT: do not trust or inspect mismatching content as approved asset.
            continue
        if cv2 is None:
            continue
        capture = cv2.VideoCapture(str(path))
        try:
            if not capture.isOpened():
                item["frame_status"] = "COULD_NOT_OPEN"
                code = 2
                continue
            fps = float(capture.get(cv2.CAP_PROP_FPS))
            count = int(capture.get(cv2.CAP_PROP_FRAME_COUNT))
            width = int(capture.get(cv2.CAP_PROP_FRAME_WIDTH))
            height = int(capture.get(cv2.CAP_PROP_FRAME_HEIGHT))
            item.update({"fps": round(fps, 2), "frames": count, "width": width, "height": height, "duration_estimate_seconds": round(count / fps, 2) if fps > 0 else None})
            item["samples"] = []
            for pct in (.15, .5, .85):
                frame_id = max(0, min(count - 1, int(count * pct)))
                capture.set(cv2.CAP_PROP_POS_FRAMES, frame_id)
                ok, frame = capture.read()
                if not ok:
                    item["samples"].append({"fraction": pct, "status": "UNAVAILABLE"})
                    code = 2
                    continue
                out_path = output / (item["role"] + "_" + str(int(pct * 100)) + ".jpg")
                cv2.imwrite(str(out_path), frame)
                item["samples"].append({"fraction": pct, "status": "SAVED_LOCALLY", "frame": frame_id, "filename": out_path.name})
        finally:
            capture.release()
    return items, code


def main() -> int:
    p = argparse.ArgumentParser(description=__doc__)
    p.add_argument("--self-test", action="store_true")
    p.add_argument("--manifest", type=Path)
    p.add_argument("--media-dir", type=Path)
    p.add_argument("--frames", action="store_true")
    args = p.parse_args()
    if args.self_test:
        h = hashlib.sha256(b"test").hexdigest()
        assert h == "9f86d081884c7d659a2feaa0c55ad015a3bf4f1b2b0b822cd15d6c15b0f00a08"
        print("SELF-TEST OK: SHA256 and offline-only implementation.")
        return 0
    if not args.manifest or not args.media_dir:
        p.error("Provide --manifest and --media-dir")
    try:
        manifest = json.loads(args.manifest.read_text(encoding="utf-8-sig"))
        if manifest.get("schema_version") != 4:
            raise ValueError("Expected 2026 content profile V4 manifest")
        items, code = inspect_local(manifest, args.media_dir, ROOT / "reports" / "media-inspection", args.frames)
        print(json.dumps({"result": "MEDIA_MATCH" if code == 0 else "MISSING_OR_MISMATCH", "videos": items, "limitation": "Frame samples do not verify full video, audio/subtitles or marketing rights; inspect manually."}, ensure_ascii=False, indent=2))
        return code
    except (OSError, ValueError, RuntimeError) as exc:
        print(type(exc).__name__ + ": " + str(exc), file=sys.stderr)
        return 3


if __name__ == "__main__":
    raise SystemExit(main())
