<?php
/**
 * OFFLINE Laravel render smoke test for proposed Natal V5.2 variables.
 *
 * Usage (PowerShell, from propostanatal):
 *   $env:NATAL_CRM_ROOT = 'C:\Projetos\hypeneural\evydencia-crm'
 *   php scripts/test_natal_runtime_variables.php
 *
 * NO HTTP, no message send, no DB query, no mutation.
 * Only operates on NEW transient Message objects.
 * A pass is NOT a replacement for the real stage snapshot and dispatch canary.
 */
declare(strict_types=1);

$root = getenv('NATAL_CRM_ROOT');
if (! is_string($root) || $root === '') {
    fwrite(STDERR, "NATAL_CRM_ROOT required; point to local CRM checkout.\n");
    exit(3);
}
$root = rtrim($root, "\\/");
if (! is_file($root . '/vendor/autoload.php') || ! is_file($root . '/bootstrap/app.php')) {
    fwrite(STDERR, "CRM project must have vendor/autoload.php and bootstrap/app.php.\n");
    exit(3);
}

require $root . '/vendor/autoload.php';
$app = require $root . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

date_default_timezone_set('America/Sao_Paulo');
$tz = (string) config('app.timezone');
if ($tz !== 'America/Sao_Paulo') {
    fwrite(STDERR, "BLOCKED: app.timezone is {$tz}, expected America/Sao_Paulo.\n");
    exit(2);
}

use App\Models\Message;
use App\Services\MessageVariableService;
use Illuminate\Support\Carbon;

$cases = [
    ['2026-10-26 08:00:00', 'Bom dia'],
    ['2026-10-26 15:00:00', 'Boa tarde'],
    ['2026-10-26 21:00:00', 'Boa noite'],
    ['2026-12-24 15:00:00', 'Boa tarde'],
    ['2026-12-25 10:00:00', 'Bom dia'],
];

try {
    foreach ($cases as [$instant, $greeting]) {
        Carbon::setTestNow(Carbon::parse($instant, $tz));
        $with = new Message();
        $with->message = '{saudacao}, *{primeiro_nome}*! 🎄';
        $with->title = '*{natal_countdown_dias_descricao}*';
        $a = (new MessageVariableService())->setContact('Elaine Exemplo', '5500000000000')->parseMessage($with);

        $without = new Message();
        $without->message = '{saudacao}! 🎄';
        $without->title = '*{natal_countdown_semanas_descricao}*';
        $b = (new MessageVariableService())->setContact(null, '5500000000000')->parseMessage($without);

        if (!str_starts_with($a->message, "{$greeting}, *Elaine*!") ||
            !str_starts_with($b->message, "{$greeting}!") ||
            str_contains($a->message . $a->title . $b->message . $b->title, '{')) {
            throw new RuntimeException("A variable was not resolved correctly at {$instant}");
        }

        if ($instant === '2026-12-25 10:00:00' && !str_contains($a->title, 'Hoje é Natal')) {
            throw new RuntimeException('Christmas-day boundary not rendered as expected.');
        }
        fwrite(STDOUT, "PASS " . $instant . " {$greeting}; Christmas countdown resolved.\n");
    }
    fwrite(STDOUT, "ALL_OFFLINE_VARIABLE_RENDER_CASES_PASSED; LIVE_CRM_DISPATCH_UNTESTED\n");
    exit(0);
} catch (Throwable $e) {
    fwrite(STDERR, "VARIABLE_TEST_FAILED: " . $e->getMessage() . "\n");
    exit(2);
} finally {
    Carbon::setTestNow();
}
