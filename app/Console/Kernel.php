<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define las tareas programadas.
     */
    protected function schedule(Schedule $schedule)
    {
        // Programar el comando para que se ejecute diariamente
        $schedule->command('verificar:fechas-corte')->dailyAt('00:00');
        //$schedule->command('verificar:fechas-corte')->everyMinute();

        /*php C:\path_del_proyecto\artisan schedule:run  */

    }

    /**
     * Registra los comandos para la consola de Artisan.
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}


/*
  En el caso de linux: crontab -e
Para ejecutar el comando verificar:fechas-corte de Laravel automáticamente, agrega una línea al archivo crontab con el siguiente formato:

0 0 * * * /usr/bin/php /ruta/a/tu/proyecto/artisan verificar:fechas-corte >> /ruta/a/tu/proyecto/storage/logs/cron.log 2>&1

 /usr/bin/php: Ruta al ejecutable de PHP. Verifica tu versión con: which php

 */
