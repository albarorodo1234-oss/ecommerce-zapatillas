<?php
// admin/producto_comun.php
// Funciones compartidas por crear.php y editar.php (CRUD de productos):
// listas de opciones, lectura y validación del formulario, parámetros
// para las consultas preparadas y ayudas para mostrar los errores.
// Este archivo solo define funciones: si se abre directo, no muestra nada.

// Producto "vacío": valores iniciales del formulario de alta.
function producto_vacio(): array
{
    return [
        'nombre'       => '',
        'marca_id'     => '',
        'categoria_id' => '',
        'origen_id'    => '',
        'precio'       => '',
        'descripcion'  => '',
        'imagen'       => '',
        'alt'          => '',
        'activo'       => 1,
    ];
}

// Opciones de las listas desplegables, leídas de las tablas maestras.
// Devuelve, por ejemplo, ['marcas' => [1 => 'Nike', 2 => 'Adidas', ...], ...].
function obtener_opciones(PDO $pdo): array
{
    // Los nombres de tabla no pueden ir como parámetros (:tabla) en una
    // consulta preparada, por eso se recorren desde esta lista fija:
    // nunca vienen del usuario.
    $tablas   = ['marcas', 'categorias', 'origenes'];
    $opciones = [];

    foreach ($tablas as $tabla) {
        $stmt = $pdo->prepare("SELECT id, nombre FROM $tabla ORDER BY nombre");
        $stmt->execute();
        // FETCH_KEY_PAIR arma un arreglo [id => nombre].
        $opciones[$tabla] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    return $opciones;
}

// Imágenes disponibles en la carpeta img/ (jpg, jpeg, png o webp).
// Devuelve ['img/archivo.jpg' => 'archivo.jpg', ...].
function imagenes_disponibles(): array
{
    $imagenes = [];
    foreach (scandir(__DIR__ . '/../img') as $archivo) {
        $extension = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            $imagenes['img/' . $archivo] = $archivo;
        }
    }
    return $imagenes;
}

// Lee los campos del formulario enviados por POST (con isset y trim).
function leer_producto_post(): array
{
    return [
        'nombre'       => dato_post('nombre'),
        'marca_id'     => dato_post('marca_id'),
        'categoria_id' => dato_post('categoria_id'),
        'origen_id'    => dato_post('origen_id'),
        'precio'       => dato_post('precio'),
        'descripcion'  => dato_post('descripcion'),
        'imagen'       => dato_post('imagen'),
        'alt'          => dato_post('alt'),
        // Una casilla sin marcar no se envía: si no llega, el producto queda oculto.
        'activo'       => isset($_POST['activo']) ? 1 : 0,
    ];
}

// true si $valor es un número entero que existe como id en la lista.
function opcion_valida(string $valor, array $lista): bool
{
    return ctype_digit($valor) && isset($lista[(int) $valor]);
}

// Valida los datos en el servidor. Devuelve los errores: [campo => mensaje].
function validar_producto(array $datos, array $opciones, array $imagenes): array
{
    $errores = [];

    if ($datos['nombre'] === '') {
        $errores['nombre'] = 'El nombre es obligatorio.';
    } elseif (mb_strlen($datos['nombre']) > 100) {
        $errores['nombre'] = 'El nombre no puede superar los 100 caracteres.';
    }

    if (!opcion_valida($datos['marca_id'], $opciones['marcas'])) {
        $errores['marca_id'] = 'Elegí una marca de la lista.';
    }
    if (!opcion_valida($datos['categoria_id'], $opciones['categorias'])) {
        $errores['categoria_id'] = 'Elegí una categoría de la lista.';
    }
    if (!opcion_valida($datos['origen_id'], $opciones['origenes'])) {
        $errores['origen_id'] = 'Elegí un origen de la lista.';
    }

    // Precio: número entre 1.000 y 99.999.999,99 (máximo de DECIMAL(10,2)).
    // El mínimo evita un error común: escribir "189.900" con punto de miles,
    // que PHP leería como 189,9.
    $precio = filter_var($datos['precio'], FILTER_VALIDATE_FLOAT);
    if ($datos['precio'] === '') {
        $errores['precio'] = 'El precio es obligatorio.';
    } elseif ($precio === false || $precio < 1000 || $precio > 99999999.99) {
        $errores['precio'] = 'Ingresá un precio entre 1000 y 99999999, sin puntos de miles (por ejemplo, 189900).';
    }

    if (mb_strlen($datos['descripcion']) > 1000) {
        $errores['descripcion'] = 'La descripción no puede superar los 1000 caracteres.';
    }

    // La imagen es opcional, pero si se elige debe ser una de la carpeta img/.
    if ($datos['imagen'] !== '' && !isset($imagenes[$datos['imagen']])) {
        $errores['imagen'] = 'Elegí una imagen de la lista.';
    }

    // Accesibilidad: si hay imagen, el texto alternativo es obligatorio.
    if ($datos['imagen'] !== '' && $datos['alt'] === '') {
        $errores['alt'] = 'Describí la imagen (texto alternativo para lectores de pantalla).';
    } elseif (mb_strlen($datos['alt']) > 255) {
        $errores['alt'] = 'El texto alternativo no puede superar los 255 caracteres.';
    }

    return $errores;
}

// Arma los valores para los :parametros del INSERT y del UPDATE.
// Los campos opcionales vacíos se guardan como NULL en la base.
function parametros_producto(array $datos): array
{
    return [
        ':marca_id'     => (int) $datos['marca_id'],
        ':categoria_id' => (int) $datos['categoria_id'],
        ':origen_id'    => (int) $datos['origen_id'],
        ':nombre'       => $datos['nombre'],
        ':descripcion'  => $datos['descripcion'] !== '' ? $datos['descripcion'] : null,
        ':precio'       => number_format((float) $datos['precio'], 2, '.', ''),
        ':imagen'       => $datos['imagen'] !== '' ? $datos['imagen'] : null,
        ':alt'          => $datos['alt'] !== '' ? $datos['alt'] : null,
        ':activo'       => $datos['activo'] ? 1 : 0,
    ];
}

// ----- Ayudas para mostrar los errores en el formulario (Bootstrap) -----

// Devuelve " is-invalid" (borde rojo) si el campo tiene error.
function clase_invalida(array $errores, string $campo): string
{
    return isset($errores[$campo]) ? ' is-invalid' : '';
}

// Atributos de accesibilidad que vinculan el campo con su mensaje de error.
function atributos_error(array $errores, string $campo): string
{
    return isset($errores[$campo])
        ? 'aria-invalid="true" aria-describedby="error-' . e($campo) . '"'
        : '';
}

// Imprime el mensaje de error del campo (si tiene).
function mostrar_error(array $errores, string $campo): void
{
    if (isset($errores[$campo])) {
        echo '<div class="invalid-feedback" id="error-' . e($campo) . '">' . e($errores[$campo]) . '</div>';
    }
}