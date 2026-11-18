-- =====================================================================
-- SoleMate · TP N° 6 · Parte 3: consultas de prueba CRUD (consultas.sql)
-- Inserta datos de prueba y valida la estructura con SELECT, UPDATE
-- y DELETE.
-- Requisito: ejecutar antes schema.sql.
-- Cómo ejecutarlo: phpMyAdmin > pestaña "Importar" > elegir este archivo.
-- =====================================================================

USE solemate_db;

-- =====================================================================
-- 1. CREATE: inserción de datos iniciales
-- =====================================================================
-- schema.sql recrea la base desde cero, así que cada id AUTO_INCREMENT
-- empieza en 1 y sigue el orden de inserción. Los comentarios indican
-- qué id recibe cada valor, para usarlo en las claves foráneas.

-- ---------------------------------------------------------------------
-- 1.1 Tablas maestras
-- ---------------------------------------------------------------------

-- roles: 1 = Administrador, 2 = Cliente
INSERT INTO roles (nombre) VALUES
    ('Administrador'),
    ('Cliente');

-- provincias (las mismas del <select> de checkout.php):
-- 1 = Buenos Aires, 2 = CABA, 3 = Córdoba, 4 = Jujuy,
-- 5 = Mendoza, 6 = Salta, 7 = Santa Fe, 8 = Tucumán
INSERT INTO provincias (nombre) VALUES
    ('Buenos Aires'),
    ('Ciudad Autónoma de Buenos Aires'),
    ('Córdoba'),
    ('Jujuy'),
    ('Mendoza'),
    ('Salta'),
    ('Santa Fe'),
    ('Tucumán');

-- estados_pedido: 1 = Pendiente, 2 = Pagado, 3 = Enviado,
-- 4 = Entregado, 5 = Cancelado
INSERT INTO estados_pedido (nombre) VALUES
    ('Pendiente'),
    ('Pagado'),
    ('Enviado'),
    ('Entregado'),
    ('Cancelado');

-- marcas: 1 = Nike, 2 = Adidas, 3 = Jordan, 4 = New Balance
INSERT INTO marcas (nombre) VALUES
    ('Nike'),
    ('Adidas'),
    ('Jordan'),
    ('New Balance');

-- categorias: 1 = Running, 2 = Basketball, 3 = Lifestyle
INSERT INTO categorias (nombre) VALUES
    ('Running'),
    ('Basketball'),
    ('Lifestyle');

-- origenes: 1 = Chile, 2 = Bolivia
INSERT INTO origenes (nombre) VALUES
    ('Chile'),
    ('Bolivia');

-- ---------------------------------------------------------------------
-- 1.2 Usuarios
-- ---------------------------------------------------------------------
-- Contraseña de prueba de los tres usuarios: SoleMate2026.
-- password_hash guarda el resultado de password_hash() de PHP:
-- la contraseña nunca se guarda en texto plano.
-- 1 = Administrador SoleMate, 2 = Lucía Fernández, 3 = Martín Gómez
INSERT INTO usuarios (rol_id, nombre, email, password_hash, telefono) VALUES
    (1, 'Administrador SoleMate', 'admin@solemate.com',
     '$2y$10$rtAaPbArz.wQNci6OLGxBeKqLP5OFMTk.hnyOeS.3STbCvsk5xVaa', NULL),
    (2, 'Lucía Fernández', 'lucia@mail.com',
     '$2y$10$rtAaPbArz.wQNci6OLGxBeKqLP5OFMTk.hnyOeS.3STbCvsk5xVaa', '387-4111111'),
    (2, 'Martín Gómez', 'martin@mail.com',
     '$2y$10$rtAaPbArz.wQNci6OLGxBeKqLP5OFMTk.hnyOeS.3STbCvsk5xVaa', '381-5222222');

-- ---------------------------------------------------------------------
-- 1.3 Productos (los mismos del catálogo de catalogo.php)
-- ---------------------------------------------------------------------
-- 1 = Nike Air Max 270, 2 = Adidas Ultraboost,
-- 3 = Jordan 1 Retro, 4 = New Balance 990
-- Columnas de FK: marca_id, categoria_id, origen_id.
INSERT INTO productos (marca_id, categoria_id, origen_id, nombre, descripcion, precio, imagen, alt) VALUES
    (1, 1, 1, 'Nike Air Max 270',
     'Zapatilla de running con la unidad Air Max visible en el talón, liviana y cómoda para el uso diario.',
     189900.00, 'img/nike-air-max-270.jpg', 'Zapatilla Nike Air Max 270 blanca con suela roja'),
    (2, 1, 2, 'Adidas Ultraboost',
     'Zapatilla de running con amortiguación Boost y capellada de tejido elástico.',
     219900.00, 'img/adidas-ultraboost.jpg', 'Zapatilla Adidas Ultraboost negra con suela blanca'),
    (3, 2, 1, 'Jordan 1 Retro',
     'Clásico modelo de basketball de caña alta en cuero, inspirado en el diseño original de 1985.',
     249900.00, 'img/jordan-1-retro.jpg', 'Zapatilla Jordan 1 Retro roja, blanca y negra'),
    (4, 3, 2, 'New Balance 990',
     'Zapatilla lifestyle de gamuza y malla, con amortiguación ENCAP y terminación premium.',
     229900.00, 'img/new-balance-990.jpg', 'Zapatilla New Balance 990 gris');

-- Stock de cada producto por talle (incluye el talle que hoy muestra
-- el catálogo para cada zapatilla).
INSERT INTO producto_talle (producto_id, talle, stock) VALUES
    (1, 38, 5), (1, 39, 3), (1, 40, 4),
    (2, 40, 2), (2, 41, 6),
    (3, 41, 3), (3, 42, 2),
    (4, 42, 4), (4, 43, 1);

-- ---------------------------------------------------------------------
-- 1.4 Pedidos (los del ejemplo de NORMALIZACION.md)
-- ---------------------------------------------------------------------
-- 1 = primer pedido de Lucía, 2 = pedido de Martín,
-- 3 = segundo pedido de Lucía.
-- Columnas de FK: usuario_id, provincia_id, estado_id.
INSERT INTO pedidos (usuario_id, provincia_id, estado_id, direccion, ciudad, codigo_postal, total, created_at) VALUES
    (2, 6, 1, 'Av. Belgrano 1250', 'Salta', '4400', 439800.00, '2026-11-10 10:15:00'),
    (3, 8, 3, 'San Martín 455', 'San Miguel de Tucumán', '4000', 439800.00, '2026-11-12 18:40:00'),
    (2, 6, 1, 'Av. Belgrano 1250', 'Salta', '4400', 229900.00, '2026-11-15 09:05:00');

-- Detalle de cada pedido (precio_unitario = precio al momento de la venta).
INSERT INTO detalle_pedidos (pedido_id, producto_id, talle, cantidad, precio_unitario) VALUES
    (1, 1, 38, 1, 189900.00),
    (1, 3, 41, 1, 249900.00),
    (2, 2, 40, 2, 219900.00),
    (3, 4, 42, 1, 229900.00);