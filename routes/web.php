<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\IconController;
use App\Http\Controllers\ClasificacionController;
use App\Http\Controllers\SubClasificacionController;
use App\Http\Controllers\RazonSocialController;
use App\Http\Controllers\EquipoController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BackupController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;



Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
// Ruta principal que redirige al dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
})->middleware('auth');

// Rutas accesibles para usuarios no autenticados
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
});

// Rutas accesibles solo para usuarios autenticados
Route::middleware(['auth'])->group(function () {
    // Ruta para cerrar sesión
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware([RoleMiddleware::class . ':10004,10005,10006'])->group(function () {
        Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
    });

    // Ruta de API para obtener íconos
    Route::get('/api/icons', [IconController::class, 'getIcons']);

    // Rutas para modificar el Calendario
    Route::get('/api/tickets', [TicketController::class, 'getTickets']);
    Route::post('/tickets/update-event', [TicketController::class, 'updateEvent'])->name('tickets.updateEvent');

    // Ruta para obtener datos en Tickets
    Route::get('/get-equipos/{idCliente}', [TicketController::class, 'getEquiposPorCliente'])->name('tickets.equiposPorCliente');
    Route::get('/clientes/get', [TicketController::class, 'getClient'])->name('clientes.get');

    // Ruta para obtener datos en Equipos
    Route::get('/dueño/get', [EquipoController::class, 'getClient'])->name('dueño.get');

    // Ruta para calcular el Dolar en Eventos
    Route::get('/convert-ves-to-usd', [EventoController::class, 'convertVEStoUSD'])->name('eventos.convertVEStoUSD');

    // Rutas para Administrador (rol: 10004)
    Route::middleware([RoleMiddleware::class . ':10004'])->group(function () {
        Route::resource('clasificaciones', ClasificacionController::class);
        Route::resource('clasificaciones/{parentId}/subclasificaciones', SubClasificacionController::class)->except(['show']);

        // Rutas para el Backup
        Route::prefix('backups')->group(function () {
            Route::get('/', [BackupController::class, 'index'])->name('backups.index');
            Route::post('/create', [BackupController::class, 'create'])->name('backups.create');
            Route::post('/restore', [BackupController::class, 'restore'])->name('backups.restore');
            Route::get('/download/{file}', [BackupController::class, 'download'])->name('backups.download');
            Route::delete('/delete/{file}', [BackupController::class, 'delete'])->name('backups.delete');
        });
    });

    // Rutas para Administrador y Operador (roles: 10004, 10006)
    Route::middleware([RoleMiddleware::class . ':10004,10006'])->group(function () {
        Route::resource('razon_social', RazonSocialController::class);
        Route::resource('equipos', EquipoController::class);
    });

    // Rutas para Administrador, Operador, Cliente y Técnico (roles: 10004, 10005, 10006, 10007)
    Route::middleware([RoleMiddleware::class . ':10004,10005,10006,10007'])->group(function () {
        Route::resource('tickets', TicketController::class);
        Route::resource('eventos', EventoController::class);
    });
});
