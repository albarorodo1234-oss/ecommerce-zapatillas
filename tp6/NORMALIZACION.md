# TP N° 6 – Normalización de la base de datos de SoleMate

Parte 1 del Trabajo Práctico N° 6 (Programación Web): diseño del esquema relacional del e-commerce SoleMate aplicando las formas normales 1FN, 2FN y 3FN.

## 1. Tabla sin normalizar

Para el ejemplo se toma la entidad principal del negocio: el **pedido**. Si SoleMate registrara sus ventas en una única planilla, cada pedido sería una fila con todos los datos mezclados:

| id_pedido | fecha      | cliente         | email_cliente   | rol_cliente | direccion_envio                                 | provincia | productos                                                                                                                                       | estado    | total  |
| --------- | ---------- | --------------- | --------------- | ----------- | ----------------------------------------------- | --------- | ----------------------------------------------------------------------------------------------------------------------------------------------- | --------- | ------ |
| 1001      | 2026-11-10 | Lucía Fernández | lucia@mail.com  | Cliente     | Av. Belgrano 1250, Salta (CP 4400)              | Salta     | Nike Air Max 270 · Nike · Running · Chile · talle 38 · 1 u. · $189900; Jordan 1 Retro · Jordan · Basketball · Chile · talle 41 · 1 u. · $249900 | Pendiente | 439800 |
| 1002      | 2026-11-12 | Martín Gómez    | martin@mail.com | Cliente     | San Martín 455, San Miguel de Tucumán (CP 4000) | Tucumán   | Adidas Ultraboost · Adidas · Running · Bolivia · talle 40 · 2 u. · $219900                                                                      | Enviado   | 439800 |
| 1003      | 2026-11-15 | Lucía Fernández | lucia@mail.com  | Cliente     | Av. Belgrano 1250, Salta (CP 4400)              | Salta     | New Balance 990 · New Balance · Lifestyle · Bolivia · talle 42 · 1 u. · $229900                                                                 | Pendiente | 229900 |

### Problemas que presenta esta tabla

- **Campo multivalorado:** la columna `productos` guarda **una lista** de artículos en una sola celda (el pedido 1001 tiene dos zapatillas). No se puede consultar ni sumar cada producto por separado.
- **Campos no atómicos:** cada elemento de `productos` mezcla seis datos distintos (nombre, marca, categoría, origen, talle, cantidad y precio), y `direccion_envio` mezcla calle, ciudad y código postal.
- **Redundancia:** los datos de Lucía (nombre, email, rol y dirección) se repiten en los pedidos 1001 y 1003, y los datos de cada producto (marca, categoría y origen) se repetirían en cada pedido que lo incluya.
- **Anomalías que provoca:**
  - **De inserción:** no se puede registrar un producto nuevo del catálogo hasta que alguien lo compre.
  - **De actualización:** si Lucía cambia su email, hay que corregirlo en todas sus filas; si se olvida una, la base queda con datos contradictorios.
  - **De eliminación:** si se borra el pedido 1002, se pierde el único registro de que existe la Adidas Ultraboost y de que existe el cliente Martín.

## 2. Primera Forma Normal (1FN)

_Pendiente._

## 3. Segunda Forma Normal (2FN)

_Pendiente._

## 4. Tercera Forma Normal (3FN)

_Pendiente._

## 5. Tablas resultantes (PK y FK)

_Pendiente._
