<?php
// registro.php
// Registro de clientes (TP8): valida los datos en el servidor, verifica que
// el correo no exista, cifra la contraseña con password_hash() (bcrypt) y
// guarda el usuario con una consulta preparada. Toda la lógica va ANTES del
// header, porque al terminar se redirige con header('Location: ...').

require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/includes/funciones.php';
require_once __DIR__ . '/includes/sesion.php';

// Si ya inició sesión, no tiene sentido registrarse de nuevo.
if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

$titulo_pagina      = "Crear cuenta | SoleMate";
$descripcion_pagina = "Creá tu cuenta en SoleMate y comprá zapatillas importadas de Chile y Bolivia con envío rápido y cuotas sin interés.";

$nombre  = '';
$email   = '';
$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Captura: dato_post() devuelve '' si el campo no llega o no es texto.
    //    Las contraseñas NO se recortan con trim(): un espacio puede ser parte de ella.
    $nombre    = dato_post('nombre');
    $email     = mb_strtolower(dato_post('email'));
    $password  = isset($_POST['password']) && is_string($_POST['password']) ? $_POST['password'] : '';
    $password2 = isset($_POST['password2']) && is_string($_POST['password2']) ? $_POST['password2'] : '';
    $terminos  = dato_post('terminos');

    // 2. Validación estricta en el servidor (lista blanca de caracteres en el nombre).
    if ($nombre === '') {
        $errores['nombre'] = 'Ingresá tu nombre y apellido.';
    } elseif (mb_strlen($nombre) < 3 || mb_strlen($nombre) > 100) {
        $errores['nombre'] = 'El nombre debe tener entre 3 y 100 caracteres.';
    } elseif (!preg_match("/^[\p{L} '.-]+$/u", $nombre)) {
        // Solo letras (con tildes y ñ), espacios, apóstrofo, punto y guion:
        // un nombre con "<script>" u otros símbolos se rechaza.
        $errores['nombre'] = 'El nombre solo puede tener letras, espacios, apóstrofos, puntos y guiones.';
    }

    if ($email === '') {
        $errores['email'] = 'Ingresá tu correo electrónico.';
    } elseif (mb_strlen($email) > 150 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores['email'] = 'Ingresá un correo electrónico válido (por ejemplo, nombre@mail.com).';
    }

    // bcrypt solo usa los primeros 72 bytes de la contraseña: se limita el largo.
    if (strlen($password) < 8 || strlen($password) > 72) {
        $errores['password'] = 'La contraseña debe tener entre 8 y 72 caracteres.';
    }

    if ($password2 === '' || $password2 !== $password) {
        $errores['password2'] = 'Las contraseñas no coinciden.';
    }

    if ($terminos !== '1') {
        $errores['terminos'] = 'Debés aceptar los Términos de Servicio y la Política de Privacidad.';
    }

    // 3. Verificar que el correo no esté registrado (consulta preparada).
    if (!isset($errores['email'])) {
        try {
            $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = :email LIMIT 1');
            $stmt->execute([':email' => $email]);
            if ($stmt->fetch()) {
                $errores['email'] = 'Ya existe una cuenta con ese correo. Iniciá sesión o usá otro correo.';
            }
        } catch (PDOException $e) {
            error_log('Error al verificar el email en el registro: ' . $e->getMessage());
            $errores['general'] = 'Ocurrió un error inesperado. Intentá nuevamente.';
        }
    }

    // 4. Sin errores: cifrar la contraseña e insertar con el rol "Cliente".
    if (empty($errores)) {
        try {
            $sql = 'INSERT INTO usuarios (rol_id, nombre, email, password_hash)
                    VALUES ((SELECT id FROM roles WHERE nombre = :rol), :nombre, :email, :password_hash)';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':rol'           => 'Cliente',
                ':nombre'        => $nombre,
                ':email'         => $email,
                ':password_hash' => password_hash($password, PASSWORD_BCRYPT),
            ]);

            // 5. Patrón PRG: se redirige al login con un mensaje de éxito.
            header('Location: login.php?status=registrado');
            exit;
        } catch (PDOException $e) {
            // 1062 = clave duplicada: otra persona registró el mismo correo
            // entre la verificación del paso 3 y este INSERT.
            if (($e->errorInfo[1] ?? null) === 1062) {
                $errores['email'] = 'Ya existe una cuenta con ese correo. Iniciá sesión o usá otro correo.';
            } else {
                error_log('Error al registrar usuario: ' . $e->getMessage());
                $errores['general'] = 'No se pudo crear la cuenta. Intentá nuevamente.';
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>
    <main id="contenido" class="auth-main">
      <div class="container">
        <section class="tarjeta auth-card" aria-labelledby="registro-titulo">
          <h1 id="registro-titulo" class="auth-title">Crear Cuenta</h1>

          <?php if (!empty($errores)): ?>
            <div class="alert alert-danger" role="alert">
              <?= e($errores['general'] ?? 'No se pudo crear la cuenta. Revisá los campos marcados en rojo.') ?>
            </div>
          <?php endif; ?>

          <form method="post" action="registro.php" novalidate>
            <div class="mb-3">
              <label class="form-label" for="nombre">Nombre completo</label>
              <input
                class="form-control<?= isset($errores['nombre']) ? ' is-invalid' : '' ?>"
                type="text"
                id="nombre"
                name="nombre"
                value="<?= e($nombre) ?>"
                placeholder="Tu nombre y apellido"
                autocomplete="name"
                maxlength="100"
                required
                <?= isset($errores['nombre']) ? 'aria-invalid="true" aria-describedby="error-nombre"' : '' ?>
              />
              <?php if (isset($errores['nombre'])): ?>
                <div class="invalid-feedback" id="error-nombre"><?= e($errores['nombre']) ?></div>
              <?php endif; ?>
            </div>

            <div class="mb-3">
              <label class="form-label" for="email">Correo Electrónico</label>
              <input
                class="form-control<?= isset($errores['email']) ? ' is-invalid' : '' ?>"
                type="email"
                id="email"
                name="email"
                value="<?= e($email) ?>"
                placeholder="tu@email.com"
                autocomplete="email"
                maxlength="150"
                required
                <?= isset($errores['email']) ? 'aria-invalid="true" aria-describedby="error-email"' : '' ?>
              />
              <?php if (isset($errores['email'])): ?>
                <div class="invalid-feedback" id="error-email"><?= e($errores['email']) ?></div>
              <?php endif; ?>
            </div>

            <div class="mb-3">
              <label class="form-label" for="password">Contraseña</label>
              <input
                class="form-control<?= isset($errores['password']) ? ' is-invalid' : '' ?>"
                type="password"
                id="password"
                name="password"
                autocomplete="new-password"
                minlength="8"
                maxlength="72"
                aria-describedby="ayuda-password<?= isset($errores['password']) ? ' error-password' : '' ?>"
                required
                <?= isset($errores['password']) ? 'aria-invalid="true"' : '' ?>
              />
              <div id="ayuda-password" class="form-text">Mínimo 8 caracteres.</div>
              <?php if (isset($errores['password'])): ?>
                <div class="invalid-feedback" id="error-password"><?= e($errores['password']) ?></div>
              <?php endif; ?>
            </div>

            <div class="mb-3">
              <label class="form-label" for="password2">Repetir contraseña</label>
              <input
                class="form-control<?= isset($errores['password2']) ? ' is-invalid' : '' ?>"
                type="password"
                id="password2"
                name="password2"
                autocomplete="new-password"
                minlength="8"
                maxlength="72"
                required
                <?= isset($errores['password2']) ? 'aria-invalid="true" aria-describedby="error-password2"' : '' ?>
              />
              <?php if (isset($errores['password2'])): ?>
                <div class="invalid-feedback" id="error-password2"><?= e($errores['password2']) ?></div>
              <?php endif; ?>
            </div>

            <div class="form-check mb-4">
              <input
                class="form-check-input<?= isset($errores['terminos']) ? ' is-invalid' : '' ?>"
                type="checkbox"
                id="terminos"
                name="terminos"
                value="1"
                required
                <?= isset($errores['terminos']) ? 'aria-invalid="true" aria-describedby="error-terminos"' : '' ?>
              />
              <label class="form-check-label" for="terminos">
                Acepto los <a href="#">Términos de Servicio</a> y la
                <a href="#">Política de Privacidad</a>
              </label>
              <?php if (isset($errores['terminos'])): ?>
                <div class="invalid-feedback" id="error-terminos"><?= e($errores['terminos']) ?></div>
              <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-acento btn-lg w-100">Crear cuenta</button>
          </form>

          <p class="auth-switch">¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
        </section>
      </div>
    </main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>