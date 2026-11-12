<?php
$titulo_pagina      = "Tu carrito | SoleMate";
$descripcion_pagina = "Revisá los productos de tu carrito en SoleMate, ajustá las cantidades y avanzá al pago con envío gratis.";
require_once 'includes/header.php';
?>
<main id="contenido">
      <div class="container section-padding pt-4">
        <nav aria-label="Ruta de navegación">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
            <li class="breadcrumb-item active" aria-current="page">Carrito</li>
          </ol>
        </nav>

        <h1 class="page-title mb-4">Tu Carrito</h1>

        <div class="row g-4">
          <!-- Productos del carrito -->
          <section class="col-lg-8" aria-labelledby="items-titulo">
            <h2 id="items-titulo" class="visually-hidden">Productos en tu carrito</h2>
            <div class="tarjeta cart-box">
              <ul class="cart-list">
                <li class="cart-item">
                  <img
                    src="img/nike-air-max-270.jpg"
                    alt="Zapatilla Nike Air Max 270 blanca con suela roja"
                  />
                  <div class="cart-info">
                    <div>
                      <h3 class="cart-name">
                        <a href="producto.php">Nike Air Max 270</a>
                      </h3>
                      <p class="cart-meta">Talla: 41 | Origen: Chile</p>
                    </div>
                    <div class="cart-side">
                      <p class="cart-price">ARS $189.900</p>
                      <div class="qty" role="group" aria-label="Cantidad de Nike Air Max 270">
                        <button type="button" aria-label="Disminuir cantidad">−</button>
                        <output aria-live="polite">1</output>
                        <button type="button" aria-label="Aumentar cantidad">+</button>
                      </div>
                      <button
                        type="button"
                        class="btn-remove"
                        aria-label="Eliminar Nike Air Max 270 del carrito"
                      >
                        Eliminar
                      </button>
                    </div>
                  </div>
                </li>

                <li class="cart-item">
                  <img
                    src="img/adidas-ultraboost.jpg"
                    alt="Zapatilla Adidas Ultraboost negra con suela blanca"
                  />
                  <div class="cart-info">
                    <div>
                      <h3 class="cart-name">
                        <a href="producto.php">Adidas Ultraboost</a>
                      </h3>
                      <p class="cart-meta">Talla: 42 | Origen: Bolivia</p>
                    </div>
                    <div class="cart-side">
                      <p class="cart-price">ARS $219.900</p>
                      <div class="qty" role="group" aria-label="Cantidad de Adidas Ultraboost">
                        <button type="button" aria-label="Disminuir cantidad">−</button>
                        <output aria-live="polite">1</output>
                        <button type="button" aria-label="Aumentar cantidad">+</button>
                      </div>
                      <button
                        type="button"
                        class="btn-remove"
                        aria-label="Eliminar Adidas Ultraboost del carrito"
                      >
                        Eliminar
                      </button>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          </section>

          <!-- Resumen del pedido -->
          <aside class="col-lg-4" aria-labelledby="resumen-titulo">
            <div class="tarjeta summary summary-sticky">
              <h2 id="resumen-titulo" class="summary-title">Resumen del pedido</h2>
              <dl>
                <div>
                  <dt>Subtotal</dt>
                  <dd>ARS $409.800</dd>
                </div>
                <div>
                  <dt>Envío</dt>
                  <dd>Gratis</dd>
                </div>
                <div class="total">
                  <dt>Total</dt>
                  <dd>ARS $409.800</dd>
                </div>
              </dl>
              <a class="btn btn-acento btn-lg w-100" href="checkout.php">Ir al Pago</a>
              <a class="continue-link" href="catalogo.php">Seguir comprando</a>
            </div>
          </aside>
        </div>
      </div>
    </main>

    <?php require_once 'includes/footer.php'; ?> 