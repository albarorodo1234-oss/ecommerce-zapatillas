<?php
// login.php
// Inicio de sesión (TP8): busca al usuario por correo, compara la contraseña
// con el hash guardado usando password_verify() y, si es correcta, regenera
// el id de sesión y guarda en $_SESSION el id, el nombre y el rol.
// Toda la lógica va ANTES del header, porque al terminar se redirige.

require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/includes/funciones.php';
require_once __DIR__ . '/includes/sesion.php';

// Lleva a cada usuario a su página según el rol: el Administrador va a su panel
// y el resto, al inicio de la tienda.
function redirigir_segun_rol(string $rol): never
{
    header('Location: ' . ($rol === 'Administrador' ? 'admin/index.php' : 'index.php'));
    exit;
}

// Si ya inició sesión, no se muestra el formulario otra vez.
if (isset($_SESSION['usuario_id'])) {
    redirigir_segun_rol($_SESSION['rol'] ?? '');
}

$titulo_pagina      = "Iniciar sesión | SoleMate";
$descripcion_pagina = "Ingresá a tu cuenta de SoleMate para ver tus pedidos y comprar zapatillas importadas de Chile y Bolivia.";

// Mensajes que llegan por la URL (lista blanca: lo que se escriba en la
// URL nunca se imprime directamente). Clave => [color de Bootstrap, texto].
$avisos = [
    'acceso_denegado' => ['warning', 'Iniciá sesión para acceder a esa página.'],
    'registrado'      => ['success', 'Tu cuenta se creó correctamente. Ya podés iniciar sesión.'],
    'logout'          => ['info', 'Cerraste sesión correctamente. ¡Te esperamos pronto!'],
];
$aviso = $avisos[dato_get('error')] ?? $avisos[dato_get('status')] ?? null;

$email = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aviso    = null; // Al enviar el formulario solo se muestra el resultado del intento.
    $email    = mb_strtolower(dato_post('email'));
    $password = isset($_POST['password']) && is_string($_POST['password']) ? $_POST['password'] : '';

    if ($email === '' || $password === '') {
        $error = 'Completá el correo y la contraseña.';
    } else {
        try {
            // 1. Buscar al usuario por correo, con el nombre de su rol (JOIN a roles).
            $sql = 'SELECT u.id, u.nombre, u.password_hash, r.nombre AS rol
                    FROM usuarios AS u
                    INNER JOIN roles AS r ON u.rol_id = r.id
                    WHERE u.email = :email
                    LIMIT 1';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':email' => $email]);
            $usuario = $stmt->fetch();

            // 2. Comparar la contraseña escrita con el hash guardado.
            if ($usuario && password_verify($password, $usuario['password_hash'])) {
                // 3. Nuevo id de sesión (true = borra el anterior): evita la
                //    fijación de sesión (Session Fixation).
                session_regenerate_id(true);

                // 4. Datos claves del usuario en la sesión (nunca la contraseña).
                $_SESSION['usuario_id']     = (int) $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['rol']            = $usuario['rol'];

                // 5. Redirigir al panel o a la tienda, según el rol.
                redirigir_segun_rol($usuario['rol']);
            }

            // Mismo mensaje si el correo no existe o si la contraseña es
            // incorrecta: así no se revela qué correos están registrados.
            $error = 'Correo o contraseña incorrectos.';
        } catch (PDOException $e) {
            error_log('Error al iniciar sesión: ' . $e->getMessage());
            $error = 'Ocurrió un error inesperado. Intentá nuevamente.';
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>
    <main id="contenido" class="auth-main">
      <div class="container">
        <section class="tarjeta auth-card" aria-labelledby="login-titulo">
          <h1 id="login-titulo" class="auth-title">Inicia Sesión</h1>

          <?php if ($aviso): ?>
            <div class="alert alert-<?= e($aviso[0]) ?>" role="alert"><?= e($aviso[1]) ?></div>
          <?php endif; ?>

          <?php if ($error !== ''): ?>
            <div class="alert alert-danger" role="alert"><?= e($error) ?></div>
          <?php endif; ?>

          <form method="post" action="login.php" novalidate>
            <div class="mb-3">
              <label class="form-label" for="email">Correo Electrónico</label>
              <input
                class="form-control"
                type="email"
                id="email"
                name="email"
                value="<?= e($email) ?>"
                placeholder="tu@email.com"
                autocomplete="email"
                maxlength="150"
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

            <button type="submit" class="btn btn-acento btn-lg w-100">Ingresar</button>
          </form>

          <p class="auth-switch">¿No tienes cuenta? <a href="registro.php">Regístrate</a></p>
        </section>
      </div>
    </main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>