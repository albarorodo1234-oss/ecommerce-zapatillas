<?php

// app/Http/Controllers/ReporteStockController.php
// TP10 (Parte B): CONTROLADOR del módulo "Reporte de stock por marca".
// Recibe la petición que le asigna routes/web.php, le pide los datos a los
// modelos Eloquent y elige la vista Blade (o arma el JSON).
//   index() → GET /reportes/stock            (tabla de todas las marcas)
//   show()  → GET /reportes/stock/{marca}    (productos y talles de una marca)
//   api()   → GET /api/reportes/stock        (el mismo resumen en JSON)

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ReporteStockController extends Controller
{
    public function index(): View
    {
        $marcas = Marca::resumenDeStock();

        // view('reportes.stock.index') = resources/views/reportes/stock/index.blade.php
        return view('reportes.stock.index', [
            'marcas'          => $marcas,
            'total_productos' => $marcas->sum('productos_count'),
            'total_unidades'  => (int) $marcas->sum('unidades'),
        ]);
    }

    // "Route model binding": Laravel recibe el {marca} de la URL (un id),
    // busca esa Marca en la base y la entrega ya cargada. Si no existe,
    // responde solo con la página 404.
    public function show(Marca $marca): View
    {
        $productos = $marca->productos()
            ->where('activo', true)
            ->with(['talles' => fn ($consulta) => $consulta->orderBy('talle')])
            ->withSum('talles as unidades', 'stock')
            ->orderBy('nombre')
            ->get();

        return view('reportes.stock.show', [
            'marca'          => $marca,
            'productos'      => $productos,
            'total_unidades' => (int) $productos->sum('unidades'),
        ]);
    }

    public function api(): JsonResponse
    {
        $marcas = Marca::resumenDeStock()->map(fn (Marca $marca) => [
            'id'        => $marca->id,
            'marca'     => $marca->nombre,
            'productos' => $marca->productos_count,
            'unidades'  => (int) $marca->unidades,
        ]);

        return response()->json([
            'status' => 'success',
            'total'  => $marcas->count(),
            'data'   => $marcas,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}