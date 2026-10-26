#!/usr/bin/env python3
"""Offline consistency check: CRM-approved V4 manifest vs local read-only DB inventory.

No network, MySQL connection, write operations, uploads or customer-data output.
Usage:
  python scripts/reconcile_v4_local.py --self-test
  python scripts/reconcile_v4_local.py --manifest ../evydencia-crm/docs/active/natal-2026-proposta/manifesto-natal-2026.v4.json --inventory reports/<timestamp>/inventory.json
A successful check means 'safe to review', NEVER 'approved to publish'.
"""
from __future__ import annotations
import argparse
import json
import sys
from pathlib import Path

CANONICAL_ROLES = [
    "initial", "whatsapp_h1", "whatsapp_90min", "whatsapp_d2", "whatsapp_d4",
    "whatsapp_d1", "whatsapp_d3", "whatsapp_d5", "whatsapp_d10", "document_d10",
    "sms_h2", "sms_d1", "sms_d2", "sms_d3", "sms_d4", "sms_d5",
    "sms_d10", "sms_d15", "sms_d20",
]
VARIANTS = {"with_name": 415, "without_name": 416}


def selected_ids(mapping: dict, roles: list[str]) -> tuple[list[int], list[str]]:
    problems = []
    values = mapping.get("stages")
    if not isinstance(values, list) or len(values) != len(roles):
        return [], ["manifest.mapping stages missing or wrong length"]
    try:
        ids = [int(v) for v in values]
    except (ValueError, TypeError):
        return [], ["manifest.mapping stages invalid"]
    if len(set(ids)) != len(ids) or any(v < 1 for v in ids):
        problems.append("manifest.mapping stage IDs duplicate/invalid")
    role_map = mapping.get("stage_roles")
    if not isinstance(role_map, dict) or role_map != dict(zip(roles, ids)):
        problems.append("manifest.mapping.stage_roles disagrees with V4 roles and stage IDs")
    return ids, problems


def file_basename(message: dict) -> str | None:
    value = message.get("media_basename")
    if isinstance(value, str) and value:
        return value
    # Old reports may contain absolute URLs only; strip query and decode basename.
    from urllib.parse import urlsplit, unquote
    url = message.get("url")
    if not isinstance(url, str) or not url.startswith(("https://", "http://")):
        return None
    return unquote(urlsplit(url).path.split("/")[-1]) or None


