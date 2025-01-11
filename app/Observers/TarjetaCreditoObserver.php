<?php
/*
 * Se encarga de mover las tarjetas de credito a lo que es Adeudos
*/
namespace App\Observers;

use App\Models\TarjetaCredito;
use App\Models\TipoAdeudo; // Asegúrate de que el modelo de TipoAdeudo esté disponible
use Carbon\Carbon;

class TarjetaCreditoObserver
{
    /**
     * Handle the TarjetaCredito "created" event.
     *
     * @param  \App\Models\TarjetaCredito  $tarjetaCredito
     * @return void
     */
    public function created(TarjetaCredito $tarjetaCredito)
    {
        // Crear un nuevo registro en la tabla 'tipo_adeudos' cuando se crea una tarjeta de crédito
        TipoAdeudo::create([/*
            'nombre' => $tarjetaCredito->nombre, // Asigna el valor correspondiente
            'categoria' => 'Adeudo Tarjeta de Crédito', // Puedes personalizar esto
            'tarjeta_credito_id' => $tarjetaCredito->id,
            'fecha_corte' => Carbon::now(), // Asigna la fecha de corte actual
            'procesado' => false, // Valor predeterminado
*/
            'nombre' => $tarjetaCredito->nombre, // Nombre de la tarjeta
            'categoria' => $tarjetaCredito->tipo, // Categoría de la deuda
            'adeudo' => $tarjetaCredito->limite,
            'tarjeta_credito_id' => $tarjetaCredito->id, // Relación con la tarjeta
            'fecha_corte' => $tarjetaCredito->fecha_corte, // Fecha de corte de la tarjeta
            'procesado' => false, // Estado de la deuda
        ]);
    }
}

