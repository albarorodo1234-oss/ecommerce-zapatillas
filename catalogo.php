<?php
// catalogo.php
// Catálogo público: los productos se leen de la base de datos (TP7).
// Los filtros GET del TP5 (búsqueda, talla, precio, marca y origen) se
// validan y se convierten en condiciones de una consulta preparada.
// TP9: la validación y la consulta se movieron a includes/catalogo_consulta.php,
// que también usa el endpoint api/productos.php. Esta página sigue
// funcionando sola (sin JavaScript); con JavaScript, js/main.js pide los
// productos a la API y actualiza el listado sin recargar la página.

require_once 'config/conexion.php';
require_once 'includes/catalogo_consulta.php'; // TP9: lógica compartida con la API

$titulo_pagina      = "Catálogo de zapatillas importadas | SoleMate";
$descripcion_pagina = "Explorá el catálogo de SoleMate: zapatillas Nike, Jordan, Adidas y New Balance importadas de Chile y Bolivia. Filtrá por talla, precio, marca y origen.";
$pagina_activa      = "catalogo";

$productos_filtrados  = [];
$marcas_disponibles   = [];
$origenes_disponibles = [];
$error_catalogo       = false;

// 1. Marcas y orígenes para los filtros (tablas maestras).
try {
    [$marcas_disponibles, $origenes_disponibles] = catalogo_maestros($pdo);
} catch (PDOException $e) {
    error_log('Error al leer los filtros del catálogo: ' . $e->getMessage());
    $error_catalogo = true;
}

// 2. Lectura y validación de los filtros que llegan por GET.
[$filtros, $errores_filtros] = catalogo_leer_filtros($marcas_disponibles, $origenes_disponibles);

// 3. READ: consulta preparada con los filtros válidos.
if (!$error_catalogo) {
    try {
        $productos_filtrados = catalogo_buscar($pdo, $filtros);
    } catch (PDOException $e) {
        error_log('Error al leer el catálogo: ' . $e->getMessage());
        $error_catalogo = true;
    }
}

// Variables que usa el HTML (mismos nombres que en el TP7).
$busqueda           = $filtros['q'];
$tallas_elegidas    = $filtros['tallas'];
$marcas_elegidas    = $filtros['marcas'];
$origenes_elegidos  = $filtros['origenes'];
$orden              = $filtros['orden'];
$precio_tope        = $filtros['precio_max'] ?? CATALOGO_PRECIO_MAXIMO;
$tallas_disponibles = CATALOGO_TALLAS;
$precio_minimo      = CATALOGO_PRECIO_MINIMO;
$precio_maximo      = CATALOGO_PRECIO_MAXIMO;
$precio_paso        = CATALOGO_PRECIO_PASO;

