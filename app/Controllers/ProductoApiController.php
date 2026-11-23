<?php
// app/Controllers/ProductoApiController.php
// TP10 (MVC): CONTROLADOR de la API JSON de productos (la del TP9).
// Usa el mismo Modelo (Producto) que las páginas HTML: las consultas no se
// repiten. En una API la "vista" es la respuesta JSON, que arma responder().
//   index() → lista filtrada   (api/productos.php?q=...&talla[]=...)
//   show()  → un producto      (api/productos.php?id=N)
// Respuestas: 200 OK, 400 (filtro o id inválido), 404 (no existe),
// 405 (método distinto de GET) y 500 (error de la base).

require_once __DIR__ . '/../Models/Producto.php';
require_once __DIR__ . '/../../includes/funciones.php'; // dato_get()

class ProductoApiController
{
    private Producto $modelo;

    public function __construct(PDO $pdo)
    {
        $this->modelo = new Producto($pdo);
    }

    // Solo se acepta GET (y HEAD, que es un GET sin cuerpo): esta API solo lee.
    public function soloGet(): void
    {
        if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'HEAD'], true)) {
            header('Allow: GET, HEAD');
            $this->responder(405, [
                'status'  => 'error',
                'message' => 'Método no permitido: este endpoint solo acepta GET.',
            ]);
        }
    }

    // Lista filtrada. A diferencia de la página (que ignora el filtro y
    // muestra un aviso), la API es estricta: un filtro inválido es un
    // error del cliente (400).
    public function index(): void
    {
        try {
            [$marcas, $origenes] = $this->modelo->marcasYOrigenes();
            [$filtros, $errores]  = $this->modelo->validarFiltros($_GET, $marcas, $origenes);

            if (!empty($errores)) {
                $this->responder(400, [
                    'status'  => 'error',
                    'message' => 'Hay filtros inválidos en la búsqueda.',
                    'errores' => $errores,
                ]);
            }

            $productos = $this->modelo->buscar($filtros);

            $this->responder(200, [
                'status' => 'success',
                'total'  => count($productos),
                'data'   => array_map([$this, 'aJson'], $productos),
            ]);
        } catch (PDOException $e) {
            $this->errorBase($e);
        }
    }

    // Un solo producto: ?id=N
    public function show(): void
    {
        $id = filter_var(dato_get('id'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($id === false) {
            $this->responder(400, [
                'status'  => 'error',
                'message' => 'El id debe ser un número entero mayor que 0.',
            ]);
        }

        try {
            $producto = $this->modelo->buscarPorId($id);
        } catch (PDOException $e) {
            $this->errorBase($e);
        }

        if ($producto === null) {
            $this->responder(404, [
                'status'  => 'error',
                'message' => 'Producto no encontrado.',
            ]);
        }

        $this->responder(200, [
            'status' => 'success',
            'data'   => $this->aJson($producto),
        ]);
    }

    // Convierte una fila de la base al formato del JSON, con los tipos correctos:
    // PDO entrega el precio como texto ("189900.00") y JSON lo quiere como
    // número (189900). También agrega el texto de los talles ya armado.
    private function aJson(array $producto): array
    {
        return [
            'id'        => (int) $producto['id'],
            'nombre'    => $producto['nombre'],
            'precio'    => (float) $producto['precio'],
            'imagen'    => $producto['imagen'],
            'alt'       => $producto['alt'],
            'categoria' => $producto['categoria'],
            'origen'    => $producto['origen'],
            'talles'    => Producto::textoTalles($producto),
        ];
    }

    // Error de la base: el detalle técnico va al log del servidor;
    // al cliente, un mensaje genérico.
    private function errorBase(PDOException $e): never
    {
        error_log('Error en api/productos.php: ' . $e->getMessage());
        $this->responder(500, [
            'status'  => 'error',
            'message' => 'Error al consultar la base de datos.',
        ]);
    }

    // Envía el código de estado HTTP y los datos convertidos a JSON, y termina.
    // JSON_UNESCAPED_UNICODE: deja "Córdoba" legible (sin \u00f3).
    // JSON_UNESCAPED_SLASHES: deja "img/nike.jpg" en lugar de "img\/nike.jpg".
    private function responder(int $codigo, array $datos): never
    {
        http_response_code($codigo);
        echo json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}