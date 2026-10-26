#!/usr/bin/env python3
"""Offline forensic release gates for Natal V5, with fail-closed production mode.

No MySQL, network, CRM or provider access. No write operations. Uses dated
published evidence only to detect contradictions, NEVER to clear live production.
"""
from __future__ import annotations

import argparse
import json
import re
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
DRAFT = ROOT / "editorial" / "whatsapp-v5.draft.json"
REPORT = ROOT / "docs" / "EDITORIAL-V5-20261026" / "relatorios" / "AUDITORIA-FINAL.md"
TIMELINE = ROOT / "docs" / "EDITORIAL-V5-20261026" / "relatorios" / "timeline.md"


def parse_pending_table(content: str) -> dict:
    pending: dict[int, int] = {}
    skipped: dict[int, int] = {}
    for line in content.splitlines():
        cols = [c.strip() for c in line.strip().strip("|").split("|")]
        if len(cols) != 6 or cols[0] not in ("666", "685"):
            continue
        if cols[1] not in ("415", "416"):
            continue
        if not cols[4].isdigit():
            continue
        status = cols[2].strip(chr(96))
        group = pending if status == "pending" else skipped if status == "skipped" else None
        if group is not None:
            camp = int(cols[1])
            group[camp] = group.get(camp, 0) + int(cols[4])
    return {
        "pending_by_campaign_at_snapshot": dict(sorted(pending.items())),
        "pending_total_at_snapshot": sum(pending.values()),
        "skipped_by_campaign_at_snapshot": dict(sorted(skipped.items())),
        "skipped_total_at_snapshot": sum(skipped.values()),
        "date_is_historical": True,
    }


def check() -> dict:
    draft = json.loads(DRAFT.read_text(encoding="utf-8-sig"))
    report = REPORT.read_text(encoding="utf-8")
    timeline = TIMELINE.read_text(encoding="utf-8")
    stages = draft["stage_edits"]
    problems: list[str] = []
    if len(stages) != 19 or len({x["role"] for x in stages}) != 19:
        problems.append("V5 roles are not 19 unique entries")
    if draft.get("metadata", {}).get("status") != "DRAFT_NOT_APPROVED":
        problems.append("draft status is not protected")
    if draft.get("metadata", {}).get("write_to_production") is not False:
        problems.append("draft production writes not explicitly forbidden")
    if sum(x["role"].startswith("sms_") and x["status"] == "preserve_existing_v4" for x in stages) != 9:
        problems.append("SMS preservation differs from nine expected roles")
    by_role = {x["role"]: x for x in stages}
    d1 = by_role.get("whatsapp_d1", {}).get("message_edits", [{}])[0]
    if re.search(r"\{natal_countdown_[^{}]+\}", d1.get("title", "")):
        problems.append("D+1 active caption still relies on unproved runtime countdown")
    if d1.get("fallback_title") != d1.get("title"):
        problems.append("D+1 fallback differs from safe literal title")
    if by_role.get("document_d10", {}).get("status") != "blocked_do_not_publish":
        problems.append("Dangerous D+10 attachment not blocked for new publications")
    counts = parse_pending_table(report)
    if counts["pending_by_campaign_at_snapshot"] != {415: 8, 416: 4}:
        problems.append("D+10 recorded snapshot row counts changed; inspect report manually")
    if counts["skipped_total_at_snapshot"] != 1:
        problems.append("D+10 skipped row not separated from pending")
    historical_voice_delivery = bool(re.search(r"voice/smartvoice\s*\(delivered:\s*[1-9]", timeline))
    voice_denial = "zero registros de chamadas" in report.lower() or "nenhuma ligação foi efetuada" in report.lower()
    if historical_voice_delivery and voice_denial:
        problems.append("Voice timeline has delivered events while final report denies them")
    try:
        from render_canonical_v5 import render, TARGET
        if TARGET.read_text(encoding="utf-8") != render(draft):
            problems.append("Committed canonical V5 copy differs from editorial JSON")
    except (OSError, ValueError, KeyError, ImportError) as exc:
        problems.append("Canonical copy missing or unreadable: " + type(exc).__name__)
    captions = [
        {"role": stage["role"], "count": len(msg["title"])}
        for stage in stages
        for msg in stage.get("message_edits", [])
        if msg.get("type") in ("video", "image") and isinstance(msg.get("title"), str)
    ]
    for cap in captions:
        if cap["count"] > 200:
            problems.append(f"{cap['role']} exceeds CRM V4 200-char title bound")
    return {
        "offline_integrity": "PASS" if not problems else "FAIL",
        "production_release": "NO_GO",
        "live_p0_status": "UNKNOWN_MUST_REQUERY_AND_SUPPRESS_SAFELY",
        "historical_d10": counts,
        "historical_smartvoice_delivered_event_present": historical_voice_delivery,
        "historical_smartvoice_phone_call_answered": "NOT_PROVED",
        "media_captions_checked": len(captions),
        "historical_reports_as_copy": "FORBIDDEN_CANONICAL_IS_EDITORIAL_JSON",
        "problems": problems,
        "blocking_requirements": [
            "Authorized targeted suppression/cancel of live eligible D+10 attachment deliveries, with evidence",
            "Approved V5 production manifest + expected configuration hash + preview + rollback",
            "Consented test-recipient provider rendering and actual delivery receipts",
            "Effective SmartVoice registry/CDR reconciliation and cross-channel opt-out",
        ],
        "notice": "A historical snapshot NEVER clears the live P0; this tool never authorizes production.",
    }


def self_test() -> None:
    sample = """| 666 | 415 | pending | old | 8 | 2026-10-31 19:29:04 |
| 666 | 415 | skipped | old | 1 | 2026-11-04 16:09:05 |
| 685 | 416 | pending | old | 4 | 2026-11-03 09:32:06 |
"""
    values = parse_pending_table(sample)
    assert values["pending_by_campaign_at_snapshot"] == {415: 8, 416: 4}
    assert values["pending_total_at_snapshot"] == 12
    assert values["skipped_total_at_snapshot"] == 1
    assert not re.search(r"\{natal_countdown_[^{}]+\}", "O tempo voa e as fotos ficam")
    print("SELF-TEST OK: D+10 status split + fail-closed rendering rule.")


def main() -> int:
    p = argparse.ArgumentParser(description=__doc__)
    p.add_argument("--self-test", action="store_true")
    p.add_argument("--require-production-go", action="store_true", help="ALWAYS fail closed: live CRM evidence cannot be audited offline")
    args = p.parse_args()
    if args.self_test:
        self_test()
        return 0
    try:
        result = check()
    except (KeyError, ValueError, OSError) as exc:
        print("FORENSIC_GATE_ERROR: " + repr(exc), file=sys.stderr)
        return 3
    print(json.dumps(result, indent=2, ensure_ascii=False))
    if args.require_production_go:
        print("PRODUCTION_NO_GO: offline tests cannot authorize sends or deployments.", file=sys.stderr)
        return 2
    return 0 if result["offline_integrity"] == "PASS" else 1


if __name__ == "__main__":
    sys.exit(main())
