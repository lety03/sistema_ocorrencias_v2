<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\ConsultaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\IndicadorController;

// Rotas de Autenticação
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rotas Protegidas
Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        // Redireciona para consulta conforme nova regra
        return redirect()->route('consulta.index');
    });

    // Acesso para todos os logados (Admin, Visualizador)
    Route::get('/ocorrencias', [ConsultaController::class, 'index'])->name('consulta.index');
    Route::get('/ocorrencias/exportar', [ConsultaController::class, 'exportar'])->name('consulta.exportar');
    Route::get('/autocomplete', [ConsultaController::class, 'autocomplete'])->name('consulta.autocomplete');

    // Indicadores / Rankings
    Route::get('/indicadores', [IndicadorController::class, 'index'])->name('indicadores.index');
    Route::get('/indicadores/ranking', [IndicadorController::class, 'ranking'])->name('indicadores.ranking');

    // Acesso apenas para Admin (Upload)
    Route::middleware('role:admin')->group(function() {
        Route::get('/upload', function () {
            return view('upload');
        })->name('upload.index');
        Route::post('/upload', [UploadController::class, 'upload'])->name('upload.store');
    });

    // Acesso apenas para Admin (Gestão de Usuários e Logs)
    Route::middleware('role:admin')->group(function() {
        Route::resource('users', UserController::class);
        Route::get('/logs', [LogController::class, 'index'])->name('logs.index');
    });
});
