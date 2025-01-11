<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Registro manual de comandos
Artisan::command('adeudos:mover', function () {
    $this->call(\App\Console\Commands\MoverAdeudosVencidos::class);
})->purpose('Mover adeudos vencidos a la tabla de deudas');
