#!/usr/bin/env python3
"""Construct a FULL Laravel schema-4 successor candidate from approved V4 + editorial V5.2.

Runs ONLY on the Antigravity workstation with a cloned evydencia-crm repository.
No DB, HTTP, CRM mutation, provider API, deploy, or messages. Output is a private
review artifact, NOT an automatic release.
"""
from __future__ import annotations

import argparse
import copy
import hashlib
import json
import os
import re
import stat
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
DRAFT_FILE = ROOT / "editorial" / "whatsapp-v5.draft.json"
CRM_MANIFEST = Path("docs/active/natal-2026-proposta/manifesto-natal-2026.v4.json")
ROLES = ("initial", "whatsapp_h1", "whatsapp_90min", "whatsapp_d2",
         "whatsapp_d4", "whatsapp_d1", "whatsapp_d3", "whatsapp_d5",
         "whatsapp_d10", "document_d10", "sms_h2", "sms_d1", "sms_d2",
         "sms_d3", "sms_d4", "sms_d5", "sms_d10", "sms_d15", "sms_d20")
SMS = tuple(role for role in ROLES if role.startswith("sms_"))
WHATSAPP = tuple(role for role in ROLES if role.startswith("whatsapp_")) + ("initial",)
HEX64 = re.compile(r"^[0-9a-f]{64}$")
REV = "natal2026-content-v52-20261027"
BAD_PDF = re.compile(r"(?i)(pix|comprovante|transferencia|recibo[_ -]*bancario)")


def fail(text: str) -> None:
    raise ValueError(text)


def sha(value: object) -> str:
    return hashlib.sha256(json.dumps(value, sort_keys=True, ensure_ascii=False,
                                      separators=(",", ":")).encode("utf-8")).hexdigest()


def validate_original(base: dict, draft: dict) -> None:
    if base.get("schema_version") != 4 or base.get("status") != "approved":
        fail("base_not_approved_schema_v4")
    if draft.get("metadata", {}).get("source_manifest_revision") != base.get("revision"):
        fail("editorial_base_revision_mismatch")
    if draft.get("metadata", {}).get("status") != "DRAFT_NOT_APPROVED":
        fail("unapproved_editorial_source_state")
    # Editorial JSON ordering differs from fixed Laravel order; identity is by role.
    if len(set(x.get("role") for x in draft.get("stage_edits", []))) != 19:
        fail("duplicated_editorial_roles")
    if len(draft["stage_edits"]) != 19 or set(x["role"] for x in draft["stage_edits"]) != set(ROLES):
        fail("incorrect_editorial_roles")
    for variant, campaign in (("with_name", 415), ("without_name", 416)):
        try:
            definition = base["variants"][variant]
        except KeyError:
            fail("missing_variant")
        if definition["campaign_id"] != campaign or tuple(s["role"] for s in definition["stages"]) != ROLES:
            fail("base_stage_identity_or_order_changed")
        if any(stage.get("id") is not None for stage in definition["stages"]):
            fail("base_contains_runtime_ids")
        for i, stage in enumerate(definition["stages"]):
            if i >= 10 and (stage["messages"] or len(stage["actions"]) != 1):
                fail("sms_transport_contract_mismatch")
            if i < 10 and (len(stage["actions"]) != (1 if i == 0 else 0)):
                fail("whatsapp_transport_contract_mismatch")


