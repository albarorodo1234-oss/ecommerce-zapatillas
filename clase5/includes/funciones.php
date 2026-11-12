<?php
// includes/funciones.php
// Funciones auxiliares para trabajar de forma segura con los datos de los formularios.

// Escapa un valor para imprimirlo en HTML (prevención de XSS).
// Se usa justo antes de mostrar en pantalla cualquier dato que venga del usuario.
function e(mixed $texto): string
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}

// Lee un campo enviado por POST.
// Si el campo no existe o no es un texto simple, devuelve '' (cadena vacía).
// Si existe, le quita los espacios del principio y del final con trim().
function dato_post(string $campo): string
{
    return isset($_POST[$campo]) && is_string($_POST[$campo]) ? trim($_POST[$campo]) : '';
}

// Lo mismo que dato_post(), pero para datos enviados por GET (en la URL).
function dato_get(string $campo): string
{
    return isset($_GET[$campo]) && is_string($_GET[$campo]) ? trim($_GET[$campo]) : '';
}