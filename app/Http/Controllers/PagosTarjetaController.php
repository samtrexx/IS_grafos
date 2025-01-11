<?php

namespace App\Http\Controllers;

use App\Grid\Grid;
use App\models\PagoTarjeta;
use App\Http\Controllers\Controller;
use App\Models\Ingresos;
use Illuminate\Http\Request;

class PagosTarjetaController extends Grid
{
    protected string $modelClass = PagoTarjeta::class;
    protected string $title = 'pagar tarjeta';
    protected string $resource = 'PagarTarjeta';
    protected string $page = 'PagarTarjeta';



}
