{{-- resources/views/reportes/stock/show.blade.php --}}
{{-- TP10 (Parte B): VISTA del detalle de una marca. Recibe $marca, $productos --}}
{{-- (con sus talles) y $total_unidades desde ReporteStockController@show.     --}}
@extends('layouts.app')

@section('titulo', 'Stock de ' . $marca->nombre)

@section('contenido')
  <nav aria-label="Ruta de navegación">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('reportes.stock.index') }}">Stock por marca</a></li>
      <li class="breadcrumb-item active" aria-current="page">{{ $marca->nombre }}</li>
    </ol>
  </nav>

  <h1 class="h3 mb-1">Stock de {{ $marca->nombre }}</h1>
  <p class="text-muted">
    {{ $productos->count() }} {{ $productos->count() === 1 ? 'producto activo' : 'productos activos' }}
    · {{ $total_unidades }} unidades en stock
  </p>

  @if ($productos->isEmpty())
    <div class="alert alert-info">Esta marca no tiene productos activos.</div>
  @else
    <div class="table-responsive">
      <table class="table align-middle bg-white shadow-sm">
        <thead class="table-dark">
          <tr>
            <th scope="col">Producto</th>
            <th scope="col" class="text-end">Precio</th>
            <th scope="col">Stock por talle</th>
            <th scope="col" class="text-end">Unidades</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($productos as $producto)
            <tr>
              <td>{{ $producto->nombre }}</td>
              <td class="text-end">ARS ${{ number_format($producto->precio, 0, ',', '.') }}</td>
              <td>
                @forelse ($producto->talles as $talle)
                  <span class="badge {{ $talle->stock > 0 ? 'text-bg-success' : 'text-bg-secondary' }}">
                    {{ $talle->talle }}: {{ $talle->stock }}
                  </span>
                @empty
                  <span class="text-muted">Sin talles cargados</span>
                @endforelse
              </td>
              <td class="text-end">{{ (int) $producto->unidades }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif

  <a class="btn btn-outline-dark" href="{{ route('reportes.stock.index') }}">← Volver al reporte</a>
@endsection