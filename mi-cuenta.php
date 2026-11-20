<?php
// mi-cuenta.php
// Vista protegida (TP8): solo se ve con la sesión iniciada, con cualquier rol.
// Es el destino del cliente después del login. Muestra los datos de la cuenta,
// que se leen de la base con el id guardado en $_SESSION['usuario_id'].

require_once __DIR__ . '/includes/auth_guard.php'; // Sin sesión → login.php?error=acceso_denegado
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/includes/funciones.php';

$titulo_pagina      = 'Mi cuenta | SoleMate';
$descripcion_pagina = 'Datos de tu cuenta en SoleMate.';
$pagina_activa      = '';

try {
    $sql = 'SELECT u.nombre, u.email, u.telefono, u.created_at, r.nombre AS rol
            FROM usuarios AS u
            INNER JOIN roles AS r ON u.rol_id = r.id
            WHERE u.id = :id
            LIMIT 1';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $_SESSION['usuario_id']]);
    $usuario = $stmt->fetch();
} catch (PDOException $e) {
    error_log('Error al leer la cuenta: ' . $e->getMessage());
    $usuario = null;
}

// Si la cuenta ya no existe (por ejemplo, se borró de la base), se cierra la sesión.
if (!$usuario) {
    header('Location: logout.php');
    exit;
}

require_once __DIR__ . '/includes/header.php';
?>
    <main id="contenido" class="auth-main">
      <div class="container">
        <section class="tarjeta auth-card" aria-labelledby="cuenta-titulo">
          <h1 id="cuenta-titulo" class="auth-title">Mi cuenta</h1>
          <p>¡Hola, <strong><?= e($usuario['nombre']) ?></strong>! Estos son los datos de tu cuenta.</p>

          <dl class="row mb-4">
            <dt class="col-sm-5">Correo</dt>
            <dd class="col-sm-7"><?= e($usuario['email']) ?></dd>

            <dt class="col-sm-5">Teléfono</dt>
            <dd class="col-sm-7"><?= e($usuario['telefono'] ?? 'Sin cargar') ?></dd>

            <dt class="col-sm-5">Rol</dt>
            <dd class="col-sm-7"><span class="badge text-bg-dark"><?= e($usuario['rol']) ?></span></dd>

            <dt class="col-sm-5">Cliente desde</dt>
            <dd class="col-sm-7"><?= e(date('d/m/Y', strtotime($usuario['created_at']))) ?></dd>
          </dl>

          <div class="d-grid gap-2">
            <?php if ($_SESSION['rol'] === 'Administrador'): ?>
              <a href="admin/index.php" class="btn btn-acento">
                <i class="bi bi-speedometer2" aria-hidden="true"></i> Ir al panel de productos
              </a>
            <?php endif; ?>
            <a href="catalogo.php" class="btn btn-contorno">Seguir comprando</a>
            <a href="logout.php" class="btn btn-outline-danger">
              <i class="bi bi-box-arrow-right" aria-hidden="true"></i> Cerrar sesión
            </a>
          </div>
        </section>
      </div>
    </main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>