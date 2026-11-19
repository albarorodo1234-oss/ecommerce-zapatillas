<?php
// admin/crear.php
// Alta de productos (CREATE del CRUD): muestra el formulario y, al enviarlo
// por POST, valida los datos y los guarda con una consulta preparada.
// TP8: aquí se agregará el guardián de rutas (solo rol Administrador).

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';
require_once __DIR__ . '/producto_comun.php';

$titulo_pagina      = 'Nuevo producto | SoleMate';
$descripcion_pagina = 'Panel de administración de SoleMate: alta de un producto.';
$pagina_activa      = '';
$ruta_base          = '../';
$texto_boton        = 'Guardar producto';

$datos   = producto_vacio();
$errores = [];

// Opciones de las listas (marcas, categorías y orígenes) e imágenes.
try {
    $opciones = obtener_opciones($pdo);
} catch (PDOException $e) {
    error_log('Error al leer las opciones del formulario: ' . $e->getMessage());
    header('Location: index.php?status=error');
    exit;
}
$imagenes = imagenes_disponibles();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Captura y validación en el servidor.
    $datos   = leer_producto_post();
    $errores = validar_producto($datos, $opciones, $imagenes);

    // 2. Si no hay errores, INSERT con consulta preparada (:parametros).
    if (empty($errores)) {
        try {
            $sql = 'INSERT INTO productos
                        (marca_id, categoria_id, origen_id, nombre, descripcion, precio, imagen, alt, activo)
                    VALUES
                        (:marca_id, :categoria_id, :origen_id, :nombre, :descripcion, :precio, :imagen, :alt, :activo)';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(parametros_producto($datos));

            // 3. Patrón PRG (Post/Redirect/Get): se redirige al listado para
            //    que, si el usuario recarga la página, no se inserte dos veces.
            header('Location: index.php?status=creado');
            exit;
        } catch (PDOException $e) {
            error_log('Error al crear producto: ' . $e->getMessage());
            $errores['general'] = 'No se pudo guardar el producto. Intentá nuevamente.';
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
            <li class="breadcrumb-item active" aria-current="page">Nuevo producto</li>
          </ol>
        </nav>

        <h1 class="page-title mb-4">Nuevo producto</h1>

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