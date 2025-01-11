<?php

namespace App\Http\Controllers;

use App\Grid\Grid;
use App\Models\PagoTarjeta;
use App\Models\TarjetaCredito;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TarjetCreditoController extends Grid
{
    protected string $modelClass = TarjetaCredito::class;
    protected string $title = 'Tarjetas';
    protected string $resource = 'tarjetas';
    protected string $page = 'TarjetaCredito';


    protected function mounted()
    {
        /* //Toolbar modificacion
         * $this->toolbar
            ->actions
            ->addAction(
                'Pagar',
                route('tarjetas.index'),
                'bi-plus'
            );*/
        $this->rows
            ->actions
            ->addAction(
                'Pagar',
                route('tarjeta-credito.pagar', [';id;']),
                'bi bi-credit-card'
            );
    }
    public function pagar($id, Request $request){

        $tarjeta = TarjetaCredito::find($id);

        if (!$tarjeta) {
            return redirect()->route('tarjetas.index')->withErrors('Tarjeta no encontrada');
        }

        return Inertia::render('PagarTarjeta', [
            'tarjeta_numero' => $tarjeta->no_tarjeta,
            'tarjeta_id' => $tarjeta->id,
            'url' => route('tarjetadecredito.procesar_pago', $tarjeta->id),
            'method' =>'POST',
            //'backurl' => route('tarjetas.index'),
        ]);
        //return Inertia::render('PagarTarjeta', []);
    }

    public  function procesapago($id,Request $request  ){
        PagoTarjeta::create([
            'tarjeta_id' => $id,
            'tarjeta_numero'=>$request->tarjeta_numero,
            'Monto'=>$request->Monto,
            'tipo_liquidacion'=>$request->tipo_liquidacion,
            'estado'=>$request->estado,
            'fecha_liquidacion'=>$request->fecha_liquidacion,

        ]) ;
        return response()->json(['url' => route('tarjetas.index')]);

    }

}
