<?php
// admin/index.php
// Panel de administración de productos: listado principal (READ del CRUD).
// Desde aquí se accede a crear, editar y eliminar productos.
// TP8: protegido por el guardián de rutas (auth_guard.php): solo rol Administrador.

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';

$titulo_pagina      = 'Panel de productos | SoleMate';
$descripcion_pagina = 'Panel de administración de SoleMate: alta, modificación y baja de productos.';
$pagina_activa      = '';
$ruta_base          = '../'; // Esta página está en admin/: las rutas suben una carpeta.
require_once __DIR__ . '/../includes/auth_guard.php'; // TP8: exige haber iniciado sesión
verificarRol('Administrador');                        // TP8: y tener el rol Administrador

// Mensajes de resultado: crear.php, editar.php y eliminar.php redirigen
// aquí con ?status=... para informar qué pasó.
// Cada clave permitida => [color de la alerta de Bootstrap, texto].
$mensajes = [
    'creado'        => ['success', 'El producto se creó correctamente.'],
    'editado'       => ['success', 'Los cambios se guardaron correctamente.'],
    'eliminado'     => ['success', 'El producto se eliminó correctamente.'],
    'con_ventas'    => ['warning', 'No se puede eliminar un producto que ya tiene ventas registradas. Para ocultarlo del catálogo, editalo y desmarcá "Visible en el catálogo".'],
    'no_encontrado' => ['danger', 'El producto solicitado no existe.'],
    'error'         => ['danger', 'Ocurrió un error inesperado. Intentá nuevamente.'],
];

// Solo se muestra un mensaje si ?status= trae una de las claves de la lista:
// lo que escribe el visitante en la URL nunca se imprime directamente.
$mensaje = $mensajes[dato_get('status')] ?? null;

// READ: todos los productos con los nombres de su marca, categoría y origen.
$productos     = [];
$error_lectura = false;

try {
    $sql = 'SELECT p.id, p.nombre, p.precio, p.imagen, p.alt, p.activo,
                   m.nombre AS marca, c.nombre AS categoria, o.nombre AS origen
            FROM productos AS p
            INNER JOIN marcas     AS m ON p.marca_id     = m.id
            INNER JOIN categorias AS c ON p.categoria_id = c.id
            INNER JOIN origenes   AS o ON p.origen_id    = o.id
            ORDER BY p.id DESC';

    // Consulta preparada: esta no recibe datos del usuario, pero se usa
    // prepare() + execute() igual que en el resto del CRUD.
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $productos = $stmt->fetchAll();
} catch (PDOException $e) {
    // El detalle técnico va al log; en pantalla, un aviso amable.
    error_log('Error al listar productos: ' . $e->getMessage());
    $error_lectura = true;
}

require_once __DIR__ . '/../includes/header.php';
?>
    <main id="contenido">
      <div class="container section-padding pt-4">
        <nav aria-label="Ruta de navegación">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= $ruta_base ?>index.php">Inicio</a></li>
            <li class="breadcrumb-item active" aria-current="page">Panel de productos</li>
          </ol>
        </nav>

        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
          <div>
            <h1 class="page-title">Panel de productos</h1>
            <p class="results-count mb-0">
              <?= count($productos) ?> <?= count($productos) === 1 ? 'producto registrado' : 'productos registrados' ?>
            </p>
          </div>
          <a href="crear.php" class="btn btn-acento">
            <i class="bi bi-plus-lg" aria-hidden="true"></i> Nuevo producto
          </a>
        </div>

        <?php if ($mensaje): ?>
          <div class="alert alert-<?= e($mensaje[0]) ?>" role="alert">
            <?= e($mensaje[1]) ?>
          </div>
        <?php endif; ?>

        <?php if ($error_lectura): ?>
          <div class="alert alert-danger" role="alert">
            No se pudieron cargar los productos. Intentá nuevamente más tarde.
          </div>
        <?php elseif (empty($productos)): ?>
          <div class="alert alert-info" role="alert">
            Todavía no hay productos. Creá el primero con el botón "Nuevo producto".
          </div>
        <?php else: ?>
          <div class="tarjeta p-3">
            <div class="table-responsive">
              <table class="table table-striped table-hover align-middle mb-0">
                <caption class="visually-hidden">Listado de productos de la tienda</caption>
                <thead>
                  <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Imagen</th>
                    <th scope="col">Producto</th>
                    <th scope="col">Marca</th>
                    <th scope="col">Categoría</th>
                    <th scope="col">Origen</th>
                    <th scope="col" class="text-end">Precio</th>
                    <th scope="col">Estado</th>
                    <th scope="col" class="text-end">Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($productos as $producto): ?>
                    <tr>
                      <td><?= e($producto['id']) ?></td>
                      <td>
                        <?php if (!empty($producto['imagen'])): ?>
                          <img
                            src="<?= e($ruta_base . $producto['imagen']) ?>"
                            alt="<?= e($producto['alt'] ?? '') ?>"
                            width="64"
                            height="64"
                            class="rounded object-fit-cover"
                            loading="lazy"
                          />
                        <?php else: ?>
                          <span class="small text-body-secondary">Sin imagen</span>
                        <?php endif; ?>
                      </td>
                      <td class="fw-semibold"><?= e($producto['nombre']) ?></td>
                      <td><?= e($producto['marca']) ?></td>
                      <td><?= e($producto['categoria']) ?></td>
                      <td><?= e($producto['origen']) ?></td>
                      <td class="text-end text-nowrap">ARS $<?= e(number_format((float) $producto['precio'], 0, ',', '.')) ?></td>
                      <td>
                        <?php if ($producto['activo']): ?>
                          <span class="badge text-bg-success">Visible</span>
                        <?php else: ?>
                          <span class="badge text-bg-secondary">Oculto</span>
                        <?php endif; ?>
                      </td>
                      <td class="text-end text-nowrap">
                        <a href="editar.php?id=<?= e($producto['id']) ?>" class="btn btn-sm btn-contorno">
                          <i class="bi bi-pencil" aria-hidden="true"></i> Editar
                          <span class="visually-hidden"><?= e($producto['nombre']) ?></span>
                        </a>
                        <!-- Eliminar va por POST (no con un enlace GET) y pide confirmación -->
                        <form
                          action="eliminar.php"
                          method="post"
                          class="d-inline"
                          onsubmit="return confirm('¿Eliminar el producto con ID <?= (int) $producto['id'] ?>? Esta acción no se puede deshacer.');"
                        >
                          <input type="hidden" name="id" value="<?= e($producto['id']) ?>" />
                          <button type="submit" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-trash" aria-hidden="true"></i> Eliminar
                            <span class="visually-hidden"><?= e($producto['nombre']) ?></span>
                          </button>
                        </form>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>