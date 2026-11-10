<?php
// config/env.php
// Lee el archivo .env de la raíz del proyecto y crea una constante
// por cada línea (APP_NAME, APP_EMAIL, APP_ENV).

$ruta_env = __DIR__ . '/../.env';

if (!file_exists($ruta_env)) {
    die('Falta el archivo .env. Copiá .env.example como .env y completá los valores.');
}

$lineas = file($ruta_env, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach ($lineas as $linea) {
    $linea = trim($linea);

    // Ignorar comentarios (líneas que empiezan con #) y líneas sin "="
    if (str_starts_with($linea, '#') || !str_contains($linea, '=')) {
        continue;
    }

    // Separar "CLAVE=valor" en dos partes
    [$clave, $valor] = explode('=', $linea, 2);
    $clave = trim($clave);
    $valor = trim($valor, " \t\"'");

    if (!defined($clave)) {
        define($clave, $valor);
    }
}