def candidate(base: dict, draft: dict, current_hash: str, approved: bool = False) -> tuple[dict, dict]:
    validate_original(base, draft)
    if not HEX64.fullmatch(current_hash):
        fail("approved_current_runtime_hash_required")
    if not isinstance(approved, bool):
        fail("invalid_approval_state")
    result = copy.deepcopy(base)
    result["revision"] = REV
    result["status"] = "approved" if approved else "draft"
    result["base_revision"] = base["revision"]
    result["base_configuration_hash"] = current_hash
    stage_edits = {stage["role"]: stage for stage in draft["stage_edits"]}
    touched = {}
    for variant in ("with_name", "without_name"):
        stages = result["variants"][variant]["stages"]
        diff = []
        for stage in stages:
            role = stage["role"]
            planned = stage_edits[role]
            if role in SMS:
                if planned["status"] != "preserve_existing_v4":
                    fail("sms_stage_should_not_be_edited")
                continue
            if role == "document_d10":
                if planned["status"] != "blocked_do_not_publish":
                    fail("d10_p0_guard_missing")
                # Schema 4 REQUIRES a document at D+10. Replace the misleading
                # Pix-style document by the already verified, legitimate T0 PDF.
                old = stage["messages"][0]
                welcome_pdf = stages[0]["messages"][2]
                if welcome_pdf["role"] != "proposta_pdf" or welcome_pdf["type"] != "attachment":
                    fail("welcome_pdf_contract_changed")
                if BAD_PDF.search(str(welcome_pdf["asset"]["basename"])) or not welcome_pdf["asset"]["sha256"]:
                    fail("safe_pdf_not_verified")
                old_hash = sha(old)
                old["title"] = welcome_pdf["title"]
                old["description"] = None
                old["message"] = None
                old["asset"] = copy.deepcopy(welcome_pdf["asset"])
                # D+10 is a single PDF send. title is Z-API filename, NOT a
                # caption; no extra body or description is allowed.
                if len(stage["messages"]) != 1 or not old["title"].lower().endswith(".pdf"):
                    fail("d10_attachment_must_be_standalone_pdf_with_filename")
                if old["message"] is not None or old["description"] is not None or old["buttons"] != []:
                    fail("d10_attachment_contains_extra_text_or_buttons")
                diff.append({"role": role, "message_role": old["role"], "fields": ["title", "asset"],
                             "before_sha256": old_hash, "after_sha256": sha(old),
                             "guard": "REPLACED_PIX_WITH_APPROVED_T0_PDF"})
                continue
            existing = {m["role"]: m for m in stage["messages"]}
            for edit in planned.get("message_edits", []):
                mr = edit["message_role"]
                msg = existing.get(mr)
                if msg is None or msg["type"] != edit["type"]:
                    fail(f"message_role_or_type_drift:{role}:{mr}")
                orig = sha(msg)
                if msg["type"] == "action":
                    if mr == "intro_cta":
                        key = "message_with_name" if variant == "with_name" else "message_without_name"
                        value = edit[key]
                    else:
                        value = edit["message"]
                    msg["message"] = value
                    if role == "initial" and msg["buttons"] != draft["stage_edits"][0]["buttons"]:
                        fail("initial_button_contract_mismatch")
                else:
                    if msg["type"] in ("video", "image", "attachment"):
                        value = edit["title"]
                        if edit.get("asset_basename") != msg["asset"]["basename"]:
                            fail(f"asset_filename_changed:{role}:{mr}")
                        if msg["type"] in ("video", "image") and len(value) > 200:
                            fail("media_caption_too_long")
                        msg["title"] = value
                    else:
                        fail("unsupported_transport_type")
                if orig != sha(msg):
                    diff.append({"role": role, "message_role": mr,
                                 "fields": ["message" if msg["type"] == "action" else "title"],
                                 "before_sha256": orig, "after_sha256": sha(msg)})
        touched[variant] = diff
    for variant in ("with_name", "without_name"):
        stages = result["variants"][variant]["stages"]
        d10 = stages[9]["messages"]
        if len(d10) != 1 or d10[0]["message"] is not None or d10[0]["description"] is not None:
            fail("d10_not_a_standalone_attachment")
        if result["variants"][variant]["stages"][9]["messages"][0]["asset"] != stages[0]["messages"][2]["asset"]:
            fail("unsafe_d10_asset")
        if BAD_PDF.search(stages[9]["messages"][0]["asset"]["basename"]):
            fail("pix_pdf_not_removed")
        if "{saudacao}" not in stages[0]["messages"][0]["message"]:
            fail("missing_dynamic_greeting")
        if any(t in stages[5]["messages"][0]["title"] for t in ("{natal_countdown_dias_descricao}", "{natal_countdown_semanas_descricao}")):
            fail("unapproved_dynamic_countdown")
        if "PARARNATAL2026" not in str(stages[1]["messages"][1]["buttons"]):
            fail("opt_out_button_removed")
        if not all(x in stages[4]["messages"][1]["message"] for x in ("10 motivos", "PARARNATAL2026")):
            fail("ten_reasons_missing")
    for i in range(10, 19):
        if result["variants"]["with_name"]["stages"][i]["actions"] != result["variants"]["without_name"]["stages"][i]["actions"]:
            # The canonical fixture might hold different integration accounts;
            # do not alter those IDs: require only payload equivalence.
            if result["variants"]["with_name"]["stages"][i]["actions"][0]["payload"] != (
                result["variants"]["without_name"]["stages"][i]["actions"][0]["payload"]
            ):
                fail("sms_payloads_not_identical")
    return result, {
        "status": "OFFLINE_REVIEW_ONLY", "revision": REV,
        "base_revision": base["revision"], "base_runtime_configuration_hash": current_hash,
        "crm_schema_version": 4, "manifest_sha256": sha(result),
        "preserved_sms_roles": list(SMS), "preserved_initial_email": True,
        "media_bytes_and_hashes_inherited": True, "new_record_ids_required": True,
        "d10_new_safe_pdf_reuses_t0": True,
        "d10_old_snapshots_not_fixed": True, "production_ready": False,
        "diff_by_variant": touched
    }


