<?php
// app/Controllers/ProductoController.php
// TP10 (MVC): CONTROLADOR de las páginas de productos.
// Es el intermediario: recibe la petición (los datos de $_GET), le pide los
// datos al Modelo (Producto) y elige la Vista que arma el HTML, pasándole
// esas variables. No escribe SQL ni HTML.
//   index() → catálogo con filtros        (lo llama catalogo.php)
//   show()  → detalle de un producto      (lo llama producto.php?id=N)

require_once __DIR__ . '/../Models/Producto.php';
require_once __DIR__ . '/../../includes/funciones.php'; // e(), dato_get()

class ProductoController
{
    private Producto $modelo;

    // Recibe la conexión y crea el Modelo que usarán sus métodos.
    public function __construct(PDO $pdo)
    {
        $this->modelo = new Producto($pdo);
    }

    // Catálogo: GET catalogo.php?q=...&talla[]=...&marca[]=...&origen[]=...&precio_max=...&orden=...
    public function index(): void
    {
        $marcas         = [];
        $origenes       = [];
        $productos      = [];
        $error_catalogo = false;

        // 1. Marcas y orígenes para los filtros (tablas maestras).
        try {
            [$marcas, $origenes] = $this->modelo->marcasYOrigenes();
        } catch (PDOException $e) {
            error_log('Error al leer los filtros del catálogo: ' . $e->getMessage());
            $error_catalogo = true;
        }

        // 2. El Modelo valida los filtros que llegaron por GET.
        [$filtros, $errores_filtros] = $this->modelo->validarFiltros($_GET, $marcas, $origenes);

        // 3. READ: productos que cumplen los filtros válidos.
        if (!$error_catalogo) {
            try {
                $productos = $this->modelo->buscar($filtros);
            } catch (PDOException $e) {
                error_log('Error al leer el catálogo: ' . $e->getMessage());
                $error_catalogo = true;
            }
        }

        // Se agrega a cada producto el texto de sus talles ("ARG 38–40"),
        // para que la Vista solo tenga que mostrarlo.
        foreach ($productos as $i => $producto) {
            $productos[$i]['texto_talles'] = Producto::textoTalles($producto);
        }

        // 4. Se elige la Vista y se le pasan los datos (mismos nombres que en el TP9).
        $this->render('productos/index', [
            'titulo_pagina'        => 'Catálogo de zapatillas importadas | SoleMate',
            'descripcion_pagina'   => 'Explorá el catálogo de SoleMate: zapatillas Nike, Jordan, Adidas y New Balance importadas de Chile y Bolivia. Filtrá por talla, precio, marca y origen.',
            'pagina_activa'        => 'catalogo',
            'productos_filtrados'  => $productos,
            'marcas_disponibles'   => $marcas,
            'origenes_disponibles' => $origenes,
            'error_catalogo'       => $error_catalogo,
            'errores_filtros'      => $errores_filtros,
            'busqueda'             => $filtros['q'],
            'tallas_elegidas'      => $filtros['tallas'],
            'marcas_elegidas'      => $filtros['marcas'],
            'origenes_elegidos'    => $filtros['origenes'],
            'orden'                => $filtros['orden'],
            'precio_tope'          => $filtros['precio_max'] ?? Producto::PRECIO_MAXIMO,
            'tallas_disponibles'   => Producto::TALLAS,
            'precio_minimo'        => Producto::PRECIO_MINIMO,
            'precio_maximo'        => Producto::PRECIO_MAXIMO,
            'precio_paso'          => Producto::PRECIO_PASO,
        ]);
    }

    // Detalle: GET producto.php?id=N
    // Si el id no es válido, no existe o el producto está oculto, la Vista
    // muestra "Producto no encontrado" con el código HTTP 404.
    public function show(): void
    {
        $producto     = null;
        $stock        = [];   // [talle => stock]
        $relacionados = [];

        // 1. El id debe ser un número entero mayor que 0.
        $id = filter_var(dato_get('id'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

        // 2. Se le piden al Modelo el producto, su stock y los relacionados.
        if ($id !== false) {
            try {
                $producto = $this->modelo->detalle($id);
                if ($producto) {
                    $stock        = $this->modelo->stockPorTalle($id);
                    $relacionados = $this->modelo->relacionados($id, $producto['categoria_id']);
                }
            } catch (PDOException $e) {
                error_log('Error al leer el producto: ' . $e->getMessage());
                $producto = null;
            }
        }

        // 3. Título y descripción de la pestaña según el producto.
        if ($producto) {
            $titulo      = $producto['nombre'] . ' | SoleMate';
            $descripcion = $producto['descripcion']
                ? mb_substr($producto['descripcion'], 0, 155)
                : $producto['nombre'] . ' importada de ' . $producto['origen'] . '. Zapatillas 100% auténticas en SoleMate.';
        } else {
            http_response_code(404); // Le avisa al navegador (y a los buscadores) que la página no existe
            $titulo      = 'Producto no encontrado | SoleMate';
            $descripcion = 'El producto que buscás no existe o ya no está disponible en SoleMate.';
        }

        // 4. Se elige la Vista y se le pasan los datos.
        $this->render('productos/show', [
            'titulo_pagina'      => $titulo,
            'descripcion_pagina' => $descripcion,
            'pagina_activa'      => 'catalogo',
            'producto'           => $producto,
            'stock'              => $stock,
            'relacionados'       => $relacionados,
            'tallas_disponibles' => Producto::TALLAS,
        ]);
    }

    // Carga una Vista de app/Views/ y le pasa los datos.
    // extract() convierte cada clave del arreglo en una variable:
    // ['producto' => ...] pasa a ser $producto dentro de la Vista.
    private function render(string $vista, array $datos): void
    {
        extract($datos);
        require __DIR__ . '/../Views/' . $vista . '.php';
    }
}