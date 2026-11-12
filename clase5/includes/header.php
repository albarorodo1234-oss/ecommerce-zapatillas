<?php
// includes/header.php
// Plantilla común: <head>, metadatos, hojas de estilo y apertura del <body>.
// Cada página define ANTES de incluirlo:
//   $titulo_pagina       (título de la pestaña)
//   $descripcion_pagina  (opcional: descripción para buscadores)

require_once __DIR__ . '/../config/env.php';

// Si la página no define un valor, se usa uno por defecto
$titulo_pagina      = $titulo_pagina ?? APP_NAME;
$descripcion_pagina = $descripcion_pagina ?? 'Zapatillas importadas de Chile y Bolivia: Nike, Jordan, Adidas y New Balance. Envío rápido, 100% auténticas y cuotas sin interés.';
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= htmlspecialchars($titulo_pagina) ?></title>
    <meta name="description" content="<?= htmlspecialchars($descripcion_pagina) ?>" />
    <meta name="theme-color" content="#d43f00" />
    <link
      rel="icon"
      href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>👟</text></svg>"
    />

    <!-- Fuentes -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@600;700&display=swap"
      rel="stylesheet"
    />

    <!-- Bootstrap e íconos de Bootstrap -->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    />

    <!-- Nuestros estilos (siempre al final para poder sobrescribir Bootstrap) -->
    <link rel="stylesheet" href="css/styles.css" />
  </head>

  <body>
    <a class="visually-hidden-focusable skip-link" href="#contenido">Saltar al contenido</a>

    <?php require_once __DIR__ . '/nav.php'; ?>