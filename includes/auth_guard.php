<?php
// includes/auth_guard.php
// Guardián de rutas (TP8). Se incluye al principio de cada página protegida,
// antes de su lógica y antes del header:
//   1. Autenticación: si no hay sesión iniciada, redirige a
//      login.php?error=acceso_denegado.
//   2. Autorización: la página puede llamar a verificarRol('Administrador')
//      para que solo entre ese rol; a los demás les muestra un error 403.
// Las páginas de una subcarpeta (como admin/) definen $ruta_base = '../'
// ANTES de incluirlo, para que la redirección apunte bien a login.php.

require_once __DIR__ . '/sesion.php';

// 1. Autenticación: ¿hay un usuario con la sesión iniciada?
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . ($ruta_base ?? '') . 'login.php?error=acceso_denegado');
    exit; // Detiene el script: la página protegida no se ejecuta.
}

// 2. Autorización por rol: corta con un 403 (Prohibido) si el rol del
//    usuario no es el requerido.
function verificarRol(string $rolRequerido): void
{
    if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== $rolRequerido) {
        http_response_code(403);
        global $ruta_base; // La página 403 usa el $ruta_base de la página protegida.
        require __DIR__ . '/prohibido.php';
        exit;
    }
}