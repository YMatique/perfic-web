<?php

use App\Console\Commands\ExecuteRecurringTransactions;
use App\Console\Commands\PerficSetupCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Registrar comandos personalizados
// Artisan::command('perfic:setup', PerficSetupCommand::class);
// Artisan::command('perfic:execute-recurring', ExecuteRecurringTransactions::class);

// Schedule para executar transações recorrentes automaticamente
// Schedule::command('perfic:execute-recurring')->dailyAt('09:00')->withoutOverlapping();

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
// Outros schedules úteis
// Schedule::command('queue:work --stop-when-empty')->everyMinute();