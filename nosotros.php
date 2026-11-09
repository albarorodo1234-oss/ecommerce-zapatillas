<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>Nosotros | SoleMate</title>
    <meta
      name="description"
      content="Conocé SoleMate: una tienda de zapatillas importadas de Chile y Bolivia, con productos 100% auténticos, envío rápido y cuotas sin interés."
    />

    <meta name="theme-color" content="#d43f00" />
    <link
      rel="icon"
      href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>👟</text></svg>"
    />

    <!-- Fuentes -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@600;700&display=swap"
      rel="stylesheet"
    />

    <!-- Bootstrap e íconos de Bootstrap -->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    />

    <!-- Nuestros estilos (siempre al final para poder sobrescribir Bootstrap) -->
    <link rel="stylesheet" href="css/styles.css" />
  </head>

  <body>
    <a class="visually-hidden-focusable skip-link" href="#contenido">Saltar al contenido</a>

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

    <!-- ========== MAIN ========== -->
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

    <!-- ========== FOOTER ========== -->
    <footer class="site-footer">
      <div class="container">
        <div class="row g-4 footer-top">
          <div class="col-lg-6">
            <a class="brand brand-light" href="index.php">
              <span class="brand-mark brand-mark-light" aria-hidden="true"
                ><i class="bi bi-x-circle"></i
              ></span>
              SOLEMATE
            </a>
            <p class="footer-text">
              Tu tienda de referencia para zapatillas importadas. Traemos las mejores selecciones de
              Chile y Bolivia para que las disfrutes sin salir de casa.
            </p>
            <ul class="social">
              <li>
                <a href="#" aria-label="Instagram de SoleMate"
                  ><i class="bi bi-instagram" aria-hidden="true"></i
                ></a>
              </li>
              <li>
                <a href="#" aria-label="Twitter de SoleMate"
                  ><i class="bi bi-twitter" aria-hidden="true"></i
                ></a>
              </li>
            </ul>
          </div>

          <nav class="col-6 col-md-4 col-lg-2" aria-label="Tienda">
            <h2 class="footer-title">Tienda</h2>
            <ul class="footer-links">
              <li><a href="catalogo.php">Nuevas Llegadas</a></li>
              <li><a href="catalogo.php">Más Vendidos</a></li>
              <li><a href="catalogo.php">Colecciones</a></li>
            </ul>
          </nav>

          <nav class="col-6 col-md-4 col-lg-2" aria-label="Soporte">
            <h2 class="footer-title">Soporte</h2>
            <ul class="footer-links">
              <!-- Los "#" son temporales: se reemplazan cuando existan esas páginas -->
              <li><a href="#">Envíos</a></li>
              <li><a href="#">Cambios y Devoluciones</a></li>
              <li><a href="#">Preguntas Frecuentes</a></li>
            </ul>
          </nav>

          <nav class="col-6 col-md-4 col-lg-2" aria-label="Empresa">
            <h2 class="footer-title">Empresa</h2>
            <ul class="footer-links">
              <li><a href="nosotros.php">Acerca de Nosotros</a></li>
              <li><a href="#">Carreras</a></li>
              <li><a href="contacto.php">Contacto</a></li>
            </ul>
          </nav>
        </div>

        <div class="payments">
          <p class="payments-title">Medios de pago</p>
          <ul class="pay-list">
            <li>VISA</li>
            <li>Mastercard</li>
            <li>Mercado Pago</li>
            <li>Transferencia</li>
          </ul>
        </div>

        <div class="footer-bottom">
          <p>© 2026 SoleMate. Todos los derechos reservados.</p>
          <ul class="legal-links">
            <li><a href="#">Política de Privacidad</a></li>
            <li><a href="#">Términos de Servicio</a></li>
          </ul>
        </div>

        <div class="footer-help">
          <a
            class="btn-whatsapp"
            href="https://wa.me/56912345678"
            target="_blank"
            rel="noopener noreferrer"
          >
            <i class="bi bi-whatsapp" aria-hidden="true"></i>
            ¿Necesitás ayuda? Chatea con nosotros por WhatsApp
          </a>
          <p>+56 9 1234 5678</p>
        </div>
      </div>
    </footer>

    <script src="js/main.js"></script>
  </body>
</html>
