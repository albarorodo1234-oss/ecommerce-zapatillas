<?php
// catalogo.php
// Catálogo público: los productos se leen de la base de datos (TP7).
// Los filtros GET del TP5 (búsqueda, talla, precio, marca y origen) se
// validan igual que antes y ahora se convierten en condiciones de una
// consulta preparada.

require_once 'config/conexion.php';
require_once 'includes/funciones.php';

$titulo_pagina      = "Catálogo de zapatillas importadas | SoleMate";
$descripcion_pagina = "Explorá el catálogo de SoleMate: zapatillas Nike, Jordan, Adidas y New Balance importadas de Chile y Bolivia. Filtrá por talla, precio, marca y origen.";
$pagina_activa      = "catalogo";
$busqueda           = dato_get('q');

// Valores permitidos para los filtros.
$tallas_disponibles = [38, 39, 40, 41, 42, 43, 44];
$precio_minimo      = 50000;
$precio_maximo      = 300000;
$precio_paso        = 10000;

// Orden del listado: el ORDER BY no admite :parametros, por eso cada
// opción permitida se traduce a un fragmento SQL fijo (lista blanca).
$ordenes_disponibles = [
    'menor' => 'p.precio ASC',
    'mayor' => 'p.precio DESC',
];

$productos_filtrados = [];
$marcas_disponibles  = [];
$origenes_disponibles = [];
$error_catalogo      = false;

try {
    // Marcas y orígenes para los filtros, desde las tablas maestras: [id => nombre].
    $stmt = $pdo->prepare('SELECT id, nombre FROM marcas ORDER BY nombre');
    $stmt->execute();
    $marcas_disponibles = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    $stmt = $pdo->prepare('SELECT id, nombre FROM origenes ORDER BY nombre');
    $stmt->execute();
    $origenes_disponibles = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
} catch (PDOException $e) {
    error_log('Error al leer los filtros del catálogo: ' . $e->getMessage());
    $error_catalogo = true;
}

// ----- Lectura y validación de los datos que llegan por GET (igual que en el TP5) -----
$errores_filtros = [];

// Búsqueda de texto (viene del buscador del menú): máximo 60 caracteres.
if (mb_strlen($busqueda) > 60) {
    $busqueda = mb_substr($busqueda, 0, 60);
    $errores_filtros['q'] = 'La búsqueda se acortó a 60 caracteres.';
}

// Tallas: cada valor debe ser un número entero de la lista permitida.
$tallas_elegidas = [];
foreach (dato_get_lista('talla') as $valor) {
    $talla = filter_var(trim($valor), FILTER_VALIDATE_INT);
    if ($talla !== false && in_array($talla, $tallas_disponibles, true)) {
        $tallas_elegidas[] = $talla;
    } else {
        $errores_filtros['talla'] = 'Se ignoró una talla que no existe.';
    }
}

// Marcas: cada valor debe ser el id de una marca de la base.
$marcas_elegidas = [];
foreach (dato_get_lista('marca') as $valor) {
    $valor = trim($valor);
    if (ctype_digit($valor) && isset($marcas_disponibles[(int) $valor])) {
        $marcas_elegidas[] = (int) $valor;
    } else {
        $errores_filtros['marca'] = 'Se ignoró una marca que no existe.';
    }
}

// Orígenes: cada valor debe ser el id de un origen de la base.
$origenes_elegidos = [];
foreach (dato_get_lista('origen') as $valor) {
    $valor = trim($valor);
    if (ctype_digit($valor) && isset($origenes_disponibles[(int) $valor])) {
        $origenes_elegidos[] = (int) $valor;
    } else {
        $errores_filtros['origen'] = 'Se ignoró un origen que no existe.';
    }
}

// Precio máximo: un entero entre el mínimo y el máximo del deslizador.
$precio_tope     = $precio_maximo;
$filtrar_precio  = false;
$precio_recibido = dato_get('precio_max');
if ($precio_recibido !== '') {
    $precio_valido = filter_var($precio_recibido, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => $precio_minimo, 'max_range' => $precio_maximo],
    ]);
    if ($precio_valido === false) {
        $errores_filtros['precio_max'] = 'Se ignoró un precio máximo fuera del rango permitido.';
    } else {
        $precio_tope    = $precio_valido;
        $filtrar_precio = true;
    }
}

// Orden: solo se acepta una clave de la lista blanca.
$orden = dato_get('orden');
if ($orden !== '' && !isset($ordenes_disponibles[$orden])) {
    $orden = '';
    $errores_filtros['orden'] = 'Se ignoró un orden que no existe.';
}

