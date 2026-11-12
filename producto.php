<?php
$titulo_pagina      = "Nike Air Max 270 | SoleMate";
$descripcion_pagina = "Nike Air Max 270 importada de Chile. Aire visible de largo recorrido, parte superior ligera y diseño cómodo para el uso diario.";
$pagina_activa      = "catalogo";
require_once 'includes/header.php';
?>
 <main id="contenido">
      <div class="container section-padding pt-4">
        <nav aria-label="Ruta de navegación">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
            <li class="breadcrumb-item"><a href="catalogo.php">Catálogo</a></li>
            <li class="breadcrumb-item active" aria-current="page">Nike Air Max 270</li>
          </ol>
        </nav>

        <!-- ===== Detalle del producto ===== -->
        <section aria-labelledby="producto-titulo">
          <div class="row g-5">
            <!-- Galería -->
            <div class="col-lg-5">
              <img
                class="featured-img"
                src="img/nike-air-max-270.jpg"
                alt="Nike Air Max 270 blanca con suela roja, vista lateral"
                fetchpriority="high"
              />
              <!-- Las 4 miniaturas usan la misma foto por ahora; más adelante se cambian por otras vistas -->
              <ul class="thumbs" aria-label="Galería de imágenes del producto">
                <li>
                  <button type="button" aria-label="Ver imagen 1" aria-current="true">
                    <img src="img/nike-air-max-270.jpg" alt="" />
                  </button>
                </li>
                <li>
                  <button type="button" aria-label="Ver imagen 2">
                    <img src="img/nike-air-max-270.jpg" alt="" />
                  </button>
                </li>
                <li>
                  <button type="button" aria-label="Ver imagen 3">
                    <img src="img/nike-air-max-270.jpg" alt="" />
                  </button>
                </li>
                <li>
                  <button type="button" aria-label="Ver imagen 4">
                    <img src="img/nike-air-max-270.jpg" alt="" />
                  </button>
                </li>
              </ul>
            </div>

            <!-- Información -->
            <div class="col-lg-7">
              <p class="eyebrow">NUEVO</p>
              <h1 id="producto-titulo" class="featured-title">Nike Air Max 270</h1>
              <p class="featured-price">ARS $189.900</p>
              <p class="featured-desc">
                La Nike Air Max 270 combina un aire visible de largo recorrido con una parte
                superior ligera y versátil. Diseñada para el uso diario, mantiene el ADN deportivo
                de la línea Air Max con un perfil moderno y cómodo.
              </p>

              <form>
                <fieldset class="filter-group">
                  <legend class="featured-legend">Seleccionar Talla</legend>
                  <div class="pill-group">
                    <input class="btn-check" type="radio" name="talla" id="sel-38" />
                    <label class="btn pill pill-square" for="sel-38">38</label>
                    <input class="btn-check" type="radio" name="talla" id="sel-39" />
                    <label class="btn pill pill-square" for="sel-39">39</label>
                    <input class="btn-check" type="radio" name="talla" id="sel-40" />
                    <label class="btn pill pill-square" for="sel-40">40</label>
                    <input class="btn-check" type="radio" name="talla" id="sel-41" checked />
                    <label class="btn pill pill-square" for="sel-41">41</label>
                    <input class="btn-check" type="radio" name="talla" id="sel-42" />
                    <label class="btn pill pill-square" for="sel-42">42</label>
                    <input class="btn-check" type="radio" name="talla" id="sel-43" />
                    <label class="btn pill pill-square" for="sel-43">43</label>
                    <input class="btn-check" type="radio" name="talla" id="sel-44" />
                    <label class="btn pill pill-square" for="sel-44">44</label>
                  </div>
                  <a class="size-guide-link" href="guia-tallas.php">Ver guía de tallas</a>
                </fieldset>

                <div class="d-flex flex-wrap gap-3 mb-4">
                  <button type="button" class="btn btn-acento btn-lg">Añadir al Carrito</button>
                  <a class="btn btn-contorno btn-lg" href="checkout.php">Comprar Ahora</a>
                </div>
              </form>

              <h2 class="specs-title">Especificaciones Técnicas</h2>
              <dl class="specs">
                <div>
                  <dt>Origen</dt>
                  <dd>Chile</dd>
                </div>
                <div>
                  <dt>Categoría</dt>
                  <dd>Running</dd>
                </div>
                <div>
                  <dt>Material</dt>
                  <dd>Sintético y malla</dd>
                </div>
                <div>
                  <dt>Amortiguación</dt>
                  <dd>Air Max visible</dd>
                </div>
              </dl>
            </div>
          </div>
        </section>
      </div>

      <!-- ===== Productos relacionados ===== -->
      <section class="section-padding bg-soft" aria-labelledby="relacionados-titulo">
        <div class="container">
          <h2 id="relacionados-titulo" class="section-title">También te puede interesar</h2>
          <div class="row g-3">
            <div class="col-12 col-sm-6 col-lg-4">
              <article class="card tarjeta product-card">
                <img
                  src="img/adidas-ultraboost.jpg"
                  class="card-img-top"
                  alt="Zapatilla Adidas Ultraboost negra con suela blanca"
                  loading="lazy"
                />
                <div class="card-body">
                  <div class="meta">
                    <span class="badge-origen">Bolivia</span>
                    <span class="talla-ref">ARG 40</span>
                  </div>
                  <h3 class="product-name">
                    <a class="stretched-link" href="producto.php">Adidas Ultraboost</a>
                  </h3>
                  <p class="product-type">Zapatilla running</p>
                  <div class="price-row">
                    <p class="price">ARS $219.900</p>
                    <button
                      type="button"
                      class="btn btn-acento btn-sm btn-add"
                      aria-label="Añadir Adidas Ultraboost al carrito"
                    >
                      Añadir
                    </button>
                  </div>
                </div>
              </article>
            </div>

            <div class="col-12 col-sm-6 col-lg-4">
              <article class="card tarjeta product-card">
                <img
                  src="img/jordan-1-retro.jpg"
                  class="card-img-top"
                  alt="Zapatilla Jordan 1 Retro roja, blanca y negra"
                  loading="lazy"
                />
                <div class="card-body">
                  <div class="meta">
                    <span class="badge-origen">Chile</span>
                    <span class="talla-ref">ARG 41</span>
                  </div>
                  <h3 class="product-name">
                    <a class="stretched-link" href="producto.php">Jordan 1 Retro</a>
                  </h3>
                  <p class="product-type">Zapatilla basketball</p>
                  <div class="price-row">
                    <p class="price">ARS $249.900</p>
                    <button
                      type="button"
                      class="btn btn-acento btn-sm btn-add"
                      aria-label="Añadir Jordan 1 Retro al carrito"
                    >
                      Añadir
                    </button>
                  </div>
                </div>
              </article>
            </div>

            <div class="col-12 col-sm-6 col-lg-4">
              <article class="card tarjeta product-card">
                <img
                  src="img/new-balance-990.jpg"
                  class="card-img-top"
                  alt="Zapatilla New Balance 990 gris"
                  loading="lazy"
                />
                <div class="card-body">
                  <div class="meta">
                    <span class="badge-origen">Bolivia</span>
                    <span class="talla-ref">ARG 42</span>
                  </div>
                  <h3 class="product-name">
                    <a class="stretched-link" href="producto.php">New Balance 990</a>
                  </h3>
                  <p class="product-type">Zapatilla lifestyle</p>
                  <div class="price-row">
                    <p class="price">ARS $229.900</p>
                    <button
                      type="button"
                      class="btn btn-acento btn-sm btn-add"
                      aria-label="Añadir New Balance 990 al carrito"
                    >
                      Añadir
                    </button>
                  </div>
                </div>
              </article>
            </div>
          </div>
        </div>
      </section>
    </main>

    <?php require_once 'includes/footer.php'; ?> 