def reconcile(manifest: dict, inventory: dict, draft: dict) -> dict:
    issues, evidence = [], []
    manifest_flow = inventory.get("flow", {}).get("manifest", {})
    if manifest.get("schema_version") != 4:
        issues.append("Manifest schema_version is not V4.")
    if draft.get("metadata", {}).get("status") != "DRAFT_NOT_APPROVED" or draft.get("metadata", {}).get("write_to_production") is not False:
        issues.append("V5 must remain a non-deployable draft.")
    if draft.get("metadata", {}).get("source_manifest_revision") != manifest.get("revision"):
        issues.append("Draft base revision differs from supplied manifest.")
    if manifest_flow.get("revision") != manifest.get("revision"):
        issues.append("PRODUCTION_DB manifest revision differs from supplied Git V4.")
    if manifest_flow.get("content_profile_version") != 4:
        issues.append("PRODUCTION_DB selected content profile is not V4.")
    if manifest_flow.get("status") != "approved":
        issues.append("PRODUCTION_DB selected manifest is not approved.")
    if not manifest_flow.get("configuration_hash"):
        issues.append("Approved configuration hash absent from inventory; rerun latest audit.php.")
    stage_rows = {int(row["id"]): row for row in inventory.get("stages", []) if isinstance(row, dict) and "id" in row}
    draft_roles = {row.get("role") for row in draft.get("stage_edits", []) if isinstance(row, dict)}
    if draft_roles != set(CANONICAL_ROLES):
        issues.append("Editorial draft role set does not match fixed V4 roles.")
    all_ids = []
    for variant, campaign_id in VARIANTS.items():
        manifest_stages = manifest.get("variants", {}).get(variant, {}).get("stages", [])
        mapping = manifest_flow.get("mapping", {}).get(variant, {})
        ids, errs = selected_ids(mapping, CANONICAL_ROLES)
        issues.extend(variant + ": " + e for e in errs)
        if not ids:
            continue
        all_ids.extend(ids)
        if int(mapping.get("campaign_id", -1)) != campaign_id:
            issues.append(f"{variant}: campaign ID mismatch in DB manifest mapping.")
        if len(manifest_stages) != 19:
            issues.append(f"{variant}: Git manifest has not exactly 19 stages.")
            continue
        for pos, expected in enumerate(manifest_stages):
            role, stage_id = CANONICAL_ROLES[pos], ids[pos]
            if expected.get("role") != role:
                issues.append(f"{variant}/{role}: role order different in Git manifest.")
                continue
            stage = stage_rows.get(stage_id)
            if stage is None:
                issues.append(f"{variant}/{role}: selected stage ID {stage_id} not present in DB inventory.")
                continue
            if int(stage.get("campaign_id", -1)) != campaign_id or int(stage.get("is_active", 0)) != 1:
                issues.append(f"{variant}/{role}: wrong campaign or inactive selected stage.")
            linked = [x.get("message") for x in stage.get("messages", []) if isinstance(x, dict)]
            linked = [x for x in linked if isinstance(x, dict)]
            declared = expected.get("messages", [])
            if len(linked) != len(declared):
                issues.append(f"{variant}/{role}: expected {len(declared)} messages, DB has {len(linked)}.")
                continue
            for i, (m, ref) in enumerate(zip(linked, declared)):
                mtype = m.get("type")
                if mtype != ref.get("type"):
                    issues.append(f"{variant}/{role}/message[{i}]: type {mtype} != {ref.get('type')}.")
                asset = ref.get("asset")
                if isinstance(asset, dict) and asset.get("basename"):
                    actual = file_basename(m)
                    if actual != asset["basename"]:
                        issues.append(f"{variant}/{role}/message[{i}]: basename {actual!r} != Git V4 {asset['basename']!r}.")
                evidence.append({
                    "variant": variant, "role": role, "stage_id": stage_id,
                    "message_id": m.get("id"), "type": mtype,
                    "media_basename": file_basename(m),
                    "manifest_asset": asset.get("basename") if isinstance(asset, dict) else None,
                })
    if len(all_ids) != 38 or len(set(all_ids)) != len(all_ids):
        issues.append("Expected exactly 38 distinct selected stage IDs across both variants.")
    return {
        "status": "BLOCKED_DIVERGENCE" if issues else "CONSISTENT_FOR_EDITORIAL_REVIEW_ONLY",
        "manifest_revision": manifest.get("revision"),
        "db_revision": manifest_flow.get("revision"),
        "db_configuration_hash": manifest_flow.get("configuration_hash"),
        "selected_stages_checked": len(all_ids),
        "message_bindings_checked": len(evidence),
        "problems": sorted(set(issues)),
        "bindings": evidence,
        "warning": "Output contains internal campaign IDs/file basenames; keep local, never commit production reports. Consistency does NOT authorize --apply."
    }


def self_test() -> None:
    roles = ["initial", "whatsapp_h1"]
    actual, problems = selected_ids({"stages": [657, 658], "stage_roles": dict(zip(roles, [657, 658]))}, roles)
    assert actual == [657, 658] and not problems
    _, problems = selected_ids({"stages": [657, 658], "stage_roles": {"initial": 999}}, roles)
    assert problems
    assert file_basename({"url": "https://evydencia.com/resources/video/clip.mp4?private=redacted"}) == "clip.mp4"
    print("SELF-TEST OK: role mapping, mismatch gate, safe basename.")


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--self-test", action="store_true")
    parser.add_argument("--manifest", type=Path)
    parser.add_argument("--inventory", type=Path)
    parser.add_argument("--draft", type=Path, default=Path(__file__).resolve().parents[1] / "editorial" / "whatsapp-v5.draft.json")
    options = parser.parse_args()
    if options.self_test:
        self_test()
        return 0
    if options.manifest is None or options.inventory is None:
        parser.error("--manifest and --inventory are mandatory unless --self-test is used")
    try:
        source = json.loads(options.manifest.read_text(encoding="utf-8-sig"))
        inventory = json.loads(options.inventory.read_text(encoding="utf-8-sig"))
        draft = json.loads(options.draft.read_text(encoding="utf-8-sig"))
        report = reconcile(source, inventory, draft)
    except (OSError, ValueError, KeyError, TypeError) as exc:
        print("RECONCILE_FAILED: " + type(exc).__name__ + ": " + str(exc), file=sys.stderr)
        return 3
    print(json.dumps(report, ensure_ascii=False, indent=2))
    return 0 if report["status"] == "CONSISTENT_FOR_EDITORIAL_REVIEW_ONLY" else 2


if __name__ == "__main__":
    sys.exit(main())
