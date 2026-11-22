// js/main.js
// JavaScript del sitio SoleMate. Lo carga includes/footer.php en todas las páginas.
//
// TP9: catálogo con comunicación asincrónica (AJAX) usando Fetch API y JSON.
// En catalogo.php, la búsqueda del menú, los filtros y el orden ya no recargan
// la página: JavaScript pide los productos a api/productos.php en segundo plano,
// recibe un JSON y reemplaza solo la grilla de tarjetas y el contador.
// Si JavaScript no funciona, el catálogo sigue andando como antes (formularios GET).

document.addEventListener("DOMContentLoaded", () => {
  iniciarCatalogoAjax();
});

function iniciarCatalogoAjax() {
  // ----- 1. Elementos de la página -----
  // Si no existe la grilla, no estamos en el catálogo: no se hace nada.
  const grilla = document.getElementById("grilla-productos");
  const formFiltros = document.getElementById("filtros");
  const plantilla = document.getElementById("plantilla-producto");
  if (!grilla || !formFiltros || !plantilla) {
    return;
  }

  const contador = document.getElementById("contador-resultados");
  const estado = document.getElementById("estado-catalogo"); // "sin resultados"
  const avisos = document.getElementById("avisos-catalogo"); // errores (400, 500, red)
  const inputBusqueda = document.getElementById("buscar"); // buscador del menú (nav.php)
  const formBusqueda = inputBusqueda ? inputBusqueda.form : null;
  const campoBusqueda = formFiltros.elements.namedItem("q"); // campo oculto del formulario

  const ESPERA_MS = 300; // debounce: espera a que el usuario deje de escribir
  const formatoPrecio = new Intl.NumberFormat("es-AR", { maximumFractionDigits: 0 });

  let temporizador = null; // setTimeout pendiente de la búsqueda
  let controlador = null; // AbortController de la petición en curso

  // El buscador del menú arranca con la búsqueda actual (por ejemplo, ?q=nike).
  if (inputBusqueda) {
    inputBusqueda.value = campoBusqueda.value;
  }

  // ----- 2. Eventos del usuario -----

  // a) Mientras se escribe en el buscador (evento input) se espera 300 ms
  //    sin teclear antes de pedir los datos: así no se hace una petición por letra.
  if (inputBusqueda) {
    inputBusqueda.addEventListener("input", () => {
      campoBusqueda.value = inputBusqueda.value.trim();
      clearTimeout(temporizador);
      temporizador = setTimeout(cargarProductos, ESPERA_MS);
    });
  }

  // b) Enter en el buscador: se evita el envío normal (que recarga la página).
  if (formBusqueda) {
    formBusqueda.addEventListener("submit", (evento) => {
      evento.preventDefault();
      clearTimeout(temporizador);
      campoBusqueda.value = inputBusqueda.value.trim();
      cargarProductos();
    });
  }

  // c) Botón "Aplicar" y selector de orden (que llama a requestSubmit()).
  formFiltros.addEventListener("submit", (evento) => {
    evento.preventDefault();
    cargarProductos();
  });

  // d) Cada cambio de talla, precio, marca u origen se aplica en el momento.
  formFiltros.addEventListener("change", () => {
    cargarProductos();
  });

  // ----- 3. Petición asincrónica con Fetch API (async/await) -----
  async function cargarProductos() {
    // Los mismos datos que enviaría el formulario: q, talla[], precio_max,
    // marca[], origen[] y orden (el select pertenece al formulario por form="filtros").
    const parametros = new URLSearchParams(new FormData(formFiltros));
    for (const [clave, valor] of [...parametros]) {
      if (valor === "") {
        parametros.delete(clave); // sin "q=" ni "orden=" vacíos en la URL
      }
    }
    const rangoPrecio = formFiltros.elements.namedItem("precio_max");
    if (rangoPrecio && parametros.get("precio_max") === rangoPrecio.max) {
      parametros.delete("precio_max"); // el precio está al máximo: no se filtra por precio
    }
    const consulta = parametros.toString();

    // Si había una petición sin terminar, se cancela: solo importa la última.
    if (controlador) {
      controlador.abort();
    }
    controlador = new AbortController();
    const estaPeticion = controlador;

    mostrarCargando(true);

    try {
      const respuesta = await fetch(`api/productos.php?${consulta}`, {
        headers: { Accept: "application/json" },
        signal: estaPeticion.signal,
      });

      // Se lee el cuerpo JSON también en los errores (400, 404, 500),
      // porque la API explica el problema en "message".
      let datos = null;
      try {
        datos = await respuesta.json();
      } catch {
        datos = null; // la respuesta no era JSON válido
      }

      // fetch() solo falla ante errores de red: un 400 o un 500 llegan
      // como respuesta "normal", por eso hay que revisar response.ok.
      if (!respuesta.ok || !datos || datos.status !== "success") {
        const error = new Error(datos?.message ?? `Error HTTP: ${respuesta.status}`);
        error.codigo = respuesta.status;
        error.detalles = datos?.errores ?? {};
        throw error;
      }

      // ----- 4. Actualización del DOM con los datos JSON -----
      renderizarProductos(datos.data, parametros.get("q") ?? "");
      avisos.replaceChildren(); // se borran avisos anteriores

      // La dirección de la barra refleja los filtros (sin recargar la página),
      // para que al actualizar o compartir el enlace se vea lo mismo.
      history.replaceState(null, "", consulta ? `catalogo.php?${consulta}` : "catalogo.php");
    } catch (error) {
      if (error.name === "AbortError") {
        return; // la cancelamos nosotros porque llegó una búsqueda más nueva
      }
      console.error("Error al cargar el catálogo:", error);
      mostrarError(error);
    } finally {
      if (estaPeticion === controlador) {
        mostrarCargando(false);
      }
    }
  }

  // Reemplaza las tarjetas de la grilla: .map() convierte cada producto
  // del JSON en una tarjeta HTML.
  function renderizarProductos(productos, busqueda) {
    const tarjetas = productos.map(crearTarjeta);
    grilla.replaceChildren(...tarjetas);

    actualizarContador(productos.length, busqueda);

    estado.replaceChildren();
    if (productos.length === 0) {
      estado.append(crearAlertaSinResultados());
    }
  }

  // Copia la <template> de catalogo.php y completa sus datos.
  // Se usa textContent (no innerHTML): el texto nunca se interpreta como
  // HTML, así un nombre con <script> no se ejecuta (prevención de XSS).
  function crearTarjeta(producto) {
    const tarjeta = plantilla.content.firstElementChild.cloneNode(true);

    const imagen = tarjeta.querySelector("img");
    if (producto.imagen) {
      imagen.src = producto.imagen;
      imagen.alt = producto.alt ?? "";
    } else {
      imagen.remove();
    }

    tarjeta.querySelector(".badge-origen").textContent = producto.origen;
    tarjeta.querySelector(".talla-ref").textContent = producto.talles;

    const enlace = tarjeta.querySelector(".product-name a");
    enlace.href = `producto.php?id=${encodeURIComponent(producto.id)}`;
    enlace.textContent = producto.nombre;

    tarjeta.querySelector(".product-type").textContent =
      `Zapatilla ${producto.categoria.toLowerCase()}`;
    tarjeta.querySelector(".price").textContent = `ARS $${formatoPrecio.format(producto.precio)}`;
    tarjeta
      .querySelector(".btn-add")
      .setAttribute("aria-label", `Añadir ${producto.nombre} al carrito`);

    return tarjeta;
  }

  // "Mostrando 2 productos · Buscaste: nike"
  function actualizarContador(total, busqueda) {
    contador.textContent = `Mostrando ${total} ${total === 1 ? "producto" : "productos"}`;
    if (busqueda) {
      const resaltado = document.createElement("strong");
      resaltado.textContent = busqueda;
      contador.append(" · Buscaste: ", resaltado);
    }
  }

  // Mientras se espera la respuesta, la grilla se atenúa y se marca como "ocupada"
  // (aria-busy) para los lectores de pantalla.
  function mostrarCargando(cargando) {
    grilla.setAttribute("aria-busy", cargando ? "true" : "false");
    grilla.style.opacity = cargando ? "0.5" : "";
  }

  // Muestra el error arriba del listado. Las tarjetas anteriores quedan visibles.
  function mostrarError(error) {
    let alerta;

    if (error.codigo === 400) {
      // Filtros inválidos: la API envía un aviso por cada filtro.
      alerta = crearAlerta("warning", error.message);
      const lista = document.createElement("ul");
      lista.className = "mb-0";
      for (const detalle of Object.values(error.detalles)) {
        const item = document.createElement("li");
        item.textContent = detalle;
        lista.append(item);
      }
      alerta.append(lista);
    } else if (error.codigo) {
      // Error del servidor (por ejemplo, 500 si la base de datos no responde).
      alerta = crearAlerta(
        "danger",
        `No pudimos cargar el catálogo: ${error.message} Intentá nuevamente más tarde.`,
      );
    } else {
      // Sin código HTTP: falló la red (servidor apagado, sin conexión).
      alerta = crearAlerta(
        "danger",
        "No pudimos conectar con el servidor. Revisá tu conexión e intentá nuevamente.",
      );
    }

    avisos.replaceChildren(alerta);
  }

  function crearAlerta(tipo, texto) {
    const alerta = document.createElement("div");
    alerta.className = `alert alert-${tipo}`;
    alerta.setAttribute("role", "alert");
    alerta.textContent = texto;
    return alerta;
  }

  // Mismo mensaje que muestra catalogo.php cuando no hay resultados.
  function crearAlertaSinResultados() {
    const alerta = document.createElement("div");
    alerta.className = "alert alert-info";
    alerta.setAttribute("role", "status");

    const enlace = document.createElement("a");
    enlace.href = "catalogo.php";
    enlace.className = "alert-link";
    enlace.textContent = "limpiá la búsqueda";

    alerta.append(
      "No encontramos zapatillas con esos filtros. Probá con menos filtros o ",
      enlace,
      ".",
    );
    return alerta;
  }
}
