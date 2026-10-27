# ANTIGRAVITY: SOMENTE o repositorio propostanatal; NUNCA operar evydencia-crm.
# Uso: powershell -NoProfile -ExecutionPolicy Bypass -File scripts/prepare-v52-local.ps1
# Escopo: copy, video captions, botoes, editorial JSON, provas OFFLINE.
# Geracao de manifesto executavel, hash LIVE, MySQL, Laravel e deploy: CODEX/CHATGPT.
$ErrorActionPreference = "Stop"
[Console]::OutputEncoding = [System.Text.Encoding]::UTF8
$Repo = (Resolve-Path (Join-Path $PSScriptRoot "..")).Path
function Invoke-Check {
    param([string]$Display, [string[]]$Arguments)
    Write-Host "CHECK: $Display"
    & python @Arguments
    if ($LASTEXITCODE -ne 0) {
        throw "BLOCKED: $Display (exit $LASTEXITCODE)"
    }
}
Push-Location $Repo
try {
    Invoke-Check "Editorial strict JSON and contracts" @("scripts/validate_editorial.py")
    Invoke-Check "Canonical Markdown equals editorial JSON" @("scripts/render_canonical_v5.py", "--check")
    Invoke-Check "Manager diff: REVIEW ONLY, NOT an import" @("scripts/plan_manager_v5.py", "--check")
    Invoke-Check "History and D+10 risk gates: OFFLINE" @("scripts/check_forensic_gates.py")
    Invoke-Check "V5.2 complete manifesto builder: synthetic self-test only" @("scripts/build_v52_release_candidate.py", "--self-test")
    Invoke-Check "MP4 audit utility: synthetic self-test only" @("scripts/inspect_local_media.py", "--self-test")
    Invoke-Check "Asset inventory utility: synthetic self-test only" @("scripts/review_v4_assets.py", "--self-test")
    Write-Host "ANTIGRAVITY_PROPOSTANATAL_EDITORIAL_OK -- no CRM access, DB, SMS, provider messages, release or deploy."
    Write-Host "Next owner: CODEX/ChatGPT in evydencia-crm Issue #434. D+10: standalone legitimate attachment only."
} finally {
    Pop-Location
}
