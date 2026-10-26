#!/usr/bin/env python3
"""Print plain-text PREVIEW of the V5 WhatsApp draft; never sends messages.

All previews are local. Token replacements are sample placeholders and DO NOT
prove real CRM rendering. No network, WhatsApp API, database or output files.
"""
from __future__ import annotations

import argparse
import json
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
DRAFT = ROOT / "editorial" / "whatsapp-v5.draft.json"
CLOCK_EXAMPLE = "[CONTAGEM DE EXEMPLO - NÃO VALIDADA]"


def render(value: str, name: str) -> str:
    return value.replace("{primeiro_nome}", name).replace(
        "{natal_countdown_dias_descricao}", CLOCK_EXAMPLE
    )


def previews(data: dict, name: str, chosen_role: str | None) -> list[str]:
    result = []
    for stage in data["stage_edits"]:
        role = stage["role"]
        if chosen_role and role != chosen_role:
            continue
        if role.startswith("sms_"):
            continue
        result.append("=" * 57)
        result.append(f"ROLE: {role} | ESTADO: {stage['status']} | SEM ENVIO")
        if role == "document_d10":
            result.append("P0: ANEXO BLOQUEADO. NÃO PUBLICAR NEM ENVIAR.")
            continue
        for m in stage.get("message_edits", []):
            typ, label = m.get("type"), m.get("message_role")
            if typ == "action":
                for variant, key in (("415 com nome", "message_with_name"), ("416 sem nome", "message_without_name")):
                    value = m.get(key)
                    if isinstance(value, str):
                        result.extend([f"{label} | {variant} | ACTION.BODY", render(value, name), ""])
                if isinstance(m.get("message"), str):
                    result.extend([f"{label} | ACTION.BODY", render(m["message"], name), ""])
            elif typ in ("image", "video"):
                value = m.get("title", "")
                result.extend([f"{label} | {typ.upper()} CAPTION | {len(value)} caracteres", render(value, name), ""])
                if isinstance(m.get("fallback_title"), str):
                    result.extend(["ALTERNATIVA EDITORIAL NÃO AUTOMÁTICA:", m["fallback_title"], ""])
            elif typ == "attachment":
                result.extend([f"{label} | PDF FILE NAME (NÃO É CAPTION)", m.get("title", "[não configurado]"), ""])
        if role == "initial":
            for b in stage.get("buttons", []):
                result.append(f"[BOTÃO {b['id']}] {b['label']} → {b['url']}")
            result.append("NOTA: V4 só permite destinos previstos no manifesto aprovado.")
    return result


def main() -> None:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--role", help="initial, whatsapp_h1, whatsapp_d3, etc. Omit to preview all WhatsApp roles")
    parser.add_argument("--example-name", default="Maria")
    opts = parser.parse_args()
    data = json.loads(DRAFT.read_text(encoding="utf-8-sig"))
    if opts.role and opts.role not in {r["role"] for r in data["stage_edits"]}:
        parser.error("Unknown role; consult editorial/whatsapp-v5.draft.json")
    print("SIMULAÇÃO DE COPY: NÃO é payload real do provedor. NENHUMA MENSAGEM SERÁ ENVIADA.")
    print("Aviso: nome e countdown são dados de exemplo; variável dinâmica deve ser comprovada no CRM.")
    print("\n".join(previews(data, opts.example_name, opts.role)))


if __name__ == "__main__":
    main()
