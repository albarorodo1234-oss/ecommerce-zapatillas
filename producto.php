<?php
// producto.php
// Detalle de un producto según el ?id= de la URL.
// TP10 (MVC): este archivo ya no tiene consultas ni HTML. Es el punto de
// entrada de la URL producto.php?id=N (cumple el papel de una "ruta").
//   Modelo:      app/Models/Producto.php      (detalle, stock y relacionados)
//   Controlador: app/Controllers/ProductoController.php → show()
//   Vista:       app/Views/productos/show.php (HTML del detalle o "no encontrado")

require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/app/Controllers/ProductoController.php';

(new ProductoController($pdo))->show();