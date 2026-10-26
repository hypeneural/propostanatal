#!/usr/bin/env python3
"""Produce a review-only mapping for the future Natal V5 manager importer.

Never writes CRM, DB, production manifests or network resources.
The JSON on stdout is NOT a payload accepted by Laravel. Actual stage/message IDs
and expected_configuration_hash must be resolved in the manager by authorized reads.
"""
from __future__ import annotations

import argparse
import json
import sys
from pathlib import Path

if hasattr(sys.stdout, 'reconfigure'):
    sys.stdout.reconfigure(encoding='utf-8')
if hasattr(sys.stderr, 'reconfigure'):
    sys.stderr.reconfigure(encoding='utf-8')

ROOT = Path(__file__).resolve().parents[1]
DRAFT = ROOT / "editorial" / "whatsapp-v5.draft.json"
VALID_ROLES = {
    "initial", "whatsapp_h1", "whatsapp_90min", "whatsapp_d1", "whatsapp_d2",
    "whatsapp_d3", "whatsapp_d4", "whatsapp_d5", "whatsapp_d10",
}
VALID_TYPES = {"video", "image", "action", "attachment"}


def build(data: dict) -> dict:
    if data.get("metadata", {}).get("status") != "DRAFT_NOT_APPROVED":
        raise ValueError("Refusing non-draft V5 source.")
    operations = []
    for stage in data["stage_edits"]:
        role = stage["role"]
        if role not in VALID_ROLES:
            continue
        for m in stage.get("message_edits", []):
            typ = m["type"]
            if typ not in VALID_TYPES:
                raise ValueError(f"Unknown message type {typ}")
            field = "message" if typ == "action" else "title"
            values = (
                [(415, m["message_with_name"]), (416, m["message_without_name"])]
                if role == "initial" and m["message_role"] == "intro_cta"
                else [(415, m[field]), (416, m[field])]
            )
            for campaign, text in values:
                if not isinstance(text, str):
                    raise ValueError(f"Missing content {role}/{m['message_role']}/{field}")
                if typ in ("image", "video") and len(text) > 200:
                    raise ValueError(f"Caption limit {role}/{m['message_role']}")
                operations.append({
                    "campaign_id": campaign,
                    "stage_role": role,
                    "message_role": m["message_role"],
                    "model": "messages",
                    "field": field,
                    "media_type": typ,
                    "asset_basename_expected": m.get("asset_basename"),
                    "expected_runtime_stage_id": None,
                    "expected_runtime_message_id": None,
                    "proposed_value": text,
                    "status": "READ_ONLY_PREVIEW_NOT_AN_UPDATE",
                })
    buttons = data["stage_edits"][0]["buttons"]
    return {
        "schema": "evydencia.v5.manager_import_preview.v1",
        "draft_revision": data["metadata"]["revision"],
        "mode": "READ_ONLY_PREVIEW_NO_PRODUCTION_WRITE",
        "applicable_in_current_manager": False,
        "reason": "Ordinary MessageController.update does not provision/update approved manifest hash and frozen runs.",
        "requires_before_any_apply": [
            "Fresh authenticated 415/416 read by stage_role and message_role",
            "Exact current stage and message IDs, not historical hints",
            "No changes to button IDs/URL allowlist and no extra stage/message types",
            "Versioned approved full CRM V5 manifest, expected_configuration_hash/CAS",
            "Review, dry-run, rollback plan and approved runtime suppression for D+10",
            "Consent, human-operator review and authorized canary",
        ],
        "buttons_preserved": buttons,
        "planned_field_operations": operations,
        "operation_count": len(operations),
        "existing_D10_document": "BLOCKED_NO_CHANGE_OR_SEND",
        "sms_resend_smartvoice": "PRESERVE_NO_ACTIVATION",
        "production_go": False,
    }


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--check", action="store_true", help="Integrity gate only, do not print full text")
    args = parser.parse_args()
    try:
        data = json.loads(DRAFT.read_text(encoding="utf-8-sig"))
        result = build(data)
    except (OSError, ValueError, KeyError, TypeError) as exc:
        print(f"MANAGER_PLAN_FAILED: {exc}", file=sys.stderr)
        return 2
    roles = {x["stage_role"] for x in result["planned_field_operations"]}
    if roles != VALID_ROLES or result["production_go"] or not result["operation_count"]:
        print("MANAGER_PLAN_FAILED: role coverage or production guard", file=sys.stderr)
        return 2
    if args.check:
        print(f"MANAGER_PLAN_OK: {result['operation_count']} field-level candidate changes, 9 roles, 2 variants; NOT IMPORTABLE until versioned CRM release.")
    else:
        print(json.dumps(result, ensure_ascii=False, indent=2))
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
