<?php
$titulo_pagina      = "Catálogo de zapatillas importadas | SoleMate";
$descripcion_pagina = "Explorá el catálogo de SoleMate: zapatillas Nike, Jordan, Adidas y New Balance importadas de Chile y Bolivia. Filtrá por talla, precio, marca y origen.";
$pagina_activa      = "catalogo";
$busqueda           = isset($_GET['q']) && is_string($_GET['q']) ? trim($_GET['q']) : '';

require_once 'includes/funciones.php';

// Listas de valores permitidos para los filtros (se usan al armar el formulario
// y, más adelante, para validar lo que llegue por GET).
$tallas_disponibles  = [38, 39, 40, 41, 42, 43, 44];
$marcas_disponibles  = ['nike' => 'Nike', 'jordan' => 'Jordan', 'adidas' => 'Adidas', 'nb' => 'New Balance'];
$origenes_disponibles = ['chile' => 'Chile', 'bolivia' => 'Bolivia'];
$precio_minimo = 50000;
$precio_maximo = 300000;
$precio_paso   = 10000;

// Catálogo de productos (en un sitio real saldría de una base de datos).
$productos = [
    [
        'nombre' => 'Nike Air Max 270',
        'marca'  => 'nike',
        'origen' => 'chile',
        'talla'  => 38,
        'precio' => 189900,
        'tipo'   => 'Zapatilla running',
        'imagen' => 'img/nike-air-max-270.jpg',
        'alt'    => 'Zapatilla Nike Air Max 270 blanca con suela roja',
    ],
    [
        'nombre' => 'Adidas Ultraboost',
        'marca'  => 'adidas',
        'origen' => 'bolivia',
        'talla'  => 40,
        'precio' => 219900,
        'tipo'   => 'Zapatilla running',
        'imagen' => 'img/adidas-ultraboost.jpg',
        'alt'    => 'Zapatilla Adidas Ultraboost negra con suela blanca',
    ],
    [
        'nombre' => 'Jordan 1 Retro',
        'marca'  => 'jordan',
        'origen' => 'chile',
        'talla'  => 41,
        'precio' => 249900,
        'tipo'   => 'Zapatilla basketball',
        'imagen' => 'img/jordan-1-retro.jpg',
        'alt'    => 'Zapatilla Jordan 1 Retro roja, blanca y negra',
    ],
    [
        'nombre' => 'New Balance 990',
        'marca'  => 'nb',
        'origen' => 'bolivia',
        'talla'  => 42,
        'precio' => 229900,
        'tipo'   => 'Zapatilla lifestyle',
        'imagen' => 'img/new-balance-990.jpg',
        'alt'    => 'Zapatilla New Balance 990 gris',
    ],
];
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
            <p class="results-count mb-0">
             Mostrando 4 productos
            <?php if ($busqueda !== ''): ?>
              · Buscaste: <strong><?= htmlspecialchars($busqueda) ?></strong>
            <?php endif; ?>
            </p>
          </div>
          <div>
            <label class="visually-hidden" for="orden">Ordenar productos</label>
            <select class="form-select form-select-sm sort" id="orden">
              <option value="">Ordenar por precio</option>
              <option value="menor">Menor precio</option>
              <option value="mayor">Mayor precio</option>
            </select>
          </div>
        </div>

        <div class="row g-4">
          <!-- Filtros -->
          <aside class="col-lg-3" aria-label="Filtros del catálogo">
                      <form class="tarjeta filters filters-sticky" method="get" action="catalogo.php">
              <h2 class="filters-title">Filtros</h2>
              <p class="filters-sub">Refina tu búsqueda</p>

              <!-- Conserva la búsqueda del menú cuando se aplican los filtros -->
              <input type="hidden" name="q" value="<?= e($busqueda) ?>" />

              <fieldset class="filter-group">
                <legend>Tallas</legend>
                <div class="pill-group">
                  <?php foreach ($tallas_disponibles as $talla): ?>
                    <input class="btn-check" type="checkbox" id="talla-<?= e($talla) ?>" name="talla[]" value="<?= e($talla) ?>" />
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
                  value="<?= $precio_maximo ?>"
                />
                <div class="range-labels">
                  <span>ARS $<?= number_format($precio_minimo, 0, ',', '.') ?></span>
                  <span>ARS $<?= number_format($precio_maximo, 0, ',', '.') ?></span>
                </div>
              </fieldset>

              <fieldset class="filter-group">
                <legend>Marcas</legend>
                <div class="pill-group">
                  <?php foreach ($marcas_disponibles as $clave => $etiqueta): ?>
                    <input class="btn-check" type="checkbox" id="marca-<?= e($clave) ?>" name="marca[]" value="<?= e($clave) ?>" />
                    <label class="btn pill" for="marca-<?= e($clave) ?>"><?= e($etiqueta) ?></label>
                  <?php endforeach; ?>
                </div>
              </fieldset>

              <fieldset class="filter-group">
                <legend>Origen</legend>
                <div class="pill-group">
                  <?php foreach ($origenes_disponibles as $clave => $etiqueta): ?>
                    <input class="btn-check" type="checkbox" id="origen-<?= e($clave) ?>" name="origen[]" value="<?= e($clave) ?>" />
                    <label class="btn pill" for="origen-<?= e($clave) ?>"><?= e($etiqueta) ?></label>
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
            <div class="row g-3">
              <?php foreach ($productos as $producto): ?>
              <div class="col-12 col-sm-6 col-xl-3">
                <article class="card tarjeta product-card">
                  <img
                    src="<?= e($producto['imagen']) ?>"
                    class="card-img-top"
                    alt="<?= e($producto['alt']) ?>"
                    loading="lazy"
                  />
                  <div class="card-body">
                    <div class="meta">
                      <span class="badge-origen"><?= e($origenes_disponibles[$producto['origen']]) ?></span>
                      <span class="talla-ref">ARG <?= e($producto['talla']) ?></span>
                    </div>
                    <h3 class="product-name">
                      <a class="stretched-link" href="producto.php"><?= e($producto['nombre']) ?></a>
                    </h3>
                    <p class="product-type"><?= e($producto['tipo']) ?></p>
                    <div class="price-row">
                      <p class="price">ARS $<?= number_format($producto['precio'], 0, ',', '.') ?></p>
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
          </section>
        </div>
      </div>
    </main>

    <?php require_once 'includes/footer.php'; ?>