require_once 'includes/header.php';
?>
    <main id="contenido">
      <div class="container section-padding pt-4">
        <nav aria-label="Ruta de navegación">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
            <li class="breadcrumb-item active" aria-current="page">Catálogo</li>
          </ol>
        </nav>

        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
          <div>
            <h1 class="page-title">Catálogo de Productos</h1>
            <!-- TP9: id para que js/main.js actualice el contador sin recargar -->
            <p class="results-count mb-0" id="contador-resultados" aria-live="polite">
              Mostrando <?= count($productos_filtrados) ?> <?= count($productos_filtrados) === 1 ? 'producto' : 'productos' ?>
              <?php if ($busqueda !== ''): ?>
                · Buscaste: <strong><?= e($busqueda) ?></strong>
              <?php endif; ?>
            </p>
          </div>
          <div>
            <!-- form="filtros": este select pertenece al formulario de filtros aunque esté fuera de él -->
            <label class="visually-hidden" for="orden">Ordenar productos</label>
            <select class="form-select form-select-sm sort" id="orden" name="orden" form="filtros" onchange="this.form.requestSubmit()">
              <option value="">Ordenar por precio</option>
              <option value="menor" <?= $orden === 'menor' ? 'selected' : '' ?>>Menor precio</option>
              <option value="mayor" <?= $orden === 'mayor' ? 'selected' : '' ?>>Mayor precio</option>
            </select>
          </div>
        </div>

        <!-- TP9: js/main.js usa este contenedor para los avisos de la API (400 y errores) -->
        <div id="avisos-catalogo">
          <?php if (!empty($errores_filtros)): ?>
            <div class="alert alert-warning" role="alert">
              <strong>Ajustamos tu búsqueda:</strong>
              <ul class="mb-0">
                <?php foreach ($errores_filtros as $aviso): ?>
                  <li><?= e($aviso) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>
        </div>

        <div class="row g-4">
          <!-- Filtros -->
          <aside class="col-lg-3" aria-label="Filtros del catálogo">
            <form id="filtros" class="tarjeta filters filters-sticky" method="get" action="catalogo.php">
              <h2 class="filters-title">Filtros</h2>
              <p class="filters-sub">Refina tu búsqueda</p>

              <!-- Conserva la búsqueda del menú cuando se aplican los filtros -->
              <input type="hidden" name="q" value="<?= e($busqueda) ?>" />

              <fieldset class="filter-group">
                <legend>Tallas</legend>
                <div class="pill-group">
                  <?php foreach ($tallas_disponibles as $talla): ?>
                    <input class="btn-check" type="checkbox" id="talla-<?= e($talla) ?>" name="talla[]" value="<?= e($talla) ?>" <?= in_array($talla, $tallas_elegidas, true) ? 'checked' : '' ?> />
                    <label class="btn pill" for="talla-<?= e($talla) ?>"><?= e($talla) ?></label>
                  <?php endforeach; ?>
                </div>
              </fieldset>

              <fieldset class="filter-group">
                <legend>Precio máximo</legend>
                <label class="visually-hidden" for="precio-max">Precio máximo</label>
                <input
                  class="form-range"
                  type="range"
                  id="precio-max"
                  name="precio_max"
                  min="<?= $precio_minimo ?>"
                  max="<?= $precio_maximo ?>"
                  step="<?= $precio_paso ?>"
                  value="<?= e($precio_tope) ?>"
                />
                <div class="range-labels">
                  <span>ARS $<?= number_format($precio_minimo, 0, ',', '.') ?></span>
                  <span>ARS $<?= number_format($precio_maximo, 0, ',', '.') ?></span>
                </div>
              </fieldset>

              <fieldset class="filter-group">
                <legend>Marcas</legend>
                <div class="pill-group">
                  <?php foreach ($marcas_disponibles as $id => $nombre): ?>
                    <input class="btn-check" type="checkbox" id="marca-<?= e($id) ?>" name="marca[]" value="<?= e($id) ?>" <?= in_array($id, $marcas_elegidas, true) ? 'checked' : '' ?> />
                    <label class="btn pill" for="marca-<?= e($id) ?>"><?= e($nombre) ?></label>
                  <?php endforeach; ?>
                </div>
              </fieldset>

              <fieldset class="filter-group">
                <legend>Origen</legend>
                <div class="pill-group">
                  <?php foreach ($origenes_disponibles as $id => $nombre): ?>
                    <input class="btn-check" type="checkbox" id="origen-<?= e($id) ?>" name="origen[]" value="<?= e($id) ?>" <?= in_array($id, $origenes_elegidos, true) ? 'checked' : '' ?> />
                    <label class="btn pill" for="origen-<?= e($id) ?>"><?= e($nombre) ?></label>
                  <?php endforeach; ?>
                </div>
              </fieldset>

              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-acento flex-fill">Aplicar</button>
                <a href="catalogo.php" class="btn btn-contorno flex-fill">Limpiar</a>
              </div>
            </form>
          </aside>

          <!-- Listado de productos -->
          <section class="col-lg-9" aria-labelledby="listado-titulo">
            <h2 id="listado-titulo" class="visually-hidden">Listado de productos</h2>

            <!-- TP9: js/main.js muestra aquí "Cargando…", "sin resultados" o el error -->
            <div id="estado-catalogo">
              <?php if ($error_catalogo): ?>
                <div class="alert alert-danger" role="alert">
                  No pudimos cargar el catálogo en este momento. Intentá nuevamente más tarde.
                </div>
              <?php elseif (empty($productos_filtrados)): ?>
                <div class="alert alert-info" role="status">
                  No encontramos zapatillas con esos filtros. Probá con menos filtros o
                  <a href="catalogo.php" class="alert-link">limpiá la búsqueda</a>.
                </div>
              <?php endif; ?>
            </div>

            <!-- TP9: js/main.js reemplaza el contenido de esta grilla con los datos JSON -->
            <div class="row g-3" id="grilla-productos">
              <?php foreach ($productos_filtrados as $producto): ?>
                <?php $texto_talles = catalogo_texto_talles($producto); // "ARG 38–40", "ARG 41" o "Consultar talle" ?>
                <div class="col-12 col-sm-6 col-xl-3">
                  <article class="card tarjeta product-card">
                    <?php if (!empty($producto['imagen'])): ?>
                      <img
                        src="<?= e($producto['imagen']) ?>"
                        class="card-img-top"
                        alt="<?= e($producto['alt'] ?? '') ?>"
                        loading="lazy"
                      />
                    <?php endif; ?>
                    <div class="card-body">
                      <div class="meta">
                        <span class="badge-origen"><?= e($producto['origen']) ?></span>
                        <span class="talla-ref"><?= e($texto_talles) ?></span>
                      </div>
                      <h3 class="product-name">
                        <a class="stretched-link" href="producto.php?id=<?= e($producto['id']) ?>"><?= e($producto['nombre']) ?></a>
                      </h3>
                      <p class="product-type">Zapatilla <?= e(mb_strtolower($producto['categoria'])) ?></p>
                      <div class="price-row">
                        <p class="price">ARS $<?= e(number_format((float) $producto['precio'], 0, ',', '.')) ?></p>
                        <button
                          type="button"
                          class="btn btn-acento btn-sm btn-add"
                          aria-label="Añadir <?= e($producto['nombre']) ?> al carrito"
                        >
                          Añadir
                        </button>
                      </div>
                    </div>
                  </article>
                </div>
              <?php endforeach; ?>
            </div>

            <!-- TP9: plantilla de una tarjeta. El navegador no la muestra: js/main.js
                 la copia por cada producto del JSON y completa los datos con textContent
                 (nunca con innerHTML, para evitar XSS). Debe tener el mismo diseño que
                 las tarjetas de arriba. -->
            <template id="plantilla-producto">
              <div class="col-12 col-sm-6 col-xl-3">
                <article class="card tarjeta product-card">
                  <img src="" class="card-img-top" alt="" loading="lazy" />
                  <div class="card-body">
                    <div class="meta">
                      <span class="badge-origen"></span>
                      <span class="talla-ref"></span>
                    </div>
                    <h3 class="product-name">
                      <a class="stretched-link" href=""></a>
                    </h3>
                    <p class="product-type"></p>
                    <div class="price-row">
                      <p class="price"></p>
                      <button type="button" class="btn btn-acento btn-sm btn-add" aria-label="">
                        Añadir
                      </button>
                    </div>
                  </div>
                </article>
              </div>
            </template>
          </section>
        </div>
      </div>
    </main>

<?php require_once 'includes/footer.php'; ?>