<?php
// catalogo.php
// Catálogo público de productos.
// TP10 (MVC): este archivo ya no tiene consultas ni HTML. Es el punto de
// entrada de la URL catalogo.php (cumple el papel de una "ruta"): conecta
// con la base y le pasa la petición al controlador.
//   Modelo:      app/Models/Producto.php       (consultas PDO y validación de filtros)
//   Controlador: app/Controllers/ProductoController.php → index()
//   Vista:       app/Views/productos/index.php (HTML del catálogo)
// Sigue funcionando sin JavaScript; con JavaScript, js/main.js pide los
// productos a api/productos.php y actualiza el listado sin recargar (TP9).

require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/app/Controllers/ProductoController.php';

(new ProductoController($pdo))->index();