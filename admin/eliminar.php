<?php
// admin/eliminar.php
// Baja de productos (DELETE del CRUD). No muestra ninguna página: recibe el
// id por POST desde el botón "Eliminar" del panel, borra el producto con una
// consulta preparada y vuelve al listado con el resultado en ?status=.
// TP8: protegido por el guardián de rutas (auth_guard.php): solo rol Administrador.

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';

$ruta_base = '../'; // Esta página está en admin/: el guardián redirige una carpeta arriba.
require_once __DIR__ . '/../includes/auth_guard.php'; // TP8: exige haber iniciado sesión
verificarRol('Administrador');                        // TP8: y tener el rol Administrador

// 1. Solo se acepta POST: si alguien abre esta dirección directo
//    (o la sigue desde un enlace), no se borra nada.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// 2. El id debe ser un número entero mayor que 0.
$id = filter_var(dato_post('id'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($id === false) {
    header('Location: index.php?status=no_encontrado');
    exit;
}

// 3. DELETE con consulta preparada.
try {
    $stmt = $pdo->prepare('DELETE FROM productos WHERE id = :id');
    $stmt->execute([':id' => $id]);

    // rowCount() dice cuántas filas se borraron: 0 = ese id no existía.
    $status = $stmt->rowCount() > 0 ? 'eliminado' : 'no_encontrado';
} catch (PDOException $e) {
    // SQLSTATE 23000 = violación de integridad: el producto aparece en
    // detalle_pedidos (ON DELETE RESTRICT), es decir, ya tiene ventas.
    if ($e->getCode() === '23000') {
        $status = 'con_ventas';
    } else {
        error_log('Error al eliminar producto: ' . $e->getMessage());
        $status = 'error';
    }
}

header('Location: index.php?status=' . $status);
exit;