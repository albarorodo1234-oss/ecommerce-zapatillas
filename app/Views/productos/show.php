<?php
// app/Views/productos/show.php
// TP10 (MVC): VISTA del detalle de un producto. Solo arma el HTML con las
// variables que le pasa ProductoController::show(); no consulta la base.
// Recibe: $producto (null si no existe), $stock ([talle => stock]),
// $relacionados y $tallas_disponibles
// (además de $titulo_pagina, $descripcion_pagina y $pagina_activa para el header).

require_once __DIR__ . '/../../../includes/header.php';
?>
    <main id="contenido">
      <div class="container section-padding pt-4">
        <nav aria-label="Ruta de navegación">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
            <li class="breadcrumb-item"><a href="catalogo.php">Catálogo</a></li>
            <li class="breadcrumb-item active" aria-current="page">
              <?= $producto ? e($producto['nombre']) : 'Producto no encontrado' ?>
            </li>
          </ol>
        </nav>

        <?php if (!$producto): ?>
          <!-- ===== Producto inexistente u oculto ===== -->
          <section class="tarjeta p-4 text-center" aria-labelledby="producto-titulo">
            <h1 id="producto-titulo" class="page-title">Producto no encontrado</h1>
            <p class="results-count">El producto que buscás no existe o ya no está disponible.</p>
            <a class="btn btn-acento" href="catalogo.php">Ver el catálogo</a>
          </section>
        <?php else: ?>
          <!-- ===== Detalle del producto ===== -->
          <section aria-labelledby="producto-titulo">
            <div class="row g-5">
              <!-- Imagen -->
              <div class="col-lg-5">
                <?php if (!empty($producto['imagen'])): ?>
                  <img
                    class="featured-img"
                    src="<?= e($producto['imagen']) ?>"
                    alt="<?= e($producto['alt'] ?? '') ?>"
                    fetchpriority="high"
                  />
                <?php endif; ?>
              </div>

              <!-- Información -->
              <div class="col-lg-7">
                <p class="eyebrow"><?= e(mb_strtoupper($producto['marca'])) ?></p>
                <h1 id="producto-titulo" class="featured-title"><?= e($producto['nombre']) ?></h1>
                <p class="featured-price">ARS $<?= e(number_format((float) $producto['precio'], 0, ',', '.')) ?></p>
                <p class="featured-desc">
                  <?= e($producto['descripcion'] ?: 'Pronto agregaremos la descripción de este producto.') ?>
                </p>

                <form>
                  <fieldset class="filter-group">
                    <legend class="featured-legend">Seleccionar Talla</legend>
                    <?php if (empty($stock)): ?>
                      <p class="results-count">Consultanos por WhatsApp la disponibilidad de talles.</p>
                    <?php else: ?>
                      <div class="pill-group">
                        <?php foreach ($tallas_disponibles as $talla): ?>
                          <?php $hay_stock = ($stock[$talla] ?? 0) > 0; ?>
                          <input
                            class="btn-check"
                            type="radio"
                            name="talla"
                            id="sel-<?= e($talla) ?>"
                            value="<?= e($talla) ?>"
                            <?= $hay_stock ? '' : 'disabled' ?>
                          />
                          <label
                            class="btn pill pill-square"
                            for="sel-<?= e($talla) ?>"
                            <?= $hay_stock ? '' : 'title="Sin stock"' ?>
                          ><?= e($talla) ?></label>
                        <?php endforeach; ?>
                      </div>
                    <?php endif; ?>
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
                    <dt>Marca</dt>
                    <dd><?= e($producto['marca']) ?></dd>
                  </div>
                  <div>
                    <dt>Origen</dt>
                    <dd><?= e($producto['origen']) ?></dd>
                  </div>
                  <div>
                    <dt>Categoría</dt>
                    <dd><?= e($producto['categoria']) ?></dd>
                  </div>
                </dl>
              </div>
            </div>
          </section>
        <?php endif; ?>
      </div>

      <?php if (!empty($relacionados)): ?>
        <!-- ===== Productos relacionados ===== -->
        <section class="section-padding bg-soft" aria-labelledby="relacionados-titulo">
          <div class="container">
            <h2 id="relacionados-titulo" class="section-title">También te puede interesar</h2>
            <div class="row g-3">
              <?php foreach ($relacionados as $relacionado): ?>
                <div class="col-12 col-sm-6 col-lg-4">
                  <article class="card tarjeta product-card">
                    <?php if (!empty($relacionado['imagen'])): ?>
                      <img
                        src="<?= e($relacionado['imagen']) ?>"
                        class="card-img-top"
                        alt="<?= e($relacionado['alt'] ?? '') ?>"
                        loading="lazy"
                      />
                    <?php endif; ?>
                    <div class="card-body">
                      <div class="meta">
                        <span class="badge-origen"><?= e($relacionado['origen']) ?></span>
                      </div>
                      <h3 class="product-name">
                        <a class="stretched-link" href="producto.php?id=<?= e($relacionado['id']) ?>"><?= e($relacionado['nombre']) ?></a>
                      </h3>
                      <p class="product-type">Zapatilla <?= e(mb_strtolower($relacionado['categoria'])) ?></p>
                      <div class="price-row">
                        <p class="price">ARS $<?= e(number_format((float) $relacionado['precio'], 0, ',', '.')) ?></p>
                        <button
                          type="button"
                          class="btn btn-acento btn-sm btn-add"
                          aria-label="Añadir <?= e($relacionado['nombre']) ?> al carrito"
                        >
                          Añadir
                        </button>
                      </div>
                    </div>
                  </article>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </section>
      <?php endif; ?>
    </main>

<?php require_once 	__DIR__ . '/../../../includes/footer.php'; ?>