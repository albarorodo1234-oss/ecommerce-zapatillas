<?php
$titulo_pagina      = "Catálogo de zapatillas importadas | SoleMate";
$descripcion_pagina = "Explorá el catálogo de SoleMate: zapatillas Nike, Jordan, Adidas y New Balance importadas de Chile y Bolivia. Filtrá por talla, precio, marca y origen.";
$pagina_activa      = "catalogo";
require_once 'includes/header.php';
?>
    <main id="contenido">
      <div class="container section-padding pt-4">
        <nav aria-label="Ruta de navegación">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
            <li class="breadcrumb-item active" aria-current="page">Catálogo</li>
          </ol>
        </nav>

        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
          <div>
            <h1 class="page-title">Catálogo de Productos</h1>
            <p class="results-count mb-0">Mostrando 4 productos</p>
          </div>
          <div>
            <label class="visually-hidden" for="orden">Ordenar productos</label>
            <select class="form-select form-select-sm sort" id="orden">
              <option value="">Ordenar por precio</option>
              <option value="menor">Menor precio</option>
              <option value="mayor">Mayor precio</option>
            </select>
          </div>
        </div>

        <div class="row g-4">
          <!-- Filtros -->
          <aside class="col-lg-3" aria-label="Filtros del catálogo">
            <form class="tarjeta filters filters-sticky">
              <h2 class="filters-title">Filtros</h2>
              <p class="filters-sub">Refina tu búsqueda</p>

              <fieldset class="filter-group">
                <legend>Tallas</legend>
                <div class="pill-group">
                  <input class="btn-check" type="checkbox" id="talla-38" />
                  <label class="btn pill" for="talla-38">38</label>
                  <input class="btn-check" type="checkbox" id="talla-39" />
                  <label class="btn pill" for="talla-39">39</label>
                  <input class="btn-check" type="checkbox" id="talla-40" />
                  <label class="btn pill" for="talla-40">40</label>
                  <input class="btn-check" type="checkbox" id="talla-41" />
                  <label class="btn pill" for="talla-41">41</label>
                  <input class="btn-check" type="checkbox" id="talla-42" />
                  <label class="btn pill" for="talla-42">42</label>
                  <input class="btn-check" type="checkbox" id="talla-43" />
                  <label class="btn pill" for="talla-43">43</label>
                  <input class="btn-check" type="checkbox" id="talla-44" />
                  <label class="btn pill" for="talla-44">44</label>
                </div>
              </fieldset>

              <fieldset class="filter-group">
                <legend>Precio máximo</legend>
                <label class="visually-hidden" for="precio-max">Precio máximo</label>
                <input
                  class="form-range"
                  type="range"
                  id="precio-max"
                  min="50000"
                  max="300000"
                  step="10000"
                  value="300000"
                />
                <div class="range-labels">
                  <span>ARS $50.000</span>
                  <span>ARS $300.000</span>
                </div>
              </fieldset>

              <fieldset class="filter-group">
                <legend>Marcas</legend>
                <div class="pill-group">
                  <input class="btn-check" type="checkbox" id="marca-nike" />
                  <label class="btn pill" for="marca-nike">Nike</label>
                  <input class="btn-check" type="checkbox" id="marca-jordan" />
                  <label class="btn pill" for="marca-jordan">Jordan</label>
                  <input class="btn-check" type="checkbox" id="marca-adidas" />
                  <label class="btn pill" for="marca-adidas">Adidas</label>
                  <input class="btn-check" type="checkbox" id="marca-nb" />
                  <label class="btn pill" for="marca-nb">New Balance</label>
                </div>
              </fieldset>

              <fieldset class="filter-group">
                <legend>Origen</legend>
                <div class="pill-group">
                  <input class="btn-check" type="checkbox" id="origen-chile" />
                  <label class="btn pill" for="origen-chile">Chile</label>
                  <input class="btn-check" type="checkbox" id="origen-bolivia" />
                  <label class="btn pill" for="origen-bolivia">Bolivia</label>
                </div>
              </fieldset>

              <div class="d-flex gap-2">
                <button type="button" class="btn btn-acento flex-fill">Aplicar</button>
                <button type="reset" class="btn btn-contorno flex-fill">Limpiar</button>
              </div>
            </form>
          </aside>

          <!-- Listado de productos -->
          <section class="col-lg-9" aria-labelledby="listado-titulo">
            <h2 id="listado-titulo" class="visually-hidden">Listado de productos</h2>
            <div class="row g-3">
              <div class="col-12 col-sm-6 col-xl-3">
                <article class="card tarjeta product-card">
                  <img
                    src="img/nike-air-max-270.jpg"
                    class="card-img-top"
                    alt="Zapatilla Nike Air Max 270 blanca con suela roja"
                    loading="lazy"
                  />
                  <div class="card-body">
                    <div class="meta">
                      <span class="badge-origen">Chile</span>
                      <span class="talla-ref">ARG 38</span>
                    </div>
                    <h3 class="product-name">
                      <a class="stretched-link" href="producto.php">Nike Air Max 270</a>
                    </h3>
                    <p class="product-type">Zapatilla running</p>
                    <div class="price-row">
                      <p class="price">ARS $189.900</p>
                      <button
                        type="button"
                        class="btn btn-acento btn-sm btn-add"
                        aria-label="Añadir Nike Air Max 270 al carrito"
                      >
                        Añadir
                      </button>
                    </div>
                  </div>
                </article>
              </div>

              <div class="col-12 col-sm-6 col-xl-3">
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

              <div class="col-12 col-sm-6 col-xl-3">
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

              <div class="col-12 col-sm-6 col-xl-3">
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
          </section>
        </div>
      </div>
    </main>

    <?php require_once 'includes/footer.php'; ?>