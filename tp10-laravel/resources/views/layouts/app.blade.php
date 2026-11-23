{{-- resources/views/layouts/app.blade.php --}}
{{-- TP10 (Parte B): plantilla base de Blade. Las vistas del reporte la usan --}}
{{-- con @extends y completan sus huecos (@yield) con @section.            --}}
<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('titulo') | SoleMate · Reportes</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  </head>
  <body class="bg-light">
    <nav class="navbar navbar-dark" style="background-color: #d43f00">
      <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('reportes.stock.index') }}">👟 SoleMate · Reportes</a>
        <span class="navbar-text text-white small">Laravel {{ app()->version() }}</span>
      </div>
    </nav>

    <main class="container py-4">
      @yield('contenido')
    </main>

    <footer class="container pb-4 text-muted small">
      TP10 – Módulo en Laravel (rutas, controlador, Eloquent y Blade) sobre la base solemate_db.
    </footer>
  </body>
</html>