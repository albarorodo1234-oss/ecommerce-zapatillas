<?php
$titulo_pagina      = "Guía de tallas | SoleMate";
$descripcion_pagina = "Tabla de equivalencias de tallas de zapatillas en Argentina, EE. UU., Europa y centímetros para elegir tu talla ideal en SoleMate.";
require_once 'includes/header.php';
?>
    <main id="contenido">
      <div class="container section-padding pt-4">
        <nav aria-label="Ruta de navegación">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
            <li class="breadcrumb-item active" aria-current="page">Guía de tallas</li>
          </ol>
        </nav>

        <section class="tarjeta size-card" aria-labelledby="tallas-titulo">
          <h1 id="tallas-titulo" class="page-title mb-4">Guía de Tallas</h1>

          <div class="table-responsive">
            <table class="table size-table">
              <caption class="visually-hidden">
                Equivalencias de tallas entre Argentina, Estados Unidos, Europa y centímetros
              </caption>
              <thead>
                <tr>
                  <th scope="col">ARG</th>
                  <th scope="col">US</th>
                  <th scope="col">EU</th>
                  <th scope="col">CM</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>38</td>
                  <td>5.5</td>
                  <td>38</td>
                  <td>24</td>
                </tr>
                <tr>
                  <td>39</td>
                  <td>6.5</td>
                  <td>39</td>
                  <td>24.5</td>
                </tr>
                <tr>
                  <td>40</td>
                  <td>7</td>
                  <td>40</td>
                  <td>25</td>
                </tr>
                <tr>
                  <td>41</td>
                  <td>8</td>
                  <td>41</td>
                  <td>26</td>
                </tr>
                <tr>
                  <td>42</td>
                  <td>8.5</td>
                  <td>42</td>
                  <td>26.5</td>
                </tr>
                <tr>
                  <td>43</td>
                  <td>9.5</td>
                  <td>43</td>
                  <td>27.5</td>
                </tr>
                <tr>
                  <td>44</td>
                  <td>10</td>
                  <td>44</td>
                  <td>28</td>
                </tr>
              </tbody>
            </table>
          </div>

          <p class="size-note">
            Referencia orientativa · US hombre. El calce puede variar según la marca.
          </p>
        </section>

        <p class="mt-4">
          <a class="btn btn-acento" href="catalogo.php">Volver al catálogo</a>
        </p>
      </div>
    </main>

<?php require_once 'includes/footer.php'; ?>