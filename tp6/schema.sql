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