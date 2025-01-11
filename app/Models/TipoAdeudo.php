<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoAdeudo extends Model
{
    protected $guarded = ['id'];

    // Definir la relación con la tabla tarjeta_creditos
    public function tarjetaCredito()
    {
        return $this->belongsTo(TarjetaCredito::class, 'tarjeta_credito_id');
    }
}
