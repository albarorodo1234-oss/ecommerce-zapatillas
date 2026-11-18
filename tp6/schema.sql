-- =====================================================================
-- SoleMate · TP N° 6 · Parte 2: script DDL (schema.sql)
-- Crea la base de datos solemate_db con todas sus tablas, claves
-- primarias, claves foráneas e índices.
-- Motor probado: MariaDB 10.11.16 (XAMPP).
-- Cómo ejecutarlo: phpMyAdmin > pestaña "Importar" > elegir este archivo.
-- =====================================================================

-- ---------------------------------------------------------------------
-- 1. Base de datos
-- ---------------------------------------------------------------------
-- ATENCIÓN: borra la base completa (si existe) y la vuelve a crear
-- desde cero. Así el script se puede ejecutar varias veces sin errores,
-- tanto en MariaDB (XAMPP) como en MySQL 8 (Laragon).
DROP DATABASE IF EXISTS solemate_db; 

-- utf8mb4: admite tildes, eñes y cualquier carácter Unicode.
-- utf8mb4_unicode_ci: compara textos sin distinguir mayúsculas de minúsculas.
CREATE DATABASE IF NOT EXISTS solemate_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE solemate_db;

-- ---------------------------------------------------------------------
-- 2. Tablas maestras (listas de valores fijos)
--    Se crean primero porque las demás tablas apuntan a ellas.
-- ---------------------------------------------------------------------

-- Roles de los usuarios del sitio (Administrador, Cliente).
CREATE TABLE IF NOT EXISTS roles (
    id     INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(30) NOT NULL UNIQUE
) ENGINE = InnoDB;

-- Provincias de destino de los envíos (las mismas del checkout).
CREATE TABLE IF NOT EXISTS provincias (
    id     INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(60) NOT NULL UNIQUE
) ENGINE = InnoDB;

-- Estados posibles de un pedido (Pendiente, Pagado, Enviado...).
CREATE TABLE IF NOT EXISTS estados_pedido (
    id     INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(30) NOT NULL UNIQUE
) ENGINE = InnoDB;

-- Marcas de zapatillas (Nike, Adidas, Jordan, New Balance).
CREATE TABLE IF NOT EXISTS marcas (
    id     INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
) ENGINE = InnoDB;

-- Categorías o tipos de zapatilla (Running, Basketball, Lifestyle).
CREATE TABLE IF NOT EXISTS categorias (
    id     INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
) ENGINE = InnoDB;

-- Países de origen de la importación (Chile, Bolivia).
CREATE TABLE IF NOT EXISTS origenes (
    id     INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
) ENGINE = InnoDB;

-- ---------------------------------------------------------------------
-- 3. Usuarios y productos
-- ---------------------------------------------------------------------

-- Usuarios del sitio: datos de acceso (email y contraseña cifrada),
-- rol y datos personales.
CREATE TABLE IF NOT EXISTS usuarios (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    rol_id        INT NOT NULL,
    nombre        VARCHAR(100) NOT NULL,
    email         VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    telefono      VARCHAR(30) NULL,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_usuarios_roles
        FOREIGN KEY (rol_id) REFERENCES roles (id)
        ON DELETE RESTRICT
) ENGINE = InnoDB;

-- Productos del catálogo (las columnas coinciden con el arreglo
-- $productos de catalogo.php, para reemplazarlo en el TP7).
CREATE TABLE IF NOT EXISTS productos (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    marca_id     INT NOT NULL,
    categoria_id INT NOT NULL,
    origen_id    INT NOT NULL,
    nombre       VARCHAR(100) NOT NULL,
    descripcion  TEXT NULL,
    precio       DECIMAL(10, 2) NOT NULL,
    imagen       VARCHAR(255) NULL,
    alt          VARCHAR(255) NULL,
    activo       TINYINT(1) NOT NULL DEFAULT 1,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_productos_marcas
        FOREIGN KEY (marca_id) REFERENCES marcas (id)
        ON DELETE RESTRICT,
    CONSTRAINT fk_productos_categorias
        FOREIGN KEY (categoria_id) REFERENCES categorias (id)
        ON DELETE RESTRICT,
    CONSTRAINT fk_productos_origenes
        FOREIGN KEY (origen_id) REFERENCES origenes (id)
        ON DELETE RESTRICT
) ENGINE = InnoDB;

-- ---------------------------------------------------------------------
-- 4. Pedidos y tablas de relación
-- ---------------------------------------------------------------------

-- Pedidos de los clientes. La fecha del pedido es created_at.
-- La dirección de envío se guarda en cada pedido (puede cambiar
-- de una compra a otra).
CREATE TABLE IF NOT EXISTS pedidos (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id    INT NOT NULL,
    provincia_id  INT NOT NULL,
    estado_id     INT NOT NULL,
    direccion     VARCHAR(150) NOT NULL,
    ciudad        VARCHAR(100) NOT NULL,
    codigo_postal VARCHAR(10) NOT NULL,
    total         DECIMAL(10, 2) NOT NULL,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_pedidos_usuarios
        FOREIGN KEY (usuario_id) REFERENCES usuarios (id)
        ON DELETE RESTRICT,
    CONSTRAINT fk_pedidos_provincias
        FOREIGN KEY (provincia_id) REFERENCES provincias (id)
        ON DELETE RESTRICT,
    CONSTRAINT fk_pedidos_estados
        FOREIGN KEY (estado_id) REFERENCES estados_pedido (id)
        ON DELETE RESTRICT
) ENGINE = InnoDB;

-- Detalle de cada pedido: relación N:M entre pedidos y productos.
-- precio_unitario guarda el precio al momento de la venta.
CREATE TABLE IF NOT EXISTS detalle_pedidos (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id       INT NOT NULL,
    producto_id     INT NOT NULL,
    talle           TINYINT UNSIGNED NOT NULL,
    cantidad        INT UNSIGNED NOT NULL,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    CONSTRAINT uq_detalle_pedido_producto_talle
        UNIQUE (pedido_id, producto_id, talle),
    CONSTRAINT fk_detalle_pedidos
        FOREIGN KEY (pedido_id) REFERENCES pedidos (id)
        ON DELETE CASCADE,
    CONSTRAINT fk_detalle_productos
        FOREIGN KEY (producto_id) REFERENCES productos (id)
        ON DELETE RESTRICT
) ENGINE = InnoDB;

-- Stock de cada producto en cada talle.  
CREATE TABLE IF NOT EXISTS producto_talle (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,
    talle       TINYINT UNSIGNED NOT NULL,
    stock       INT UNSIGNED NOT NULL DEFAULT 0,
    CONSTRAINT uq_producto_talle
        UNIQUE (producto_id, talle),
    CONSTRAINT fk_producto_talle_productos
        FOREIGN KEY (producto_id) REFERENCES productos (id)
        ON DELETE CASCADE
) ENGINE = InnoDB;

-- ---------------------------------------------------------------------
-- 5. Índices
-- ---------------------------------------------------------------------
-- MySQL ya crea índices automáticos para cada PRIMARY KEY, cada UNIQUE
-- (por ejemplo usuarios.email) y cada clave foránea (por ejemplo
-- productos.categoria_id). Crear otro índice sobre esas columnas sería
-- un duplicado, por eso se indexan columnas que todavía no tienen índice
-- y que el sitio filtra u ordena con frecuencia.

-- El catálogo filtra por precio máximo y ordena por precio
-- (menor / mayor precio).
CREATE INDEX idx_productos_precio ON productos (precio);

-- El historial de pedidos se ordena y se filtra por fecha.
CREATE INDEX idx_pedidos_created_at ON pedidos (created_at);