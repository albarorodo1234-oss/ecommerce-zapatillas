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
// ----- Lectura, validación y filtrado de los datos que llegan por GET -----
$errores_filtros = [];

// Búsqueda de texto (viene del buscador del menú): máximo 60 caracteres.
// $busqueda ya pasó por isset, is_string y trim en la primera parte del archivo.
if (mb_strlen($busqueda) > 60) {
    $busqueda = mb_substr($busqueda, 0, 60);
    $errores_filtros['q'] = 'La búsqueda se acortó a 60 caracteres.';
}

// Tallas: cada valor debe ser un número entero y estar en la lista permitida.
$tallas_elegidas = [];
foreach (dato_get_lista('talla') as $valor) {
    $talla = filter_var(trim($valor), FILTER_VALIDATE_INT);
    if ($talla !== false && in_array($talla, $tallas_disponibles, true)) {
        $tallas_elegidas[] = $talla;
    } else {
        $errores_filtros['talla'] = 'Se ignoró una talla que no existe.';
    }
}

// Marcas: cada valor debe ser una clave de $marcas_disponibles.
$marcas_elegidas = [];
foreach (dato_get_lista('marca') as $valor) {
    $valor = trim($valor);
    if (isset($marcas_disponibles[$valor])) {
        $marcas_elegidas[] = $valor;
    } else {
        $errores_filtros['marca'] = 'Se ignoró una marca que no existe.';
    }
}

// Orígenes: cada valor debe ser una clave de $origenes_disponibles.
$origenes_elegidos = [];
foreach (dato_get_lista('origen') as $valor) {
    $valor = trim($valor);
    if (isset($origenes_disponibles[$valor])) {
        $origenes_elegidos[] = $valor;
    } else {
        $errores_filtros['origen'] = 'Se ignoró un origen que no existe.';
    }
}

// Precio máximo: un entero entre el mínimo y el máximo del deslizador.
$precio_tope = $precio_maximo;
$precio_recibido = dato_get('precio_max');
if ($precio_recibido !== '') {
    $precio_valido = filter_var($precio_recibido, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => $precio_minimo, 'max_range' => $precio_maximo],
    ]);
    if ($precio_valido === false) {
        $errores_filtros['precio_max'] = 'Se ignoró un precio máximo fuera del rango permitido.';
    } else {
        $precio_tope = $precio_valido;
    }
}

// Filtrado: un producto se queda solo si cumple TODOS los filtros activos.
$productos_filtrados = [];
foreach ($productos as $producto) {
    $texto_producto = $producto['nombre'] . ' ' . $marcas_disponibles[$producto['marca']] . ' ' . $producto['tipo'];

    if ($busqueda !== '' && mb_stripos($texto_producto, $busqueda) === false) {
        continue;
    }
    if (!empty($tallas_elegidas) && !in_array($producto['talla'], $tallas_elegidas, true)) {
        continue;
    }
    if (!empty($marcas_elegidas) && !in_array($producto['marca'], $marcas_elegidas, true)) {
        continue;
    }
    if (!empty($origenes_elegidos) && !in_array($producto['origen'], $origenes_elegidos, true)) {
        continue;
    }
    if ($producto['precio'] > $precio_tope) {
        continue;
    }

    $productos_filtrados[] = $producto;
}

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
             Mostrando <?= count($productos_filtrados) ?> <?= count($productos_filtrados) === 1 ? 'producto' : 'productos' ?>
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
                  <?php foreach ($marcas_disponibles as $clave => $etiqueta): ?>
                    <input class="btn-check" type="checkbox" id="marca-<?= e($clave) ?>" name="marca[]" value="<?= e($clave) ?>" <?= in_array($clave, $marcas_elegidas, true) ? 'checked' : '' ?> />
                    <label class="btn pill" for="marca-<?= e($clave) ?>"><?= e($etiqueta) ?></label>
                  <?php endforeach; ?>
                </div>
              </fieldset>

              <fieldset class="filter-group">
                <legend>Origen</legend>
                <div class="pill-group">
                  <?php foreach ($origenes_disponibles as $clave => $etiqueta): ?>
                    <input class="btn-check" type="checkbox" id="origen-<?= e($clave) ?>" name="origen[]" value="<?= e($clave) ?>" <?= in_array($clave, $origenes_elegidos, true) ? 'checked' : '' ?> />
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
                      <?php if (empty($productos_filtrados)): ?>
              <div class="alert alert-info" role="status">
                No encontramos zapatillas con esos filtros. Probá con menos filtros o
                <a href="catalogo.php" class="alert-link">limpiá la búsqueda</a>.
              </div>
            <?php endif; ?>
            <div class="row g-3">
              <?php foreach ($productos_filtrados as $producto): ?>
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