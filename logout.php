<?php
// logout.php
// Cierre de sesión (TP8): vacía $_SESSION, borra la cookie de sesión del
// navegador, destruye la sesión en el servidor y vuelve al login.
// No muestra ninguna página.

require_once __DIR__ . '/includes/sesion.php';

// 1. Unset de todas las variables de sesión.
$_SESSION = [];

// 2. Borrar la cookie PHPSESSID del navegador: se reenvía vacía, con una
//    fecha de vencimiento en el pasado y los mismos parámetros con que se creó.
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', [
        'expires'  => time() - 42000,
        'path'     => $params['path'],
        'domain'   => $params['domain'],
        'secure'   => $params['secure'],
        'httponly' => $params['httponly'],
        'samesite' => $params['samesite'],
    ]);
}

// 3. Destruir la sesión en el servidor.
session_destroy();

// 4. Volver al login con un aviso.
header('Location: login.php?status=logout');
exit;