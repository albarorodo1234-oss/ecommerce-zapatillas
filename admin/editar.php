<?php
// admin/editar.php
// Modificación de productos (UPDATE del CRUD): carga el producto indicado
// en la URL (?id=...), muestra el formulario con sus datos y, al enviarlo,
// valida y guarda los cambios con una consulta preparada.
// TP8: aquí se agregará el guardián de rutas (solo rol Administrador).

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';
require_once __DIR__ . '/producto_comun.php';

$titulo_pagina      = 'Editar producto | SoleMate';
$descripcion_pagina = 'Panel de administración de SoleMate: modificación de un producto.';
$pagina_activa      = '';
$ruta_base          = '../';
$texto_boton        = 'Guardar cambios';

$errores = [];

// 1. El id llega por la URL: debe ser un número entero mayor que 0.
//    Cualquier otra cosa (texto, negativos, "1 OR 1=1"...) se rechaza.
$id = filter_var(dato_get('id'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($id === false) {
    header('Location: index.php?status=no_encontrado');
    exit;
}

// 2. READ del producto por su id (consulta preparada) y opciones de las listas.
try {
    $opciones = obtener_opciones($pdo);

    $stmt = $pdo->prepare(
        'SELECT nombre, marca_id, categoria_id, origen_id, precio, descripcion, imagen, alt, activo
         FROM productos
         WHERE id = :id'
    );
    $stmt->execute([':id' => $id]);
    $producto = $stmt->fetch(); // Un solo registro, o false si no existe
} catch (PDOException $e) {
    error_log('Error al cargar el producto a editar: ' . $e->getMessage());
    header('Location: index.php?status=error');
    exit;
}

if (!$producto) {
    header('Location: index.php?status=no_encontrado');
    exit;
}

// Datos iniciales del formulario: los de la base (los NULL se muestran vacíos).
$datos    = array_map(fn($valor) => $valor ?? '', $producto);
$imagenes = imagenes_disponibles();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 3. Captura y validación en el servidor (las mismas reglas que el alta).
    $datos   = leer_producto_post();
    $errores = validar_producto($datos, $opciones, $imagenes);

    // 4. Si no hay errores, UPDATE con consulta preparada.
    //    El WHERE id = :id asegura que se modifica solo este producto.
    if (empty($errores)) {
        try {
            $sql = 'UPDATE productos SET
                        marca_id     = :marca_id,
                        categoria_id = :categoria_id,
                        origen_id    = :origen_id,
                        nombre       = :nombre,
                        descripcion  = :descripcion,
                        precio       = :precio,
                        imagen       = :imagen,
                        alt          = :alt,
                        activo       = :activo
                    WHERE id = :id';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(parametros_producto($datos) + [':id' => $id]);

            header('Location: index.php?status=editado');
            exit;
        } catch (PDOException $e) {
            error_log('Error al editar producto: ' . $e->getMessage());
            $errores['general'] = 'No se pudieron guardar los cambios. Intentá nuevamente.';
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>
    <main id="contenido">
      <div class="container section-padding pt-4">
        <nav aria-label="Ruta de navegación">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= $ruta_base ?>index.php">Inicio</a></li>
            <li class="breadcrumb-item"><a href="index.php">Panel de productos</a></li>
            <li class="breadcrumb-item active" aria-current="page">Editar producto</li>
          </ol>
        </nav>

        <h1 class="page-title">Editar producto</h1>
        <p class="results-count mb-4">
          ID <?= e($id) ?> · <?= e($producto['nombre']) ?>
        </p>

        <div class="row">
          <div class="col-lg-9">
            <div class="tarjeta p-4">
<?php require __DIR__ . '/formulario_producto.php'; ?>
            </div>
          </div>
        </div>
      </div>
    </main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>