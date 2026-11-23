<?php
// app/Models/Producto.php
// TP10 (MVC): MODELO de productos.
// Es la única parte del módulo que habla con la base de datos: todas las
// consultas preparadas (PDO) del catálogo, del detalle y de la API están aquí.
// También aplica las reglas del negocio (qué filtros son válidos).
// No lee $_GET ni imprime HTML: recibe datos y devuelve arreglos.
// Lo usan dos controladores: ProductoController (páginas HTML) y
// ProductoApiController (respuestas JSON).
// Reemplaza a includes/catalogo_consulta.php (TP9) y a las consultas que
// estaban escritas dentro de producto.php (TP7).

class Producto
{
    // ----- Valores permitidos para los filtros -----
    public const TALLAS        = [38, 39, 40, 41, 42, 43, 44];
    public const PRECIO_MINIMO = 50000;
    public const PRECIO_MAXIMO = 300000;
    public const PRECIO_PASO   = 10000;
    public const BUSQUEDA_MAX  = 60; // caracteres

    // Orden del listado: el ORDER BY no admite :parametros, por eso cada
    // opción permitida se traduce a un fragmento SQL fijo (lista blanca).
    private const ORDENES = [
        'menor' => 'p.precio ASC',
        'mayor' => 'p.precio DESC',
    ];

    // El Modelo recibe la conexión ($pdo de config/conexion.php) al crearse
    // y la guarda en $this->pdo para usarla en todas sus consultas.
    public function __construct(private PDO $pdo)
    {
    }

    // Marcas y orígenes de las tablas maestras, como [id => nombre].
    // Devuelve [$marcas, $origenes]. Si la base falla, lanza una
    // PDOException: decide qué mostrar el controlador que lo llama.
    public function marcasYOrigenes(): array
    {
        $stmt = $this->pdo->prepare('SELECT id, nombre FROM marcas ORDER BY nombre');
        $stmt->execute();
        $marcas = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        $stmt = $this->pdo->prepare('SELECT id, nombre FROM origenes ORDER BY nombre');
        $stmt->execute();
        $origenes = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        return [$marcas, $origenes];
    }

    // Valida los filtros (las mismas reglas del TP5 y del TP9).
    // $entrada: los datos que recibió el controlador (normalmente $_GET).
    // Devuelve [$filtros, $errores]:
    //   $filtros: solo los valores válidos, listos para buscar().
    //   $errores: un aviso por cada filtro inválido (clave = nombre del filtro).
    public function validarFiltros(array $entrada, array $marcas, array $origenes): array
    {
        $errores = [];

        // Búsqueda de texto (viene del buscador del menú).
        $busqueda = self::texto($entrada, 'q');
        if (mb_strlen($busqueda) > self::BUSQUEDA_MAX) {
            $busqueda = mb_substr($busqueda, 0, self::BUSQUEDA_MAX);
            $errores['q'] = 'La búsqueda admite hasta ' . self::BUSQUEDA_MAX . ' caracteres.';
        }

        // Tallas: cada valor debe ser un número entero de la lista permitida.
        $tallas = [];
        foreach (self::lista($entrada, 'talla') as $valor) {
            $talla = filter_var(trim($valor), FILTER_VALIDATE_INT);
            if ($talla !== false && in_array($talla, self::TALLAS, true)) {
                $tallas[] = $talla;
            } else {
                $errores['talla'] = 'Una de las tallas elegidas no existe.';
            }
        }

        // Marcas: cada valor debe ser el id de una marca de la base.
        $marcas_elegidas = [];
        foreach (self::lista($entrada, 'marca') as $valor) {
            $valor = trim($valor);
            if (ctype_digit($valor) && isset($marcas[(int) $valor])) {
                $marcas_elegidas[] = (int) $valor;
            } else {
                $errores['marca'] = 'Una de las marcas elegidas no existe.';
            }
        }

        // Orígenes: cada valor debe ser el id de un origen de la base.
        $origenes_elegidos = [];
        foreach (self::lista($entrada, 'origen') as $valor) {
            $valor = trim($valor);
            if (ctype_digit($valor) && isset($origenes[(int) $valor])) {
                $origenes_elegidos[] = (int) $valor;
            } else {
                $errores['origen'] = 'Uno de los orígenes elegidos no existe.';
            }
        }

        // Precio máximo: un entero entre el mínimo y el máximo del deslizador.
        // null = no se filtra por precio.
        $precio_max = null;
        $precio_recibido = self::texto($entrada, 'precio_max');
        if ($precio_recibido !== '') {
            $precio_valido = filter_var($precio_recibido, FILTER_VALIDATE_INT, [
                'options' => ['min_range' => self::PRECIO_MINIMO, 'max_range' => self::PRECIO_MAXIMO],
            ]);
            if ($precio_valido === false) {
                $errores['precio_max'] = 'El precio máximo está fuera del rango permitido.';
            } else {
                $precio_max = $precio_valido;
            }
        }

        // Orden: solo se acepta una clave de la lista blanca.
        $orden = self::texto($entrada, 'orden');
        if ($orden !== '' && !isset(self::ORDENES[$orden])) {
            $orden = '';
            $errores['orden'] = 'El orden elegido no existe.';
        }

        $filtros = [
            'q'          => $busqueda,
            'tallas'     => $tallas,
            'marcas'     => $marcas_elegidas,
            'origenes'   => $origenes_elegidos,
            'precio_max' => $precio_max,
            'orden'      => $orden,
        ];

        return [$filtros, $errores];
    }

