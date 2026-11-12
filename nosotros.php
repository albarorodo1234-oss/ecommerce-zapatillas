<?php
$titulo_pagina      = "Nosotros | SoleMate";
$descripcion_pagina = "Conocé SoleMate: una tienda de zapatillas importadas de Chile y Bolivia, con productos 100% auténticas, envío rápido y cuotas sin interés.";
$pagina_activa      = "nosotros";
require_once 'includes/header.php';
?>
    <main id="contenido">
      <!-- ===== Presentación ===== -->
      <section class="about-hero" aria-labelledby="nosotros-titulo">
        <div class="container">
          <div class="row align-items-center g-5">
            <div class="col-lg-6">
              <p class="eyebrow">SOBRE NOSOTROS</p>
              <h1 id="nosotros-titulo" class="page-title">Zapatillas importadas, sin vueltas</h1>
              <p class="about-lead">
                En SoleMate seleccionamos las mejores zapatillas de Nike, Jordan, Adidas y New
                Balance directamente desde Chile y Bolivia, para que puedas acceder a modelos
                difíciles de encontrar con precios competitivos.
              </p>
              <p class="about-lead">
                Verificamos cada par, cuidamos el empaque y te acompañamos en todo el proceso: desde
                la elección de la talla hasta que las recibís en tu casa.
              </p>
              <a class="btn btn-acento mt-2" href="catalogo.php">Ver catálogo</a>
            </div>
            <div class="col-lg-6">
              <img
                src="img/hero.jpg"
                alt="Par de zapatillas blancas apoyadas sobre una superficie gris"
                loading="lazy"
              />
            </div>
          </div>
        </div>
      </section>

      <!-- ===== Valores ===== -->
      <section class="section-padding bg-soft" aria-labelledby="valores-titulo">
        <div class="container">
          <h2 id="valores-titulo" class="section-title">Por qué elegirnos</h2>
          <div class="row g-4">
            <div class="col-md-4">
              <article class="tarjeta value-card">
                <span class="value-icon" aria-hidden="true"
                  ><i class="bi bi-shield-check"></i
                ></span>
                <h3>100% auténticas</h3>
                <p>Verificamos la autenticidad de cada producto antes de enviarlo.</p>
              </article>
            </div>
            <div class="col-md-4">
              <article class="tarjeta value-card">
                <span class="value-icon" aria-hidden="true"><i class="bi bi-truck"></i></span>
                <h3>Envío rápido</h3>
                <p>Despachamos desde Chile y Bolivia con seguimiento hasta tu puerta.</p>
              </article>
            </div>
            <div class="col-md-4">
              <article class="tarjeta value-card">
                <span class="value-icon" aria-hidden="true"><i class="bi bi-credit-card"></i></span>
                <h3>Cuotas sin interés</h3>
                <p>Pagá como prefieras: tarjeta, Mercado Pago o transferencia.</p>
              </article>
            </div>
          </div>
        </div>
      </section>

      <!-- ===== Cómo funciona ===== -->
      <section class="section-padding" aria-labelledby="pasos-titulo">
        <div class="container">
          <h2 id="pasos-titulo" class="section-title">Cómo funciona</h2>
          <ol class="steps">
            <li>
              <div>
                <h3>Elegís tus zapatillas</h3>
                <p>Explorá el catálogo y filtrá por talla, marca, precio y origen.</p>
              </div>
            </li>
            <li>
              <div>
                <h3>Las importamos</h3>
                <p>Preparamos tu pedido desde Chile o Bolivia y lo enviamos a Argentina.</p>
              </div>
            </li>
            <li>
              <div>
                <h3>Las recibís en casa</h3>
                <p>Te avisamos en cada etapa y, si algo no te calza, gestionamos el cambio.</p>
              </div>
            </li>
          </ol>
        </div>
      </section>
    </main>

<?php require_once 'includes/footer.php'; ?>