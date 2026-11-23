<?php

// routes/web.php
// Rutas web del proyecto. Cada línea une una URL y un método HTTP con el
// método de un controlador (sistema de enrutamiento de Laravel).

use App\Http\Controllers\ReporteStockController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// TP10 (Parte B): módulo "Reporte de stock por marca" (solo lectura).
Route::get('/reportes/stock', [ReporteStockController::class, 'index'])
    ->name('reportes.stock.index');

// whereNumber('marca'): solo acepta números; /reportes/stock/abc da 404.
Route::get('/reportes/stock/{marca}', [ReporteStockController::class, 'show'])
    ->whereNumber('marca')
    ->name('reportes.stock.show');

Route::get('/api/reportes/stock', [ReporteStockController::class, 'api'])
    ->name('api.reportes.stock');