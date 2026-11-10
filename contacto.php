<?php
$titulo_pagina      = "Contacto | SoleMate";
$descripcion_pagina = "Escribinos por consultas sobre pedidos, tallas, cambios o envíos. Atención por formulario, WhatsApp y correo electrónico.";
$pagina_activa      = "contacto";
require_once 'includes/header.php';
?>
    <main id="contenido">
      <div class="container section-padding pt-4">
        <nav aria-label="Ruta de navegación">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
            <li class="breadcrumb-item active" aria-current="page">Contacto</li>
          </ol>
        </nav>

        <h1 class="page-title">Contacto</h1>
        <p class="results-count mb-4">
          ¿Tenés dudas sobre un pedido, una talla o un envío? Escribinos y te respondemos a la
          brevedad.
        </p>

        <div class="row g-4">
          <!-- Formulario -->
          <section class="col-lg-7" aria-labelledby="mensaje-titulo">
            <div class="tarjeta contact-card">
              <h2 id="mensaje-titulo" class="checkout-title">Envianos un mensaje</h2>
              <form>
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label" for="nombre">Nombre</label>
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
                  <div class="col-12">
                    <label class="form-label" for="asunto">Asunto</label>
                    <select class="form-select" id="asunto" name="asunto" required>
                      <option value="" selected disabled>Elegí una opción</option>
                      <option>Consulta sobre un pedido</option>
                      <option>Cambios y devoluciones</option>
                      <option>Ayuda con las tallas</option>
                      <option>Otro motivo</option>
                    </select>
                  </div>
                  <div class="col-12">
                    <label class="form-label" for="mensaje">Mensaje</label>
                    <textarea
                      class="form-control"
                      id="mensaje"
                      name="mensaje"
                      rows="5"
                      required
                    ></textarea>
                  </div>
                  <div class="col-12">
                    <!-- Pasará a type="submit" cuando el formulario se conecte con JavaScript o un servidor -->
                    <button type="button" class="btn btn-acento btn-lg">Enviar mensaje</button>
                  </div>
                </div>
              </form>
            </div>
          </section>

          <!-- Otras vías de contacto -->
          <aside class="col-lg-5" aria-labelledby="vias-titulo">
            <div class="tarjeta contact-card">
              <h2 id="vias-titulo" class="checkout-title">Otras vías de contacto</h2>
              <ul class="contact-list">
                <li>
                  <span class="contact-icon" aria-hidden="true"
                    ><i class="bi bi-whatsapp"></i
                  ></span>
                  <div>
                    <strong>WhatsApp</strong>
                    <a href="https://wa.me/56912345678" target="_blank" rel="noopener noreferrer"
                      >+56 9 1234 5678</a
                    >
                  </div>
                </li>
                <li>
                  <span class="contact-icon" aria-hidden="true"
                    ><i class="bi bi-envelope"></i
                  ></span>
                  <div>
                    <strong>Correo electrónico</strong>
                    <a href="mailto:<?= htmlspecialchars(APP_EMAIL) ?>"><?= htmlspecialchars(APP_EMAIL) ?></a>
                  </div>
                </li>
                <li>
                  <span class="contact-icon" aria-hidden="true"><i class="bi bi-clock"></i></span>
                  <div>
                    <strong>Horario de atención</strong>
                    <span>Lunes a viernes, de 9 a 18 h</span>
                  </div>
                </li>
              </ul>
            </div>
          </aside>
        </div>
      </div>
    </main>

<?php require_once 'includes/footer.php'; ?>