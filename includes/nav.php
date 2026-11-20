<?php
// includes/nav.php
// Barra superior del sitio: logo, buscador, accesos de cuenta/carrito y menú principal.
// Cada página define ANTES de incluir el header:
//   $pagina_activa  (opcional: "inicio", "catalogo", "nosotros" o "contacto")
// TP8: los accesos de cuenta cambian según $_SESSION (la sesión la inicia
// header.php con includes/sesion.php antes de incluir este archivo).

$pagina_activa = $pagina_activa ?? '';

// Menú principal: clave => [texto visible, archivo al que lleva]
$menu = [
    'inicio'   => ['Inicio',   'index.php'],
    'catalogo' => ['Catálogo', 'catalogo.php'],
    'nosotros' => ['Nosotros', 'nosotros.php'],
    'contacto' => ['Contacto', 'contacto.php'],
];
?>
    <!-- ========== HEADER ========== -->
    <header class="site-header">
      <div class="container header-inner">
        <a class="brand" href="<?= $ruta_base ?>index.php">
          <span class="brand-mark" aria-hidden="true"><i class="bi bi-x-circle"></i></span>
          SOLEMATE
        </a>

        <form class="search" role="search" action="<?= $ruta_base ?>catalogo.php">
          <label class="visually-hidden" for="buscar">Buscar zapatillas importadas</label>
          <i class="bi bi-search" aria-hidden="true"></i>
          <input
            class="form-control"
            type="search"
            id="buscar"
            name="q"
            placeholder="Buscar zapatillas importadas..."
          />
        </form>

        <nav class="header-actions" aria-label="Cuenta y carrito">
          <?php if (($_SESSION['rol'] ?? '') === 'Administrador'): ?>
            <!-- TP8: el acceso al panel solo lo ve el Administrador -->
            <a class="icon-link" href="<?= $ruta_base ?>admin/index.php" aria-label="Panel de administración" title="Panel de administración">
              <i class="bi bi-speedometer2" aria-hidden="true"></i>
            </a>
          <?php endif; ?>

          <?php if (isset($_SESSION['usuario_id'])): ?>
            <!-- TP8: con la sesión iniciada, "Mi cuenta" (con el nombre) y "Cerrar sesión" -->
            <a class="icon-link" href="<?= $ruta_base ?>mi-cuenta.php" title="Mi cuenta">
              <i class="bi bi-person-check" aria-hidden="true"></i>
              <span class="fs-6 ms-1 align-self-center d-none d-lg-inline"><?= htmlspecialchars(explode(' ', $_SESSION['usuario_nombre'])[0]) ?></span>
              <span class="visually-hidden">Mi cuenta de <?= htmlspecialchars($_SESSION['usuario_nombre']) ?></span>
            </a>
            <a class="icon-link" href="<?= $ruta_base ?>logout.php" aria-label="Cerrar sesión" title="Cerrar sesión">
              <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
            </a>
          <?php else: ?>
            <a class="icon-link" href="<?= $ruta_base ?>login.php" aria-label="Iniciar sesión" title="Iniciar sesión">
              <i class="bi bi-person" aria-hidden="true"></i>
            </a>
          <?php endif; ?>

          <a class="icon-link" href="<?= $ruta_base ?>carrito.php" aria-label="Carrito, 2 productos">
            <i class="bi bi-cart2" aria-hidden="true"></i>
            <span class="cart-count" aria-hidden="true">2</span>
          </a>
          <button class="btn-lang" type="button" aria-label="Idioma: español">ES</button>
        </nav>
      </div>

      <!-- Menú principal: la clase "active" la decide PHP en el servidor -->
      <nav class="main-nav" aria-label="Principal">
        <div class="container">
          <ul class="main-nav-list">
            <?php foreach ($menu as $clave => [$texto, $enlace]): ?>
              <li>
                <a
                  class="main-nav-link<?= $clave === $pagina_activa ? ' active' : '' ?>"
                  href="<?= $ruta_base . $enlace ?>"
                  <?= $clave === $pagina_activa ? 'aria-current="page"' : '' ?>
                  ><?= $texto ?></a
                >
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </nav>
    </header>