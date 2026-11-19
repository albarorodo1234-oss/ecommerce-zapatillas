<?php
// config/conexion.php
// Crea la conexión a la base de datos con PDO y la guarda en la variable $pdo.
// Las credenciales NO están escritas aquí: se leen del archivo .env
// (ignorado por Git) a través de config/env.php.
// Uso: require_once __DIR__ . '/../config/conexion.php'; (según la carpeta)

require_once __DIR__ . '/env.php';

// 1. Comprobar que el .env tenga todas las claves de la base de datos.
foreach (['DB_HOST', 'DB_PORT', 'DB_NAME', 'DB_USER', 'DB_PASS'] as $clave) {
    if (!defined($clave)) {
        die("Falta la clave $clave en el archivo .env. Revisá .env.example.");
    }
}

// 2. DSN (Data Source Name): indica el motor (mysql), el servidor, el puerto,
//    la base y el juego de caracteres (utf8mb4 = admite tildes, eñes y emojis).
$dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';

// 3. Opciones de seguridad y comodidad de PDO.
$opciones = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Ante un error, lanza una excepción
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Devuelve filas como arreglos asociativos
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Consultas preparadas reales del motor
];

// 4. Intentar conectar. Si falla, se registra el detalle técnico en el log
//    del servidor y al visitante solo se le muestra un mensaje genérico,
//    para no revelar datos internos (usuario, servidor, nombre de la base).
try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $opciones);
} catch (PDOException $e) {
    error_log('Error de conexión a la base de datos: ' . $e->getMessage());
    http_response_code(500);
    die('Error crítico: no se pudo establecer la conexión con la base de datos.');
}