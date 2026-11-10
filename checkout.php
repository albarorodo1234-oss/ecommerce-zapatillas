<?php
$titulo_pagina      = "Finalizar compra | SoleMate";
$descripcion_pagina = "Completá tus datos de envío, elegí cómo pagar y confirmá tu pedido de zapatillas importadas en SoleMate.";
require_once 'includes/header.php';
?>
    <main id="contenido">
      <div class="container section-padding pt-4">
        <nav aria-label="Ruta de navegación">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
            <li class="breadcrumb-item"><a href="carrito.php">Carrito</a></li>
            <li class="breadcrumb-item active" aria-current="page">Finalizar compra</li>
          </ol>
        </nav>

        <h1 class="page-title mb-4">Finalizar compra</h1>

        <div class="row g-4">
          <!-- Formulario -->
          <div class="col-lg-7">
            <form>
              <section class="tarjeta checkout-block" aria-labelledby="datos-titulo">
                <h2 id="datos-titulo" class="checkout-title">Datos de contacto</h2>
                <div class="row g-3">
                  <div class="col-12">
                    <label class="form-label" for="nombre">Nombre completo</label>
                    <input
                      class="form-control"
                      type="text"
                      id="nombre"
                      name="nombre"
                      autocomplete="name"
                      required
                    />
                  </div>
                  <div class="col-md-6">
                    <label class="form-label" for="email">Correo electrónico</label>
                    <input
                      class="form-control"
                      type="email"
                      id="email"
                      name="email"
                      placeholder="tu@email.com"
                      autocomplete="email"
                      required
                    />
                  </div>
                  <div class="col-md-6">
                    <label class="form-label" for="telefono">Teléfono</label>
                    <input
                      class="form-control"
                      type="tel"
                      id="telefono"
                      name="telefono"
                      autocomplete="tel"
                      required
                    />
                  </div>
                </div>
              </section>

              <section class="tarjeta checkout-block" aria-labelledby="envio-titulo">
                <h2 id="envio-titulo" class="checkout-title">Dirección de envío</h2>
                <div class="row g-3">
                  <div class="col-12">
                    <label class="form-label" for="direccion">Dirección</label>
                    <input
                      class="form-control"
                      type="text"
                      id="direccion"
                      name="direccion"
                      placeholder="Calle y número"
                      autocomplete="street-address"
                      required
                    />
                  </div>
                  <div class="col-md-6">
                    <label class="form-label" for="ciudad">Ciudad</label>
                    <input
                      class="form-control"
                      type="text"
                      id="ciudad"
                      name="ciudad"
                      autocomplete="address-level2"
                      required
                    />
                  </div>
                  <div class="col-md-6">
                    <label class="form-label" for="provincia">Provincia</label>
                    <select class="form-select" id="provincia" name="provincia" required>
                      <option value="" selected disabled>Elegí una provincia</option>
                      <!-- Agregá el resto de las provincias cuando quieras -->
                      <option>Buenos Aires</option>
                      <option>Ciudad Autónoma de Buenos Aires</option>
                      <option>Córdoba</option>
                      <option>Jujuy</option>
                      <option>Mendoza</option>
                      <option>Salta</option>
                      <option>Santa Fe</option>
                      <option>Tucumán</option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label" for="cp">Código postal</label>
                    <input
                      class="form-control"
                      type="text"
                      id="cp"
                      name="cp"
                      autocomplete="postal-code"
                      required
                    />
                  </div>
                </div>
              </section>

              <section class="tarjeta checkout-block">
                <fieldset>
                  <legend class="checkout-title">Método de pago</legend>

                  <div class="form-check pay-option">
                    <input
                      class="form-check-input"
                      type="radio"
                      name="pago"
                      id="pago-tarjeta"
                      checked
                    />
                    <label class="form-check-label" for="pago-tarjeta">
                      Tarjeta de crédito o débito
                      <span class="pay-note">Cuotas sin interés</span>
                    </label>
                  </div>

                  <div class="form-check pay-option">
                    <input class="form-check-input" type="radio" name="pago" id="pago-mp" />
                    <label class="form-check-label" for="pago-mp">
                      Mercado Pago
                      <span class="pay-note">Pagás desde tu cuenta o con dinero disponible</span>
                    </label>
                  </div>

                  <div class="form-check pay-option mb-0">
                    <input class="form-check-input" type="radio" name="pago" id="pago-transf" />
                    <label class="form-check-label" for="pago-transf">
                      Transferencia bancaria
                      <span class="pay-note">Te enviamos los datos por correo</span>
                    </label>
                  </div>
                </fieldset>
              </section>

              <!-- Pasará a type="submit" cuando el formulario se conecte con JavaScript o un servidor -->
              <button type="button" class="btn btn-acento btn-lg w-100">Confirmar pedido</button>
            </form>
          </div>

          <!-- Resumen -->
          <aside class="col-lg-5" aria-labelledby="pedido-titulo">
            <div class="tarjeta summary summary-sticky">
              <h2 id="pedido-titulo" class="summary-title">Tu pedido</h2>

              <ul class="mini-list">
                <li>
                  <img src="img/nike-air-max-270.jpg" alt="" />
                  <div>
                    <p class="mini-name">Nike Air Max 270</p>
                    <p class="mini-meta">Talla 41 · Cantidad 1</p>
                  </div>
                  <p class="mini-price">ARS $189.900</p>
                </li>
                <li>
                  <img src="img/adidas-ultraboost.jpg" alt="" />
                  <div>
                    <p class="mini-name">Adidas Ultraboost</p>
                    <p class="mini-meta">Talla 42 · Cantidad 1</p>
                  </div>
                  <p class="mini-price">ARS $219.900</p>
                </li>
              </ul>

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

              <a class="continue-link" href="carrito.php">Volver al carrito</a>
            </div>
          </aside>
        </div>
      </div>
    </main>

 <?php require_once 'includes/footer.php'; ?>