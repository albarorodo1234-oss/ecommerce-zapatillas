<?php
// includes/sesion.php
// Inicia (o reanuda) la sesión de PHP con una configuración segura de la cookie
// PHPSESSID (recomendaciones OWASP de la teoría del TP8).
// Se incluye con require_once desde header.php, login.php, logout.php y el
// guardián de rutas: así la configuración está escrita en un solo lugar.
// Debe incluirse ANTES de imprimir cualquier HTML, porque la sesión
// viaja en una cookie y las cookies se envían en los encabezados HTTP.

// Si la sesión ya está iniciada (otro archivo ya incluyó este), no se repite.
if (session_status() === PHP_SESSION_NONE) {
    // ¿La página llegó por HTTPS? En XAMPP (http://localhost) es false.
    $es_https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

    ini_set('session.cookie_httponly', 1);                // JavaScript no puede leer la cookie (XSS)
    ini_set('session.cookie_secure', $es_https ? 1 : 0); // Solo por HTTPS, cuando el sitio lo usa
    ini_set('session.use_only_cookies', 1);               // El id de sesión nunca viaja en la URL
    ini_set('session.use_strict_mode', 1);                // Rechaza ids de sesión inventados (fijación de sesión)
    ini_set('session.cookie_samesite', 'Lax');            // No se envía en formularios de otros sitios (CSRF)

    session_start();
}