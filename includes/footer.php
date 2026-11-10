   <?php
   // includes/footer.php
   // Pie de página, script principal de JavaScript y cierre del documento.
   ?>
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
          <p>© 2026 <?= htmlspecialchars(APP_NAME) ?>. Todos los derechos reservados.</p>
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
