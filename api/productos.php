<?php
// api/productos.php
// TP9: endpoint (API) del catálogo. No devuelve HTML, solo datos en formato
// JSON: la página los pide en segundo plano con fetch() desde js/main.js
// y arma las tarjetas sin recargar.
// Usa la misma lógica que catalogo.php (includes/catalogo_consulta.php).
//
// Cómo se usa (solo método GET, porque únicamente lee datos):
//   api/productos.php?q=nike&talla[]=41&marca[]=1&origen[]=2&precio_max=200000&orden=menor
//       200 OK          → lista de productos que cumplen los filtros (puede estar vacía)
//       400 Bad Request → algún filtro es inválido (por ejemplo, una talla que no existe)
//   api/productos.php?id=3
//       200 OK          → un solo producto
//       400 Bad Request → el id no es un número entero mayor que 0
//       404 Not Found   → no existe un producto visible con ese id
//   Cualquier método que no sea GET → 405 Method Not Allowed
//   Error de la base de datos       → 500 Internal Server Error
//
// Formato de la respuesta:
//   Éxito: {"status": "success", "total": 2, "data": [ {...}, {...} ]}
//   Error: {"status": "error", "message": "...", "errores": {...}}

const RESPUESTA_JSON = true; // conexion.php también responde sus errores en JSON

// 1. Encabezado HTTP: avisa al navegador que la respuesta es JSON (no HTML).
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/catalogo_consulta.php';

// Envía el código de estado HTTP y los datos convertidos a JSON, y termina.
// JSON_UNESCAPED_UNICODE: deja "Running" o "Córdoba" legibles (sin \u00f3).
// JSON_UNESCAPED_SLASHES: deja "img/nike.jpg" en lugar de "img\/nike.jpg".
function responder_json(int $codigo, array $datos): never
{
    http_response_code($codigo);
    echo json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

// Convierte una fila de la base al formato del JSON, con los tipos correctos:
// PDO entrega el id y el precio como texto ("189900.00") y JSON los quiere
// como números (189900). También agrega el texto de los talles ya armado.
function producto_a_json(array $producto): array
{
    return [
        'id'        => (int) $producto['id'],
        'nombre'    => $producto['nombre'],
        'precio'    => (float) $producto['precio'],
        'imagen'    => $producto['imagen'],
        'alt'       => $producto['alt'],
        'categoria' => $producto['categoria'],
        'origen'    => $producto['origen'],
        'talles'    => catalogo_texto_talles($producto),
    ];
}

// 2. Solo se acepta GET (y HEAD, que es un GET sin cuerpo): este endpoint solo lee.
if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'HEAD'], true)) {
    header('Allow: GET, HEAD');
    responder_json(405, [
        'status'  => 'error',
        'message' => 'Método no permitido: este endpoint solo acepta GET.',
    ]);
}

try {
    // 3. Un solo producto: ?id=N
    if (isset($_GET['id'])) {
        $id = filter_var(dato_get('id'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($id === false) {
            responder_json(400, [
                'status'  => 'error',
                'message' => 'El id debe ser un número entero mayor que 0.',
            ]);
        }

        $productos = catalogo_buscar($pdo, [], $id);
        if (empty($productos)) {
            responder_json(404, [
                'status'  => 'error',
                'message' => 'Producto no encontrado.',
            ]);
        }

        responder_json(200, [
            'status' => 'success',
            'data'   => producto_a_json($productos[0]),
        ]);
    }

    // 4. Lista filtrada: se validan los filtros con las mismas reglas del catálogo.
    //    A diferencia de la página (que ignora el filtro y muestra un aviso),
    //    la API es estricta: un filtro inválido es un error del cliente (400).
    [$marcas, $origenes] = catalogo_maestros($pdo);
    [$filtros, $errores]  = catalogo_leer_filtros($marcas, $origenes);

    if (!empty($errores)) {
        responder_json(400, [
            'status'  => 'error',
            'message' => 'Hay filtros inválidos en la búsqueda.',
            'errores' => $errores,
        ]);
    }

    $productos = catalogo_buscar($pdo, $filtros);

    responder_json(200, [
        'status' => 'success',
        'total'  => count($productos),
        'data'   => array_map('producto_a_json', $productos),
    ]);
} catch (PDOException $e) {
    // El detalle técnico va al log del servidor; al cliente, un mensaje genérico.
    error_log('Error en api/productos.php: ' . $e->getMessage());
    responder_json(500, [
        'status'  => 'error',
        'message' => 'Error al consultar la base de datos.',
    ]);
}