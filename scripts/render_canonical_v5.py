#!/usr/bin/env python3
"""Render or verify the ONE canonical V5 copy from editorial/whatsapp-v5.draft.json.

READ ONLY by default; --write updates ONLY the local Markdown candidate file.
No network, no CRM, no side effects on production. Historical reports are never inputs.
"""
from __future__ import annotations

import argparse
import json
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
SOURCE = ROOT / "editorial" / "whatsapp-v5.draft.json"
TARGET = ROOT / "docs" / "EDITORIAL-V5-20261026" / "14-COPY-CANONICA-V5-CANDIDATA.md"
FIELDS = (
    ("message_with_name", "Campanha 415 — com nome"),
    ("message_without_name", "Campanha 416 — sem nome"),
    ("message", "action.message"),
    ("title", "title (caption para imagem/video; fileName para documento)"),
)


def render(draft: dict) -> str:
    meta = draft["metadata"]
    if meta["status"] != "DRAFT_NOT_APPROVED":
        raise ValueError("refusing to render a non-draft configuration")
    lines = [
        "# 14 — Copy canônica V5 para aprovação (GERADA; NÃO PUBLICADA)",
        "",
        "> SOMENTE EDITORIAL / SEM ENVIO. Fonte única: editorial/whatsapp-v5.draft.json. "
        "Relatórios de relatorios/ são evidências históricas, NÃO copy executável. "
        "Documento gerado por scripts/render_canonical_v5.py. "
        "Nunca colar todo este documento no gestor.",
        "",
        f"Revisão editorial: {meta['revision']} · Campanhas: 415 e 416.",
        "",
        "As legendas de imagem/vídeo e os nomes de documentos só podem "
        "ser aplicados após revalidação do manifesto/hash, snapshots e aprovação.",
        "",
    ]
    for stage in draft["stage_edits"]:
        role, status = stage["role"], stage["status"]
        lines.extend([f"## {role} — {status}", ""])
        if role.startswith("sms_"):
            lines.extend(["SMS V4 preservado; nenhuma alteração editorial nesta rodada.", ""])
            continue
        if role == "document_d10":
            lines.extend([
                "**P0: ANEXO D+10 BLOQUEADO PARA REPUBLICAÇÃO.** "
                "Esta anotação NÃO cancela os runs pendentes de produção.",
                "",
            ])
            continue
        for message in stage.get("message_edits", []):
            lines.extend([f"### {message['message_role']} — {message['type']}", ""])
            for key, label in FIELDS:
                value = message.get(key)
                if not isinstance(value, str):
                    continue
                lines.extend([f"**{label}** ({len(value)} caracteres Unicode)", "", "~~~text", value, "~~~", ""])
        if role == "initial":
            lines.extend(["### Botões T0 — conservar IDs/URLs exatos do V4", ""])
            for button in stage["buttons"]:
                lines.append(f"- {button['id']} · {button['label']} · {button['url']}")
            lines.append("")
    lines.extend([
        "## Requisitos fora da copy",
        "",
        "- **D+1 estático:** título sem token de countdown em runtime. "
        "Qualquer retorno do token requer prova de renderização e nova aprovação.",
        "- **D+10:** confirmar/corrigir o estado dos runs pendentes e cancelar/suprimir "
        "via operação segura autorizada, mantendo recibos e auditoria.",
        "- **Outros canais:** manter SMS, Resend e SmartVoice inalterados, "
        "mas não considerar preserve_existing_v4 equivalente a entrega homologada.",
        "- **Status:** GO apenas para revisão offline; produção NO-GO.",
        "",
    ])
    return "\n".join(lines)


def main() -> int:
    p = argparse.ArgumentParser(description=__doc__)
    mode = p.add_mutually_exclusive_group()
    mode.add_argument("--write", action="store_true", help="write local candidate markdown only")
    mode.add_argument("--check", action="store_true", help="assert committed candidate matches JSON exactly")
    args = p.parse_args()
    try:
        source = json.loads(SOURCE.read_text(encoding="utf-8-sig"))
        value = render(source)
    except (OSError, ValueError, KeyError) as exc:
        print(f"CANONICAL_RENDER_FAILED: {exc}", file=sys.stderr)
        return 3
    if args.write:
        TARGET.parent.mkdir(parents=True, exist_ok=True)
        TARGET.write_text(value, encoding="utf-8")
        print(f"CANONICAL_LOCAL_WRITE: {TARGET}")
        return 0
    if args.check:
        if not TARGET.is_file() or TARGET.read_text(encoding="utf-8") != value:
            print("CANONICAL_DRIFT: regenerate locally with --write; review diff before commit.", file=sys.stderr)
            return 2
        print("CANONICAL_COPY_OK: generated MD equals current editorial JSON; no production action.")
        return 0
    print(value, end="")
    return 0


if __name__ == "__main__":
    sys.exit(main())
