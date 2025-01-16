<?php


use App\Http\Controllers\TarjetCreditoController;
use App\Http\Controllers\SocialAuthController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

//modificado por  JSM
use App\Http\Controllers\GrafoController;

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::get('/dashboard', [GrafoController::class, 'index'])->name('dashboard');
    Route::get('/generar-grafo', [GrafoController::class, 'create'])->name('generar-grafo');
    Route::post('/generar-grafo', [GrafoController::class, 'store'])->name('guardar-grafo');
});
//fin de modificaicón de JSM

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});
// Rutas para autenticación social (ejemplo: Google)
Route::get('/auth/google/redirect', [SocialAuthController::class, 'redirectToGoogle'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
// Rutas protegidas por autenticación
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Rutas para la funcionalidad de tarjetas de crédito
    Route::resource('tarjetas', TarjetCreditoController::class);

    Route::get('/tarjeta-credito/pagar/{id}', [TarjetCreditoController::class, 'pagar'])->name('tarjeta-credito.pagar');
    Route::post('/tarjetas-credito/procesarpago/{id}', [TarjetCreditoController::class, 'procesapago'])->name('tarjetadecredito.procesar_pago');
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
    Route::resource('grafo', GrafoController::class);



    Route::get('/tarjeta-credito/pagar/{id}', [TarjetCreditoController::class, 'pagar'])
        ->name('tarjeta-credito.pagar');
    Route::post('/tarjetas-credito/procesarpago/{id}', [TarjetCreditoController::class, 'procesapago'])->name('tarjetadecredito.procesar_pago');
    Route::get('/tarjeta-credito/pagar/{id}', [TarjetCreditoController::class, 'pagar'])->name('tarjeta-credito.pagar');

});

