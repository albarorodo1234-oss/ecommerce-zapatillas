<?php
// api/productos.php
// TP9: endpoint (API) del catálogo. No devuelve HTML, solo datos en formato
// JSON: js/main.js los pide con fetch() y arma las tarjetas sin recargar.
// TP10 (MVC): este archivo es solo el punto de entrada (la "ruta") de la API.
// La lógica está en app/Controllers/ProductoApiController.php, que usa el
// mismo Modelo que las páginas (app/Models/Producto.php).
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

// Encabezado HTTP: avisa al navegador que la respuesta es JSON (no HTML).
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../app/Controllers/ProductoApiController.php';

$api = new ProductoApiController($pdo);
$api->soloGet(); // 405 si el método no es GET ni HEAD

// Con ?id= se pide un producto; sin él, la lista filtrada.
if (isset($_GET['id'])) {
    $api->show();
} else {
    $api->index();
}