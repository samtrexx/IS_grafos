<?php


use App\Http\Controllers\TarjetCreditoController;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::resource('tarjetas', TarjetCreditoController::class);
    

    
    Route::get('/tarjeta-credito/pagar/{id}', [TarjetCreditoController::class, 'pagar'])
        ->name('tarjeta-credito.pagar');
    Route::post('/tarjetas-credito/procesarpago/{id}', [TarjetCreditoController::class, 'procesapago'])->name('tarjetadecredito.procesar_pago');
    Route::get('/tarjeta-credito/pagar/{id}', [TarjetCreditoController::class, 'pagar'])->name('tarjeta-credito.pagar');

});

