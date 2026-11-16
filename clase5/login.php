<?php
$titulo_pagina      = "Iniciar sesión | SoleMate";
$descripcion_pagina = "Ingresá a tu cuenta de SoleMate para ver tus pedidos y comprar zapatillas importadas de Chile y Bolivia.";
require_once 'includes/header.php';
?>
    <main id="contenido" class="auth-main">
      <div class="container">
        <section class="tarjeta auth-card" aria-labelledby="login-titulo">
          <h1 id="login-titulo" class="auth-title">Inicia Sesión</h1>

          <form>
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

            <div class="mb-4">
              <label class="form-label" for="password">Contraseña</label>
              <input
                class="form-control"
                type="password"
                id="password"
                name="password"
                placeholder="Tu contraseña"
                autocomplete="current-password"
                required
              />
            </div>

            <!-- Pasará a type="submit" cuando el formulario se conecte con JavaScript o un servidor -->
            <button type="button" class="btn btn-acento btn-lg w-100">Ingresar</button>
          </form>

          <p class="divider"><span>O</span></p>

          <button type="button" class="btn btn-contorno w-100">
            <i class="bi bi-google" aria-hidden="true"></i> Continuar con Google
          </button>

          <p class="auth-switch">¿No tienes cuenta? <a href="registro.php">Regístrate</a></p>
        </section>
      </div>
    </main>

    <?php require_once 'includes/footer.php'; ?>