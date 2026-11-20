<?php
// includes/prohibido.php
// Página "403 Acceso prohibido" con el diseño del sitio. La muestra
// verificarRol() (includes/auth_guard.php) cuando el usuario inició sesión
// pero su rol no tiene permiso para entrar a la página.

$titulo_pagina      = 'Acceso prohibido | SoleMate';
$descripcion_pagina = 'No tenés permisos para acceder a esta página.';
$pagina_activa      = '';
require_once __DIR__ . '/header.php';
?>
    <main id="contenido" class="auth-main">
      <div class="container">
        <section class="tarjeta auth-card text-center" aria-labelledby="prohibido-titulo">
          <p class="display-4 fw-bold mb-2" aria-hidden="true">403</p>
          <h1 id="prohibido-titulo" class="auth-title">Acceso prohibido</h1>
          <p>
            No tenés permisos para acceder a esta página. Está reservada al rol
            <strong><?= htmlspecialchars($rolRequerido ?? '', ENT_QUOTES, 'UTF-8') ?></strong>.
          </p>
          <a href="<?= $ruta_base ?>index.php" class="btn btn-acento w-100">Volver a la tienda</a>
        </section>
      </div>
    </main>
<?php require_once __DIR__ . '/footer.php'; ?>