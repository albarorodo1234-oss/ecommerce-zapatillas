<?php
// admin/formulario_producto.php
// Formulario de producto compartido por crear.php y editar.php.
// Antes de incluirlo, la página define: $datos, $errores, $opciones,
// $imagenes y $texto_boton.

// Si alguien abre este archivo directo desde el navegador, no muestra nada.
if (!isset($datos, $errores, $opciones, $imagenes, $texto_boton)) {
    http_response_code(404);
    exit;
}
?>
            <?php if (!empty($errores)): ?>
              <div class="alert alert-danger" role="alert">
                <?= e($errores['general'] ?? 'No se pudo guardar el producto. Revisá los campos marcados en rojo.') ?>
              </div>
            <?php endif; ?>

            <!-- Sin "action": el formulario se envía a la misma página (y conserva el ?id= al editar) -->
            <form method="post" novalidate>
              <div class="row g-3">
                <div class="col-12">
                  <label class="form-label" for="nombre">Nombre del producto</label>
                  <input
                    class="form-control<?= clase_invalida($errores, 'nombre') ?>"
                    type="text"
                    id="nombre"
                    name="nombre"
                    value="<?= e($datos['nombre']) ?>"
                    maxlength="100"
                    required
                    <?= atributos_error($errores, 'nombre') ?>
                  />
                  <?php mostrar_error($errores, 'nombre'); ?>
                </div>

                <?php
                // Las tres listas desplegables se arman con el mismo código.
                $listas = [
                    'marca_id'     => ['Marca', 'marcas'],
                    'categoria_id' => ['Categoría', 'categorias'],
                    'origen_id'    => ['Origen', 'origenes'],
                ];
                ?>
                <?php foreach ($listas as $campo => [$etiqueta, $tabla]): ?>
                  <div class="col-md-4">
                    <label class="form-label" for="<?= e($campo) ?>"><?= e($etiqueta) ?></label>
                    <select
                      class="form-select<?= clase_invalida($errores, $campo) ?>"
                      id="<?= e($campo) ?>"
                      name="<?= e($campo) ?>"
                      required
                      <?= atributos_error($errores, $campo) ?>
                    >
                      <option value="" <?= (string) $datos[$campo] === '' ? 'selected' : '' ?>>Elegí una opción</option>
                      <?php foreach ($opciones[$tabla] as $id => $nombre): ?>
                        <option value="<?= e($id) ?>" <?= (string) $datos[$campo] === (string) $id ? 'selected' : '' ?>><?= e($nombre) ?></option>
                      <?php endforeach; ?>
                    </select>
                    <?php mostrar_error($errores, $campo); ?>
                  </div>
                <?php endforeach; ?>

                <div class="col-md-6">
                  <label class="form-label" for="precio">Precio (ARS)</label>
                  <input
                    class="form-control<?= clase_invalida($errores, 'precio') ?>"
                    type="number"
                    id="precio"
                    name="precio"
                    value="<?= e($datos['precio']) ?>"
                    min="1000"
                    step="0.01"
                    required
                    <?= atributos_error($errores, 'precio') ?>
                  />
                  <div class="form-text">Sin puntos de miles. Ejemplo: 189900</div>
                  <?php mostrar_error($errores, 'precio'); ?>
                </div>

                <div class="col-md-6 d-flex align-items-center">
                  <div class="form-check mt-md-4">
                    <input
                      class="form-check-input"
                      type="checkbox"
                      id="activo"
                      name="activo"
                      value="1"
                      <?= $datos['activo'] ? 'checked' : '' ?>
                    />
                    <label class="form-check-label" for="activo">Visible en el catálogo</label>
                  </div>
                </div>

                <div class="col-12">
                  <label class="form-label" for="descripcion">Descripción (opcional)</label>
                  <textarea
                    class="form-control<?= clase_invalida($errores, 'descripcion') ?>"
                    id="descripcion"
                    name="descripcion"
                    rows="3"
                    maxlength="1000"
                    <?= atributos_error($errores, 'descripcion') ?>
                  ><?= e($datos['descripcion']) ?></textarea>
                  <?php mostrar_error($errores, 'descripcion'); ?>
                </div>

                <div class="col-md-6">
                  <label class="form-label" for="imagen">Imagen (opcional)</label>
                  <select
                    class="form-select<?= clase_invalida($errores, 'imagen') ?>"
                    id="imagen"
                    name="imagen"
                    <?= atributos_error($errores, 'imagen') ?>
                  >
                    <option value="" <?= $datos['imagen'] === '' ? 'selected' : '' ?>>Sin imagen</option>
                    <?php foreach ($imagenes as $ruta => $archivo): ?>
                      <option value="<?= e($ruta) ?>" <?= $datos['imagen'] === $ruta ? 'selected' : '' ?>><?= e($archivo) ?></option>
                    <?php endforeach; ?>
                  </select>
                  <div class="form-text">Archivos de la carpeta img/ del sitio.</div>
                  <?php mostrar_error($errores, 'imagen'); ?>
                </div>

                <div class="col-md-6">
                  <label class="form-label" for="alt">Texto alternativo de la imagen</label>
                  <input
                    class="form-control<?= clase_invalida($errores, 'alt') ?>"
                    type="text"
                    id="alt"
                    name="alt"
                    value="<?= e($datos['alt']) ?>"
                    maxlength="255"
                    placeholder="Ej.: Zapatilla Nike blanca con suela roja"
                    <?= atributos_error($errores, 'alt') ?>
                  />
                  <div class="form-text">Obligatorio si elegís una imagen.</div>
                  <?php mostrar_error($errores, 'alt'); ?>
                </div>

                <div class="col-12 d-flex flex-wrap gap-2">
                  <button type="submit" class="btn btn-acento"><?= e($texto_boton) ?></button>
                  <a href="index.php" class="btn btn-contorno">Cancelar</a>
                </div>
              </div>
            </form>