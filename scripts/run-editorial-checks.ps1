# Read-only editorial checks: no SSH, MySQL, CRM or Z-API.
$ErrorActionPreference = "Stop"
Push-Location (Split-Path $PSScriptRoot -Parent)
try {
    $commands = @(
        @("python", "scripts/validate_editorial.py"),
        @("python", "scripts/reconcile_v4_local.py", "--self-test"),
        @("python", "scripts/inspect_local_media.py", "--self-test"),
        @("python", "scripts/preview_whatsapp_v5.py", "--role", "initial")
    )
    foreach ($cmd in $commands) {
        Write-Host ("RUN " + ($cmd -join " ")) -ForegroundColor Cyan
        $executable = $cmd[0]
        $arguments = $cmd[1..($cmd.Length - 1)]
        & $executable @arguments
        if ($LASTEXITCODE -ne 0) { throw "Check failed with exit $LASTEXITCODE" }
    }
    Write-Host "EDITORIAL CHECKS OK — no production actions" -ForegroundColor Green
}
finally {
    Pop-Location
}
