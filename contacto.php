<?php
$titulo_pagina      = "Contacto | SoleMate";
$descripcion_pagina = "Escribinos por consultas sobre pedidos, tallas, cambios o envíos. Atención por formulario, WhatsApp y correo electrónico.";
$pagina_activa      = "contacto";
require_once 'includes/funciones.php';

// Asuntos permitidos (los mismos que tiene el <select> del formulario).
$asuntos_validos = [
    'Consulta sobre un pedido',
    'Cambios y devoluciones',
    'Ayuda con las tallas',
    'Otro motivo',
];

// Variables de control: el paso 7 las usará para mostrar mensajes.
$errores = [];
$exito   = false;
$datos   = ['nombre' => '', 'email' => '', 'asunto' => '', 'mensaje' => ''];

// Solo procesamos si la página fue pedida por POST (no al entrar directo por la URL).
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Captura: leer cada campo (dato_post usa isset y trim).
    $nombre  = dato_post('nombre');
    $asunto  = dato_post('asunto');
    $mensaje = dato_post('mensaje');

    // 2. Sanitización del email: elimina los caracteres que no puede tener un correo.
    $email = filter_var(dato_post('email'), FILTER_SANITIZE_EMAIL);

    // Guardamos lo recibido para volver a mostrarlo en el formulario (paso 7).
    $datos = ['nombre' => $nombre, 'email' => $email, 'asunto' => $asunto, 'mensaje' => $mensaje];

    // 3. Validaciones en el servidor.
    if ($nombre === '') {
        $errores['nombre'] = 'El nombre es obligatorio.';
    } elseif (mb_strlen($nombre) > 80) {
        $errores['nombre'] = 'El nombre no puede superar los 80 caracteres.';
    }

    if ($email === '') {
        $errores['email'] = 'El correo electrónico es obligatorio.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores['email'] = 'Ingresá un correo electrónico válido (por ejemplo, tu@email.com).';
    }

    if (!in_array($asunto, $asuntos_validos, true)) {
        $errores['asunto'] = 'Elegí un asunto de la lista.';
    }

    if ($mensaje === '') {
        $errores['mensaje'] = 'El mensaje es obligatorio.';
    } elseif (mb_strlen($mensaje) < 10) {
        $errores['mensaje'] = 'El mensaje debe tener al menos 10 caracteres.';
    } elseif (mb_strlen($mensaje) > 1000) {
        $errores['mensaje'] = 'El mensaje no puede superar los 1000 caracteres.';
    }

    // 4. Si no hubo ningún error, el envío fue exitoso.
    if (empty($errores)) {
        $exito = true;
        $nombre_enviado = $datos['nombre'];
        $datos = ['nombre' => '', 'email' => '', 'asunto' => '', 'mensaje' => ''];
        // En un sitio real, aquí se enviaría un correo o se guardaría en una base de datos.
    }
}
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
              
                            <?php if ($exito): ?>
                <div class="alert alert-success" role="status">
                  ¡Gracias, <?= e($nombre_enviado) ?>! Recibimos tu mensaje y te responderemos a la
                  brevedad.
                </div>
              <?php elseif (!empty($errores)): ?>
                <div class="alert alert-danger" role="alert">
                  No pudimos enviar tu mensaje. Revisá los campos marcados en rojo.
                </div>
              <?php endif; ?>

              <form method="post" action="contacto.php" novalidate>
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label" for="nombre">Nombre</label>
                    <input
                      class="form-control<?= isset($errores['nombre']) ? ' is-invalid' : '' ?>"
                      type="text"
                      id="nombre"
                      name="nombre"
                      value="<?= e($datos['nombre']) ?>"
                      autocomplete="name"
                      required
                      <?= isset($errores['nombre']) ? 'aria-invalid="true" aria-describedby="error-nombre"' : '' ?>
                    />
                    <?php if (isset($errores['nombre'])): ?>
                      <div class="invalid-feedback" id="error-nombre"><?= e($errores['nombre']) ?></div>
                    <?php endif; ?>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label" for="email">Correo electrónico</label>
                    <input
                      class="form-control<?= isset($errores['email']) ? ' is-invalid' : '' ?>"
                      type="email"
                      id="email"
                      name="email"
                      value="<?= e($datos['email']) ?>"
                      placeholder="tu@email.com"
                      autocomplete="email"
                      required
                      <?= isset($errores['email']) ? 'aria-invalid="true" aria-describedby="error-email"' : '' ?>
                    />
                    <?php if (isset($errores['email'])): ?>
                      <div class="invalid-feedback" id="error-email"><?= e($errores['email']) ?></div>
                    <?php endif; ?>
                  </div>

                  <div class="col-12">
                    <label class="form-label" for="asunto">Asunto</label>
                    <select
                      class="form-select<?= isset($errores['asunto']) ? ' is-invalid' : '' ?>"
                      id="asunto"
                      name="asunto"
                      required
                      <?= isset($errores['asunto']) ? 'aria-invalid="true" aria-describedby="error-asunto"' : '' ?>
                    >
                      <option value="" disabled <?= $datos['asunto'] === '' ? 'selected' : '' ?>>Elegí una opción</option>
                      <?php foreach ($asuntos_validos as $opcion): ?>
                        <option value="<?= e($opcion) ?>" <?= $datos['asunto'] === $opcion ? 'selected' : '' ?>><?= e($opcion) ?></option>
                      <?php endforeach; ?>
                    </select>
                    <?php if (isset($errores['asunto'])): ?>
                      <div class="invalid-feedback" id="error-asunto"><?= e($errores['asunto']) ?></div>
                    <?php endif; ?>
                  </div>

                  <div class="col-12">
                    <label class="form-label" for="mensaje">Mensaje</label>
                    <textarea
                      class="form-control<?= isset($errores['mensaje']) ? ' is-invalid' : '' ?>"
                      id="mensaje"
                      name="mensaje"
                      rows="5"
                      required
                      <?= isset($errores['mensaje']) ? 'aria-invalid="true" aria-describedby="error-mensaje"' : '' ?>
                    ><?= e($datos['mensaje']) ?></textarea>
                    <?php if (isset($errores['mensaje'])): ?>
                      <div class="invalid-feedback" id="error-mensaje"><?= e($errores['mensaje']) ?></div>
                    <?php endif; ?>
                  </div>

                  <div class="col-12">
                    <button type="submit" class="btn btn-acento btn-lg">Enviar mensaje</button>
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