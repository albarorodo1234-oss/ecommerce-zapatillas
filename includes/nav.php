<?php
// includes/nav.php
// Barra superior del sitio: logo, buscador y accesos de cuenta/carrito.
?>
    <!-- ========== HEADER ========== -->
    <header class="site-header">
      <div class="container header-inner">
        <a class="brand" href="index.php">
          <span class="brand-mark" aria-hidden="true"><i class="bi bi-x-circle"></i></span>
          SOLEMATE
        </a>

        <form class="search" role="search" action="catalogo.php">
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
          <a class="icon-link" href="login.php" aria-label="Iniciar sesión">
            <i class="bi bi-person" aria-hidden="true"></i>
          </a>
          <a class="icon-link" href="carrito.php" aria-label="Carrito, 2 productos">
            <i class="bi bi-cart2" aria-hidden="true"></i>
            <span class="cart-count" aria-hidden="true">2</span>
          </a>
          <button class="btn-lang" type="button" aria-label="Idioma: español">ES</button>
        </nav>
      </div>
    </header>