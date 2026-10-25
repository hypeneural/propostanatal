$ErrorActionPreference = 'Stop'
Set-Location (Resolve-Path (Join-Path $PSScriptRoot '..'))
$php = Get-Command php -ErrorAction Stop
Write-Host 'Auditoria somente leitura — Natal 2026'
& $php.Source 'scripts/audit.php' '--self-test'
if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }
& $php.Source 'scripts/audit.php'
exit $LASTEXITCODE
