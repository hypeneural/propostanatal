$ErrorActionPreference = 'Stop'
Set-Location (Resolve-Path (Join-Path $PSScriptRoot '..'))
if (-not (Get-Command php -ErrorAction SilentlyContinue)) {
    $env:Path = [System.Environment]::GetEnvironmentVariable("Path","Machine") + ";" + [System.Environment]::GetEnvironmentVariable("Path","User")
}
$php = Get-Command php -ErrorAction Stop
Write-Host 'Auditoria somente leitura — Natal 2026'
& $php.Source 'scripts/audit.php' '--self-test'
if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }
& $php.Source 'scripts/audit.php'
exit $LASTEXITCODE
