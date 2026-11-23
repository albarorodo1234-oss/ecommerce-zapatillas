{{-- resources/views/reportes/stock/index.blade.php --}}
{{-- TP10 (Parte B): VISTA del reporte. Recibe $marcas, $total_productos y --}}
{{-- $total_unidades desde ReporteStockController@index.                    --}}
@extends('layouts.app')

@section('titulo', 'Stock por marca')

@section('contenido')
  <h1 class="h3 mb-1">Reporte de stock por marca</h1>
  <p class="text-muted">
    Productos activos y unidades disponibles de cada marca, leídos de <code>solemate_db</code> con Eloquent.
  </p>

  <div class="table-responsive">
    <table class="table table-striped table-hover align-middle bg-white shadow-sm">
      <thead class="table-dark">
        <tr>
          <th scope="col">Marca</th>
          <th scope="col" class="text-end">Productos activos</th>
          <th scope="col" class="text-end">Unidades en stock</th>
          <th scope="col"><span class="visually-hidden">Acciones</span></th>
        </tr>
      </thead>
      <tbody>
        @forelse ($marcas as $marca)
          <tr>
            <td>
              {{ $marca->nombre }}
              @if ((int) $marca->unidades === 0)
                <span class="badge text-bg-danger ms-1">Sin stock</span>
              @endif
            </td>
            <td class="text-end">{{ $marca->productos_count }}</td>
            <td class="text-end">{{ (int) $marca->unidades }}</td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-dark" href="{{ route('reportes.stock.show', $marca) }}">Ver detalle</a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="4" class="text-center text-muted">No hay marcas cargadas.</td>
          </tr>
        @endforelse
      </tbody>
      <tfoot>
        <tr class="fw-bold">
          <td>Total</td>
          <td class="text-end">{{ $total_productos }}</td>
          <td class="text-end">{{ $total_unidades }}</td>
          <td></td>
        </tr>
      </tfoot>
    </table>
  </div>

  <p class="small">
    El mismo reporte en JSON: <a href="{{ route('api.reportes.stock') }}">/api/reportes/stock</a>
  </p>
@endsection