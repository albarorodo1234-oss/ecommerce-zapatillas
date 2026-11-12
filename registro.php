<?php
$titulo_pagina      = "Crear cuenta | SoleMate";
$descripcion_pagina = "Creá tu cuenta en SoleMate y comprá zapatillas importadas de Chile y Bolivia con envío rápido y cuotas sin interés.";
require_once 'includes/header.php';
?>
    <main id="contenido" class="auth-main">
      <div class="container">
        <section class="tarjeta auth-card" aria-labelledby="registro-titulo">
          <h1 id="registro-titulo" class="auth-title">Crear Cuenta</h1>

          <form>
            <div class="mb-3">
              <label class="form-label" for="nombre">Nombre completo</label>
              <input
                class="form-control"
                type="text"
                id="nombre"
                name="nombre"
                placeholder="Tu nombre y apellido"
                autocomplete="name"
                required
              />
            </div>

            <div class="mb-3">
              <label class="form-label" for="email">Correo Electrónico</label>
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

            <div class="mb-3">
              <label class="form-label" for="password">Contraseña</label>
              <input
                class="form-control"
                type="password"
                id="password"
                name="password"
                autocomplete="new-password"
                minlength="8"
                aria-describedby="ayuda-password"
                required
              />
              <div id="ayuda-password" class="form-text">Mínimo 8 caracteres.</div>
            </div>

            <div class="mb-3">
              <label class="form-label" for="password2">Repetir contraseña</label>
              <input
                class="form-control"
                type="password"
                id="password2"
                name="password2"
                autocomplete="new-password"
                minlength="8"
                required
              />
            </div>

            <div class="form-check mb-4">
              <input class="form-check-input" type="checkbox" id="terminos" required />
              <label class="form-check-label" for="terminos">
                Acepto los <a href="#">Términos de Servicio</a> y la
                <a href="#">Política de Privacidad</a>
              </label>
            </div>

            <!-- Pasará a type="submit" cuando el formulario se conecte con JavaScript o un servidor -->
            <button type="button" class="btn btn-acento btn-lg w-100">Crear cuenta</button>
          </form>

          <p class="divider"><span>O</span></p>

          <button type="button" class="btn btn-contorno w-100">
            <i class="bi bi-google" aria-hidden="true"></i> Registrarse con Google
          </button>

          <p class="auth-switch">¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
        </section>
      </div>
    </main>

<?php require_once 'includes/footer.php'; ?>