<?php
// includes/catalogo_consulta.php
// TP9: lógica del catálogo compartida por dos archivos:
//   - catalogo.php      (la página HTML que ve el visitante)
//   - api/productos.php (el endpoint que responde JSON a JavaScript)
// Antes estaba escrita dentro de catalogo.php (TP5 y TP7). Se movió aquí
// para no repetirla: si mañana cambia un filtro, se cambia en un solo lugar.
// Contiene los valores permitidos de los filtros, la lectura de marcas y
// orígenes, la validación de los filtros que llegan por GET y la consulta
// preparada. No imprime nada: solo devuelve datos.

require_once __DIR__ . '/funciones.php';

// ----- Valores permitidos para los filtros -----
const CATALOGO_TALLAS        = [38, 39, 40, 41, 42, 43, 44];
const CATALOGO_PRECIO_MINIMO = 50000;
const CATALOGO_PRECIO_MAXIMO = 300000;
const CATALOGO_PRECIO_PASO   = 10000;
const CATALOGO_BUSQUEDA_MAX  = 60; // caracteres

// Orden del listado: el ORDER BY no admite :parametros, por eso cada
// opción permitida se traduce a un fragmento SQL fijo (lista blanca).
const CATALOGO_ORDENES = [
    'menor' => 'p.precio ASC',
    'mayor' => 'p.precio DESC',
];

// Lee las marcas y los orígenes de las tablas maestras, como [id => nombre].
// Si la base falla, lanza una PDOException: decide qué mostrar quien la llama.
function catalogo_maestros(PDO $pdo): array
{
    $stmt = $pdo->prepare('SELECT id, nombre FROM marcas ORDER BY nombre');
    $stmt->execute();
    $marcas = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    $stmt = $pdo->prepare('SELECT id, nombre FROM origenes ORDER BY nombre');
    $stmt->execute();
    $origenes = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    return [$marcas, $origenes];
}

// Lee y valida los filtros que llegan por GET (las mismas reglas del TP5).
// Devuelve dos cosas: [$filtros, $errores]
//   $filtros: solo los valores válidos, listos para catalogo_buscar().
//   $errores: un aviso por cada filtro inválido (clave = nombre del filtro).
// catalogo.php muestra los avisos y sigue; la API responde 400 Bad Request.
function catalogo_leer_filtros(array $marcas, array $origenes): array
{
    $errores = [];

    // Búsqueda de texto (viene del buscador del menú).
    $busqueda = dato_get('q');
    if (mb_strlen($busqueda) > CATALOGO_BUSQUEDA_MAX) {
        $busqueda = mb_substr($busqueda, 0, CATALOGO_BUSQUEDA_MAX);
        $errores['q'] = 'La búsqueda admite hasta ' . CATALOGO_BUSQUEDA_MAX . ' caracteres.';
    }

    // Tallas: cada valor debe ser un número entero de la lista permitida.
    $tallas = [];
    foreach (dato_get_lista('talla') as $valor) {
        $talla = filter_var(trim($valor), FILTER_VALIDATE_INT);
        if ($talla !== false && in_array($talla, CATALOGO_TALLAS, true)) {
            $tallas[] = $talla;
        } else {
            $errores['talla'] = 'Una de las tallas elegidas no existe.';
        }
    }

    // Marcas: cada valor debe ser el id de una marca de la base.
    $marcas_elegidas = [];
    foreach (dato_get_lista('marca') as $valor) {
        $valor = trim($valor);
        if (ctype_digit($valor) && isset($marcas[(int) $valor])) {
            $marcas_elegidas[] = (int) $valor;
        } else {
            $errores['marca'] = 'Una de las marcas elegidas no existe.';
        }
    }

    // Orígenes: cada valor debe ser el id de un origen de la base.
    $origenes_elegidos = [];
    foreach (dato_get_lista('origen') as $valor) {
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
    $precio_recibido = dato_get('precio_max');
    if ($precio_recibido !== '') {
        $precio_valido = filter_var($precio_recibido, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => CATALOGO_PRECIO_MINIMO, 'max_range' => CATALOGO_PRECIO_MAXIMO],
        ]);
        if ($precio_valido === false) {
            $errores['precio_max'] = 'El precio máximo está fuera del rango permitido.';
        } else {
            $precio_max = $precio_valido;
        }
    }

    // Orden: solo se acepta una clave de la lista blanca.
    $orden = dato_get('orden');
    if ($orden !== '' && !isset(CATALOGO_ORDENES[$orden])) {
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

// Arma una lista de marcadores (:prefijo0, :prefijo1, ...) para un IN (...)
// y agrega cada valor a $parametros. Así ningún dato se escribe dentro del SQL.
function catalogo_marcadores_in(string $prefijo, array $valores, array &$parametros): string
{
    $marcadores = [];
    foreach ($valores as $i => $valor) {
        $marcador              = ':' . $prefijo . $i;
        $marcadores[]          = $marcador;
        $parametros[$marcador] = $valor;
    }
    return implode(', ', $marcadores);
}

// READ: consulta preparada con los filtros ya validados.
// Con $id (opcional) busca un solo producto: lo usa la API en ?id=.
// Devuelve una lista de productos (vacía si no hay coincidencias).
function catalogo_buscar(PDO $pdo, array $filtros, ?int $id = null): array
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
        $condiciones[] = 'p.marca_id IN (' . catalogo_marcadores_in('marca', $filtros['marcas'], $parametros) . ')';
    }
    if (!empty($filtros['origenes'])) {
        $condiciones[] = 'p.origen_id IN (' . catalogo_marcadores_in('origen', $filtros['origenes'], $parametros) . ')';
    }
    if (!empty($filtros['tallas'])) {
        // La talla está en producto_talle (stock por talle): el producto
        // entra si tiene stock en alguna de las tallas elegidas.
        $condiciones[] = 'EXISTS (SELECT 1 FROM producto_talle AS pt
                                  WHERE pt.producto_id = p.id
                                    AND pt.stock > 0
                                    AND pt.talle IN (' . catalogo_marcadores_in('talla', $filtros['tallas'], $parametros) . '))';
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
            ORDER BY ' . (CATALOGO_ORDENES[$filtros['orden'] ?? ''] ?? 'p.id ASC');

    $stmt = $pdo->prepare($sql);
    $stmt->execute($parametros);
    return $stmt->fetchAll();
}

// Texto de los talles con stock de un producto: "ARG 38–40", "ARG 41"
// o, si no tiene stock cargado, "Consultar talle".
function catalogo_texto_talles(array $producto): string
{
    if ($producto['talle_min'] === null) {
        return 'Consultar talle';
    }
    if ($producto['talle_min'] == $producto['talle_max']) {
        return 'ARG ' . $producto['talle_min'];
    }
    return 'ARG ' . $producto['talle_min'] . '–' . $producto['talle_max'];
}