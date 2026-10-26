#!/usr/bin/env python3
"""Validate only the PUBLIC editorial V5 draft. No database, HTTP, CRM, or writes."""
from __future__ import annotations

import json
import re
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
SOURCE = ROOT / "editorial" / "whatsapp-v5.draft.json"

ROLES = [
    "initial", "whatsapp_h1", "whatsapp_90min", "whatsapp_d2",
    "whatsapp_d4", "whatsapp_d1", "whatsapp_d3", "whatsapp_d5",
    "whatsapp_d10", "document_d10", "sms_h2", "sms_d1",
    "sms_d2", "sms_d3", "sms_d4", "sms_d5",
    "sms_d10", "sms_d15", "sms_d20",
]
SMS = [role for role in ROLES if role.startswith("sms_")]
ALLOWED_TOKENS = {"primeiro_nome", "natal_countdown_dias_descricao"}
ALLOWED_URLS = {
    "https://reserva.fotosdenatal.com/",
    "https://horarios.fotosdenatal.com/",
    "https://instagram.com/estudioevydenciaa",
}
BANNED_PATTERNS = [
    r"(?:85|95)\s*%", r"restam somente\s+\d+\s+vagas",
    r"Comprovante_de_Transferencia", r"<break\s",
    r"\[(?:excited|laughs|whispers|mischievously|curious)\]",
    r"\bD\+\d+\b",
]
CAPTION_MAX = 500


def check() -> list[str]:
    errors: list[str] = []
    draft = json.loads(SOURCE.read_text(encoding="utf-8"))
    meta = draft.get("metadata") or {}
    if meta.get("status") != "DRAFT_NOT_APPROVED" or meta.get("write_to_production") is not False:
        errors.append("Draft must remain unapproved, write_to_production=false")
    if meta.get("campaign_ids") != [415, 416]:
        errors.append("Only campaigns 415/416 are permitted in this audit")
    records = draft.get("stage_edits")
    if not isinstance(records, list) or len(records) != 19:
        return errors + ["Exactly 19 role entries expected"]
    names = [r.get("role") for r in records]
    if len(names) != len(set(names)) or set(names) != set(ROLES):
        errors.append("Roles duplicate/missing/unknown relative to approved V4")
    preserved = [r for r in records if r.get("role", "").startswith("sms_")]
    if len(preserved) != 9 or any(r.get("status") != "preserve_existing_v4" for r in preserved):
        errors.append("Nine SMS roles must be preserved, not rewritten")
    by_role = {r["role"]: r for r in records}
    t0 = by_role.get("initial") or {}
    intros = [x for x in t0.get("message_edits", []) if x.get("message_role") == "intro_cta"]
    if len(intros) != 1:
        errors.append("Missing exact initial intro_cta")
    else:
        message = intros[0]
        named = message.get("message_with_name", "")
        anonymous = message.get("message_without_name", "")
        if "{primeiro_nome}" not in named or "{primeiro_nome}" in anonymous:
            errors.append("415 must have first_name; 416 must not")
        if "PARARNATAL2026" not in named or "PARARNATAL2026" not in anonymous:
            errors.append("Do not delete initial opt-out")
    buttons = t0.get("buttons", [])
    if len(buttons) != 3 or {b.get("url") for b in buttons} != ALLOWED_URLS:
        errors.append("Initial button set has changed; verify all three canonical URLs")
    d10 = by_role.get("document_d10") or {}
    if d10.get("status") != "blocked_do_not_publish":
        errors.append("D+10 suspicious PDF MUST remain blocked")
    for record in records:
        role = record["role"]
        for msg in record.get("message_edits", []):
            text_fields = ["title", "message", "message_with_name", "message_without_name"]
            if msg.get("type") in ("image", "video") and not msg.get("title"):
                errors.append(f"{role}: missing title/caption for media")
            if msg.get("type") in ("image", "video") and len(msg.get("title", "")) > CAPTION_MAX:
                errors.append(f"{role}: video/image caption too long")
            if msg.get("type") == "attachment" and msg.get("title") and ".pdf" not in msg["title"].lower():
                errors.append(f"{role}: attachment title must look like a filename")
            if record.get("status") == "blocked_do_not_publish":
                continue
            for field in text_fields:
                body = msg.get(field)
                if not isinstance(body, str):
                    continue
                tokens = set(re.findall(r"\{([a-z_]+)\}", body))
                if tokens - ALLOWED_TOKENS:
                    errors.append(f"{role}/{field}: unknown tokens {sorted(tokens - ALLOWED_TOKENS)}")
                if "```" in body or "<br" in body.lower():
                    errors.append(f"{role}/{field}: unsupported formatting")
                for pat in BANNED_PATTERNS:
                    if re.search(pat, body, re.IGNORECASE):
                        errors.append(f"{role}/{field}: banned pattern {pat}")
    d1 = by_role.get("whatsapp_d1") or {}
    if not any(m.get("fallback_title") for m in d1.get("message_edits", [])):
        errors.append("Countdown needs an explicit fallback without variable")
    return errors


if __name__ == "__main__":
    issues = check()
    if issues:
        print("EDITORIAL_CHECK_FAILED")
        for issue in issues:
            print(" -", issue)
        sys.exit(1)
    print("EDITORIAL_CHECK_OK: 19 roles, 9 SMS preserved, opt-out, buttons, dynamic countdown gate, D+10 block")