    // READ: listado del catálogo con los filtros ya validados.
    // Con $id (opcional) busca un solo producto: lo usa buscarPorId().
    // Devuelve una lista de productos (vacía si no hay coincidencias).
    public function buscar(array $filtros, ?int $id = null): array
    {
        // Solo productos visibles. Cada filtro activo agrega una condición.
        $condiciones = ['p.activo = 1'];
        $parametros  = [];

        if ($id !== null) {
            $condiciones[]     = 'p.id = :id';
            $parametros[':id'] = $id;
        }
        if (($filtros['q'] ?? '') !== '') {
            // Se escapan % y _ para que se busquen como texto y no como comodines de LIKE.
            // Cada marcador se usa una sola vez (requisito de las consultas preparadas nativas).
            $texto = '%' . addcslashes($filtros['q'], '%_\\') . '%';
            $condiciones[] = '(p.nombre LIKE :q_nombre OR m.nombre LIKE :q_marca OR c.nombre LIKE :q_categoria)';
            $parametros[':q_nombre']    = $texto;
            $parametros[':q_marca']     = $texto;
            $parametros[':q_categoria'] = $texto;
        }
        if (!empty($filtros['marcas'])) {
            $condiciones[] = 'p.marca_id IN (' . self::marcadoresIn('marca', $filtros['marcas'], $parametros) . ')';
        }
        if (!empty($filtros['origenes'])) {
            $condiciones[] = 'p.origen_id IN (' . self::marcadoresIn('origen', $filtros['origenes'], $parametros) . ')';
        }
        if (!empty($filtros['tallas'])) {
            // La talla está en producto_talle (stock por talle): el producto
            // entra si tiene stock en alguna de las tallas elegidas.
            $condiciones[] = 'EXISTS (SELECT 1 FROM producto_talle AS pt
                                      WHERE pt.producto_id = p.id
                                        AND pt.stock > 0
                                        AND pt.talle IN (' . self::marcadoresIn('talla', $filtros['tallas'], $parametros) . '))';
        }
        if (($filtros['precio_max'] ?? null) !== null) {
            $condiciones[] = 'p.precio <= :precio_max';
            $parametros[':precio_max'] = $filtros['precio_max'];
        }

