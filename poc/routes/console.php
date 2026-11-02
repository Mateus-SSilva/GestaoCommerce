<?php

use App\Services\MonitorAgent;
use App\Services\RecurringTaskGenerator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('gestao:recorrentes', function (RecurringTaskGenerator $generator) {
    $this->info($generator->run().' tarefa(s) recorrente(s) criada(s).');
})->purpose('Cria os cartões das tarefas recorrentes de hoje');

Artisan::command('gestao:monitorar', function (MonitorAgent $agent) {
    $run = $agent->run();
    $this->info("Verificação concluída: {$run->new_issues} novo(s), {$run->updated_issues} persistente(s), {$run->resolved_issues} resolvido(s), {$run->cards_created} tarefa(s) criada(s).");
})->purpose('Executa o agente de monitoramento (simulado na POC)');

Schedule::command('gestao:recorrentes')->dailyAt('06:00');
Schedule::command('gestao:monitorar')->dailyAt('05:00');