def self_test() -> None:
    draft = json.loads(DRAFT_FILE.read_text(encoding="utf-8-sig"))
    roles = {role: st for role, st in ((x["role"], x) for x in draft["stage_edits"])}
    base = {"schema_version": 4, "revision": draft["metadata"]["source_manifest_revision"],
            "status": "approved", "flow_slug": "natal",
            "variants": {}}
    for v, cid in (("with_name", 415), ("without_name", 416)):
        stages = []
        for role in ROLES:
            ed = roles[role]
            ms = []
            for n, x in enumerate(ed.get("message_edits", [])):
                t = x["type"]
                basename = x.get("asset_basename")
                if role == "document_d10":
                    basename = "Comprovante_de_Transferencia_Pix.pdf"
                ms.append({"role": x["message_role"], "type": t, "sort": (n+1)*10,
                           "message": "Old text" if t == "action" else None,
                           "title": "Old caption", "description": None, "buttons": (
                               ed["buttons"] if role == "initial" and t == "action"
                               else [{"id": "reply"},{"id": "PARARNATAL2026"}] if role == "whatsapp_h1" and t == "action"
                               else []),
                           "asset": None if t == "action" else {
                               "basename": basename, "sha256": "f"*64, "disk": "public",
                               "status": "approved", "bytes": 100, "mime": "application/pdf"}})
            if role == "document_d10" and not ms:
                ms.append({"role": role,"type":"attachment","sort":10,"message":None,
                           "title":"Pix","description":None,"buttons":[],
                           "asset":{"basename":"Comprovante_Pix.pdf", "sha256":"e"*64}})
            actions = [{"payload":{"text":"SMS"}}] if role in SMS else ([{"payload":{"text":"Mail"}}] if role=="initial" else [])
            stages.append({"id": None, "role": role, "messages": ms, "actions": actions})
        base["variants"][v] = {"campaign_id":cid,"stages":stages}
    for v in base["variants"].values():
        m = v["stages"][0]["messages"][2]
        m["asset"]["basename"] = roles["initial"]["message_edits"][2]["asset_basename"]
        m["asset"]["sha256"] = "a"*64
    manifest, report = candidate(base, draft, "b"*64)
    assert report["d10_new_safe_pdf_reuses_t0"]
    assert manifest["variants"]["with_name"]["stages"][0]["messages"][0]["message"].startswith("{saudacao}")
    assert manifest["variants"]["without_name"]["stages"][0]["messages"][0]["message"].startswith("{saudacao}")
    assert manifest["variants"]["with_name"]["stages"][9]["messages"][0]["asset"] == manifest["variants"]["with_name"]["stages"][0]["messages"][2]["asset"]
    assert manifest["variants"]["with_name"]["stages"][5]["messages"][0]["title"] == roles["whatsapp_d1"]["message_edits"][0]["title"]
    assert report["preserved_sms_roles"] == list(SMS)
    bad = copy.deepcopy(draft)
    bad["stage_edits"][0]["buttons"][0]["url"] = "https://example.invalid"
    try:
        candidate(base, bad, "b"*64)
    except ValueError:
        pass
    else:
        fail("test_invalid_button_must_fail")
    print("V52_RELEASE_BUILDER_SELF_TEST_OK; no production actions")


def main() -> int:
    p = argparse.ArgumentParser(description=__doc__)
    p.add_argument("--self-test", action="store_true")
    p.add_argument("--crm-root", type=Path, default=Path(os.environ.get("NATAL_CRM_ROOT", ".")))
    p.add_argument("--current-hash", help="Current approved V4 manifest.configuration_hash (from authorized read-only source)")
    p.add_argument("--output", type=Path, help="Private local output manifest path")
    p.add_argument("--approve", action="store_true", help="Mark generated copy approved after explicit human approval; does NOT publish")
    p.add_argument("--ack-commercial", action="store_true")
    p.add_argument("--ack-p0-contained", action="store_true")
    args = p.parse_args()
    try:
        if args.self_test:
            self_test()
            return 0
        if args.approve and not (args.ack_commercial and args.ack_p0_contained):
            fail("cannot_mark_approved_without_commercial_and_P0_attestations")
        if not args.output or not args.current_hash:
            fail("crm_root_current_hash_and_private_output_required")
        src = args.crm_root.resolve() / CRM_MANIFEST
        if not src.is_file():
            fail("crm_v4_manifest_missing")
        draft = json.loads(DRAFT_FILE.read_text(encoding="utf-8-sig"))
        base = json.loads(src.read_text(encoding="utf-8-sig"))
        result, report = candidate(base, draft, args.current_hash, args.approve)
        output = args.output.resolve()
        # Do not write to the public proposal repository or any Git working tree.
        if output == ROOT or ROOT in output.parents or args.crm_root.resolve() in output.parents:
            fail("refusing_output_inside_git_checkout")
        if output.exists():
            fail("refusing_overwrite_output")
        output.parent.mkdir(parents=True, exist_ok=True)
        flags = os.O_WRONLY | os.O_CREAT | os.O_EXCL
        fd = os.open(output, flags, 0o600)
        with os.fdopen(fd, "w", encoding="utf-8") as f:
            json.dump(result, f, ensure_ascii=False, indent=2)
            f.write("\n")
        report["manifest_path_local_only"] = str(output)
        print(json.dumps(report, ensure_ascii=False, indent=2))
        return 0
    except (ValueError, KeyError, OSError, TypeError, AssertionError) as exc:
        print("V52_RELEASE_BUILDER_BLOCKED: " + str(exc), file=sys.stderr)
        return 2


if __name__ == "__main__":
    raise SystemExit(main())