        $sql = 'SELECT p.id, p.nombre, p.precio, p.imagen, p.alt,
                       c.nombre AS categoria, o.nombre AS origen,
                       (SELECT MIN(pt.talle) FROM producto_talle AS pt
                         WHERE pt.producto_id = p.id AND pt.stock > 0) AS talle_min,
                       (SELECT MAX(pt.talle) FROM producto_talle AS pt
                         WHERE pt.producto_id = p.id AND pt.stock > 0) AS talle_max
                FROM productos AS p
                INNER JOIN marcas     AS m ON p.marca_id     = m.id
                INNER JOIN categorias AS c ON p.categoria_id = c.id
                INNER JOIN origenes   AS o ON p.origen_id    = o.id
                WHERE ' . implode(' AND ', $condiciones) . '
                ORDER BY ' . (self::ORDENES[$filtros['orden'] ?? ''] ?? 'p.id ASC');

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($parametros);
        return $stmt->fetchAll();
    }

    // READ: un producto visible con los mismos datos del listado (lo usa la API).
    // Devuelve el producto o null si no existe o está oculto.
    public function buscarPorId(int $id): ?array
    {
        return $this->buscar([], $id)[0] ?? null;
    }

    // READ: detalle completo de un producto visible, con los nombres de su
    // marca, categoría y origen (página producto.php). null si no existe.
    public function detalle(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT p.id, p.nombre, p.descripcion, p.precio, p.imagen, p.alt, p.categoria_id,
                    m.nombre AS marca, c.nombre AS categoria, o.nombre AS origen
             FROM productos AS p
             INNER JOIN marcas     AS m ON p.marca_id     = m.id
             INNER JOIN categorias AS c ON p.categoria_id = c.id
             INNER JOIN origenes   AS o ON p.origen_id    = o.id
             WHERE p.id = :id AND p.activo = 1'
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    // READ: stock por talle de un producto, como [talle => stock].
    public function stockPorTalle(int $id): array
    {
        $stmt = $this->pdo->prepare('SELECT talle, stock FROM producto_talle WHERE producto_id = :id ORDER BY talle');
        $stmt->execute([':id' => $id]);
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    // READ: hasta 3 productos visibles distintos de $id,
    // primero los de la misma categoría.
    public function relacionados(int $id, int $categoria_id): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT p.id, p.nombre, p.precio, p.imagen, p.alt,
                    c.nombre AS categoria, o.nombre AS origen
             FROM productos AS p
             INNER JOIN categorias AS c ON p.categoria_id = c.id
             INNER JOIN origenes   AS o ON p.origen_id    = o.id
             WHERE p.activo = 1 AND p.id <> :id
             ORDER BY (p.categoria_id = :categoria_id) DESC, p.id ASC
             LIMIT 3'
        );
        $stmt->execute([':id' => $id, ':categoria_id' => $categoria_id]);
        return $stmt->fetchAll();
    }

    // Texto de los talles con stock de un producto del listado:
    // "ARG 38–40", "ARG 41" o, si no tiene stock cargado, "Consultar talle".
    public static function textoTalles(array $producto): string
    {
        if ($producto['talle_min'] === null) {
            return 'Consultar talle';
        }
        if ($producto['talle_min'] == $producto['talle_max']) {
            return 'ARG ' . $producto['talle_min'];
        }
        return 'ARG ' . $producto['talle_min'] . '–' . $producto['talle_max'];
    }

    // ----- Ayudantes internos (private: solo los usa esta clase) -----

    // Un campo de texto simple de $entrada, sin espacios a los costados ('' si no llega).
    private static function texto(array $entrada, string $campo): string
    {
        return isset($entrada[$campo]) && is_string($entrada[$campo]) ? trim($entrada[$campo]) : '';
    }

    // Un campo con varios valores (name="marca[]"): solo los que son texto.
    private static function lista(array $entrada, string $campo): array
    {
        if (!isset($entrada[$campo]) || !is_array($entrada[$campo])) {
            return [];
        }
        return array_values(array_filter($entrada[$campo], 'is_string'));
    }

    // Arma marcadores (:prefijo0, :prefijo1, ...) para un IN (...) y agrega
    // cada valor a $parametros. Así ningún dato se escribe dentro del SQL.
    private static function marcadoresIn(string $prefijo, array $valores, array &$parametros): string
    {
        $marcadores = [];
        foreach ($valores as $i => $valor) {
            $marcador              = ':' . $prefijo . $i;
            $marcadores[]          = $marcador;
            $parametros[$marcador] = $valor;
        }
        return implode(', ', $marcadores);
    }
}