// Arma una lista de marcadores (:prefijo0, :prefijo1, ...) para un IN (...)
// y agrega cada valor a $parametros. Así ningún dato se escribe dentro del SQL.
function marcadores_in(string $prefijo, array $valores, array &$parametros): string
{
    $marcadores = [];
    foreach ($valores as $i => $valor) {
        $marcador              = ':' . $prefijo . $i;
        $marcadores[]          = $marcador;
        $parametros[$marcador] = $valor;
    }
    return implode(', ', $marcadores);
}

// ----- READ: consulta preparada con los filtros elegidos -----
if (!$error_catalogo) {
    // Solo productos visibles. Cada filtro activo agrega una condición.
    $condiciones = ['p.activo = 1'];
    $parametros  = [];

    if ($busqueda !== '') {
        // Se escapan % y _ para que se busquen como texto y no como comodines de LIKE.
        // Cada marcador se usa una sola vez (requisito de las consultas preparadas nativas).
        $texto = '%' . addcslashes($busqueda, '%_\\') . '%';
        $condiciones[] = '(p.nombre LIKE :q_nombre OR m.nombre LIKE :q_marca OR c.nombre LIKE :q_categoria)';
        $parametros[':q_nombre']    = $texto;
        $parametros[':q_marca']     = $texto;
        $parametros[':q_categoria'] = $texto;
    }
    if (!empty($marcas_elegidas)) {
        $condiciones[] = 'p.marca_id IN (' . marcadores_in('marca', $marcas_elegidas, $parametros) . ')';
    }
    if (!empty($origenes_elegidos)) {
        $condiciones[] = 'p.origen_id IN (' . marcadores_in('origen', $origenes_elegidos, $parametros) . ')';
    }
    if (!empty($tallas_elegidas)) {
        // La talla está en producto_talle (stock por talle): el producto
        // entra si tiene stock en alguna de las tallas elegidas.
        $condiciones[] = 'EXISTS (SELECT 1 FROM producto_talle AS pt
                                  WHERE pt.producto_id = p.id
                                    AND pt.stock > 0
                                    AND pt.talle IN (' . marcadores_in('talla', $tallas_elegidas, $parametros) . '))';
    }
    if ($filtrar_precio) {
        $condiciones[] = 'p.precio <= :precio_max';
        $parametros[':precio_max'] = $precio_tope;
    }

    $sql = 'SELECT p.id, p.nombre, p.precio, p.imagen, p.alt,
                   c.nombre AS categoria, o.nombre AS origen,
                   (SELECT MIN(pt.talle) FROM producto_talle AS pt
                     WHERE pt.producto_id = p.id AND pt.stock > 0) AS talle_min,
                   (SELECT MAX(pt.talle) FROM producto_talle AS pt
                     WHERE pt.producto_id = p.id AND pt.stock > 0) AS talle_max
            FROM productos AS p
            INNER JOIN marcas     AS m ON p.marca_id     = m.id
            INNER JOIN categorias AS c ON p.categoria_id = c.id
            INNER JOIN origenes   AS o ON p.origen_id    = o.id
            WHERE ' . implode(' AND ', $condiciones) . '
            ORDER BY ' . ($ordenes_disponibles[$orden] ?? 'p.id ASC');

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($parametros);
        $productos_filtrados = $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log('Error al leer el catálogo: ' . $e->getMessage());
        $error_catalogo = true;
    }
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
                · Buscaste: <strong><?= e($busqueda) ?></strong>
              <?php endif; ?>
            </p>
          </div>
          <div>
            <!-- form="filtros": este select pertenece al formulario de filtros aunque esté fuera de él -->
            <label class="visually-hidden" for="orden">Ordenar productos</label>
            <select class="form-select form-select-sm sort" id="orden" name="orden" form="filtros" onchange="this.form.submit()">
              <option value="">Ordenar por precio</option>
              <option value="menor" <?= $orden === 'menor' ? 'selected' : '' ?>>Menor precio</option>
              <option value="mayor" <?= $orden === 'mayor' ? 'selected' : '' ?>>Mayor precio</option>
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

            <div class="row g-3">
              <?php foreach ($productos_filtrados as $producto): ?>
                <?php
                // Talles con stock: "ARG 38–40", "ARG 41" o, si no tiene stock cargado, "Consultar talle".
                if ($producto['talle_min'] === null) {
                    $texto_talles = 'Consultar talle';
                } elseif ($producto['talle_min'] == $producto['talle_max']) {
                    $texto_talles = 'ARG ' . $producto['talle_min'];
                } else {
                    $texto_talles = 'ARG ' . $producto['talle_min'] . '–' . $producto['talle_max'];
                }
                ?>
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
          </section>
        </div>
      </div>
    </main>

<?php require_once 'includes/footer.php'; ?>