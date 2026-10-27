# Preparação LOCAL V5.2: sem deploy, DB write, Z-API, SMS ou voz.
# Exemplo: powershell -NoProfile -ExecutionPolicy Bypass -File scripts/prepare-v52-local.ps1 -CrmRoot C:\laragon\www\evydencia\crm -ValidateCRM
# Gerar candidato: acrescentar -CurrentHash <64hex> -ManifestOutput <arquivo_no_TEMP>.
# O manifesto padrão continua DRAFT e não pode ser aplicado pelo provisionador.
param(
    [Parameter(Mandatory=$true)][string]$CrmRoot,
    [string]$CurrentHash,
    [string]$ManifestOutput,
    [switch]$ValidateCRM,
    [switch]$CommercialApproved,
    [switch]$P0Contained
)
$ErrorActionPreference = "Stop"
[Console]::OutputEncoding = [System.Text.Encoding]::UTF8
$Repo = (Resolve-Path (Join-Path $PSScriptRoot "..")).Path
$CrmRoot = (Resolve-Path $CrmRoot).Path

function Invoke-Check {
    param([string]$Display, [string]$Cmd, [string[]]$Arguments)
    Write-Host "CHECK: $Display"
    & $Cmd @Arguments
    if ($LASTEXITCODE -ne 0) { throw ("BLOCKED " + $Display + ": exit " + $LASTEXITCODE) }
}

Push-Location $Repo
try {
    Invoke-Check "canonical editorial" "python" @("scripts/validate_editorial.py")
    Invoke-Check "markdown == JSON" "python" @("scripts/render_canonical_v5.py", "--check")
    Invoke-Check "non-executable manager diff" "python" @("scripts/plan_manager_v5.py", "--check")
    Invoke-Check "V5.2 successor generator synthetic self-test" "python" @("scripts/build_v52_release_candidate.py", "--self-test")
    Invoke-Check "forensic gates: known production NO-GO" "python" @("scripts/check_forensic_gates.py")

    if ($ValidateCRM) {
        $needed = @(
            "app/Services/Proposals/Natal2026ProposalCampaignProvisioner.php",
            "app/Services/Proposals/NatalProposalContentProfile.php",
            "app/Services/Proposals/NatalUnsafeD10DocumentGuard.php",
            "app/Services/Messages/CampaignMessageService.php"
        )
        foreach($f in $needed) {
            Invoke-Check ("PHP lint " + $f) "php" @("-l", (Join-Path $CrmRoot $f))
        }
        $env:NATAL_CRM_ROOT = $CrmRoot
        Invoke-Check "Laravel greeting and countdown parser (synthetic)" "php" @("scripts/test_natal_runtime_variables.php")
        Push-Location $CrmRoot
        try {
            $testFiles = @(
                "tests/Unit/Services/Proposals/NatalProposalContentSuccessorTest.php",
                "tests/Unit/Services/Proposals/NatalUnsafeD10DocumentGuardTest.php",
                "tests/Unit/Services/Proposals/Natal2026ProposalManifestTest.php",
                "tests/Feature/Proposals/NatalProposalContentProvisionerTest.php",
                "tests/Feature/Proposals/NatalProposalContentExecutionTest.php"
            )
            foreach($t in $testFiles) {
                Invoke-Check ("PHPUnit " + $t) "php" (@("vendor/bin/phpunit") + @($t))
            }
        } finally { Pop-Location }
    } else {
        Write-Host "CRM_PHP_AND_MYSQL_FOCUSED_TESTS_SKIPPED: use -ValidateCRM after installing branch #435 and isolated synthetic DB."
    }

    if ($CommercialApproved -and -not $P0Contained) {
        throw "BLOCKED: P0 D+10 old frozen runs/outbox NOT certified contained"
    }
    if ($P0Contained -and -not $CommercialApproved) {
        throw "BLOCKED: explicit commercial approval still missing"
    }

    if ($ManifestOutput -or $CurrentHash) {
        if (-not $CurrentHash -or -not $ManifestOutput) {
            throw "BLOCKED: -CurrentHash and -ManifestOutput are BOTH required"
        }
        $args = @(
            "scripts/build_v52_release_candidate.py",
            "--crm-root", $CrmRoot,
            "--current-hash", $CurrentHash,
            "--output", $ManifestOutput
        )
        if ($CommercialApproved -and $P0Contained) {
            $args += @("--approve", "--ack-commercial", "--ack-p0-contained")
        }
        Invoke-Check "produce private schema-4 V5.2 candidate" "python" $args
    } else {
        Write-Host "MANIFEST_NOT_GENERATED: obtain approved V4 hash using authorized LIVE SELECT first."
    }
    Write-Host "V52_OFFLINE_PREPARATION_OK; NO DEPLOY, APPLY, PROVIDER SEND OR PRODUCTION WRITES."
} finally {
    Pop-Location
}
