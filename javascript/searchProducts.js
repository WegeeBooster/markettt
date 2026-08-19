// -------------------------------------------------------
// VARIABLES GLOBALES
// Accesibles en todo el script
// -------------------------------------------------------
let categoriaActiva = "Todos";  // Qué categoría está seleccionada
let temporizadorBusqueda = null; // Para no buscar en cada letra (ver debounce abajo)
let paginaActual = 1;
let cargandoMas = false; // Candado para no pedir datos 10 veces por segundo

// -------------------------------------------------------
// FUNCIÓN PRINCIPAL: Cargar productos desde PHP
// Contiene funciones para el buen funcionamiento del main
//
// En C++ sería como: vector<Producto> cargarProductos(string buscar, string cat)
//
// "async" significa que la función puede hacer operaciones
// lentas (como pedir datos a un servidor) sin bloquear
// el resto de la página. En C++ sería un hilo o callback.
// -------------------------------------------------------


// Modifica tu función cargarProductos para que acepte si es carga nueva o scroll
 async function cargarProductos(buscar = "", categoria = "", esScroll = false) {
    if (cargandoMas) return;
    cargandoMas = true;

    // Si es una búsqueda nueva, reiniciamos a la página 1
    if (!esScroll) paginaActual = 1;

    const params = new URLSearchParams();
    if (buscar) params.append("buscar", buscar);
    if (categoria && categoria !== "Todos") params.append("categoria", categoria);
    params.append("pagina", paginaActual); // Mandamos la página al PHP

    try {
        const respuesta = await fetch("../back-end/searchProducts.php?" + params.toString());
        const productos = await respuesta.json();

        // Si es scroll, los AGREGAMOS (append). Si es búsqueda nueva, REEMPLAZAMOS (innerHTML)
        if (esScroll) {
            mostrarProductos(productos, true); // Llama a tu función que crea las tarjetas pero sin borrar las anteriores
        } else {
            mostrarProductos(productos, false); // Tu función normal que limpia la pantalla primero
        }
    } catch (error) {
        console.error("Error:", error);
    }

    cargandoMas = false;
}



// -------------------------------------------------------
// MOSTRAR PRODUCTOS EN PANTALLA
//
// Recibe el array de productos y crea el HTML de las tarjetas
// En C++ sería como: void imprimirProductos(vector<Producto> lista)
// -------------------------------------------------------
function mostrarProductos(productos, esScroll = false) {
    const grid = document.getElementById("gridProductos");
    const infoText = document.getElementById("infoResultados");

    // 1. EL FRENO DE VACÍO PARA EL SCROLL
    // Si bajaste hasta el fondo, pero ya no hay más productos en la BD, no hacemos nada.
    if (esScroll && productos.length === 0) {
        return;
    }

    // Si NO es scroll (es una búsqueda nueva) y no hay productos, mostramos el mensaje de error.
    if (!esScroll && productos.length === 0) {
        grid.innerHTML = '<div class="estado-vacio">No se encontraron productos 🔍</div>';
        infoText.textContent = "0 productos";
        return;
    }

    // 2. GENERAR EL HTML
    const html = productos.map(producto => {
        // 1. Intentamos desempaquetar las fotos
        let fotos = [];
        try {
            // Si es un JSON (el formato nuevo), lo convierte en un array de JS
            fotos = JSON.parse(producto.imagen);
        } catch (e) {
            // Si falla (porque es un producto viejo con formato de texto simple),
            // lo metemos a un array nosotros mismos para que el código no truene.
            fotos = producto.imagen ? [producto.imagen] : [];
        }

        // 2. Formateamos el precio

        const precioFormateado = "$" + parseFloat(producto.precio).toLocaleString("es-MX", {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        // 3. Decidimos qué imagen mostrar (la primera del array)
        const imagenHTML = (fotos && fotos.length > 0)
            ? `<img src="${fotos[0]}" alt="${producto.titulo}" style="width: 100%; height: 100%; object-fit: cover; display: block;">`
            : `<div class="icono-caja">Sin imagen 📦</div>`;

        return `
            <div class="tarjeta" onclick="window.location.href='products.html?id=${producto.id_publicacion}'" style="cursor: pointer;">
                <div class="tarjeta-imagen">
                    ${imagenHTML}
                </div>
                <div class="tarjeta-cuerpo">
                    <div class="tarjeta-categoria">${producto.categoria || "Otros"}</div>
                    <div class="tarjeta-nombre">${producto.titulo}</div>
                    <div class="tarjeta-descripcion">${producto.descripcion || ""}</div>
                    <div class="tarjeta-pie">
                        <span class="tarjeta-precio">${precioFormateado}</span>
                    </div>
                </div>
            </div>
        `;
    }).join("");

    // 3. LA MAGIA DE LA PAGINACIÓN
    if (esScroll) {
        // Si es scroll, pegamos las tarjetas nuevas al final de la lista
        grid.insertAdjacentHTML('beforeend', html);
    } else {
        // Si es búsqueda/filtro nuevo, borramos lo viejo y ponemos lo nuevo
        grid.innerHTML = html;
    }

    // 4. EL CONTADOR INTELIGENTE
    // Contamos cuántos elementos con la clase "tarjeta" hay actualmente adentro del grid
    const totalMostrados = grid.querySelectorAll('.tarjeta').length;
    infoText.textContent = totalMostrados + " producto" + (totalMostrados !== 1 ? "s" : "");
}


// -------------------------------------------------------
// CARGAR CATEGORÍAS Y CREAR LOS BOTONES DE FILTRO
//
// Pide todas las categorías únicas a PHP y crea los botones
// -------------------------------------------------------
async function cargarCategorias() {
    try {
        // Traer todos los productos para extraer las categorías
        const respuesta = await fetch("../back-end/searchProducts.php");
        const productos = await respuesta.json();

        // Extraer categorías únicas usando Set (como un set<string> en C++)
        // Set elimina automáticamente los duplicados
        const categorias = ["Todos", ...new Set(productos.map(p => p.categoria))];

        // Crear los botones de filtro
        const contenedor = document.getElementById("contenedorFiltros");
        contenedor.innerHTML = categorias.map(cat => `
                    <button
                        class="btn-categoria ${cat === categoriaActiva ? "activo" : ""}"
                        onclick="seleccionarCategoria('${cat}')"
                    >
                        ${cat}
                    </button>
                `).join("");

    } catch (error) {
        console.error("Error al cargar categorías:", error);
    }
}


// -------------------------------------------------------
// MANEJAR CLIC EN CATEGORÍA
// -------------------------------------------------------
function seleccionarCategoria(categoria) {
    categoriaActiva = categoria;

    // Actualizar los botones: quitar "activo" de todos y ponérselo al seleccionado
    document.querySelectorAll(".btn-categoria").forEach(btn => {
        btn.classList.toggle("activo", btn.textContent.trim() === categoria);
    });

    // Recargar productos con la nueva categoría
    const busqueda = document.getElementById("inputBuscar").value.trim();
    cargarProductos(busqueda, categoriaActiva);
}


// -------------------------------------------------------
// MANEJAR BÚSQUEDA CON "DEBOUNCE"
//
// El debounce evita hacer una petición al servidor en
// CADA letra que escribe el usuario. En cambio, espera
// 400 ms después de que deja de escribir.
//
// En C++ sería como: esperar a que el usuario no presione
// teclas por un tiempo antes de buscar.
// -------------------------------------------------------
function manejarBusqueda() {
    // Cancelar el temporizador anterior (si existe)
    clearTimeout(temporizadorBusqueda);

    // Crear un nuevo temporizador de 400 ms
    // Si el usuario sigue escribiendo, se cancela y se reinicia
    temporizadorBusqueda = setTimeout(() => {
        const busqueda = document.getElementById("inputBuscar").value.trim();
        cargarProductos(busqueda, categoriaActiva);
    }, 400);
}

// =======================================================
// VERIFICAR SI EL USUARIO YA INICIÓ SESIÓN
// =======================================================
async function verificarSesion() {
    try {
        const respuesta = await fetch("../back-end/checkSession.php");
        const datos = await respuesta.json();

        // Buscamos tu etiqueta <a>
        const enlaceLogin = document.getElementById("btn-login");

        if (datos.logeado && enlaceLogin) {
            // 1. Le cambiamos el texto visual
            enlaceLogin.innerHTML = "Mis Chats";

            // 2. Le cambiamos el destino del enlace
            enlaceLogin.href = "inbox.html";

            console.log("Sesión activa para:", datos.nombre);
        }
    } catch (error) {
        console.error("Error al verificar sesión:", error);
    }
}

// -------------------------------------------------------
// ARRANQUE DEL PROGRAMA (Equivalente al int main() en C++)
// Se ejecuta cuando el HTML termina de cargar
// -------------------------------------------------------
window.addEventListener("DOMContentLoaded", () => {
    console.log("Iniciando carga de la página...");

    verificarSesion();
    cargarCategorias(); // Cargar los botones de categoría
    cargarProductos();  // Cargar todos los productos al inicio
});

// EL GATILLO DEL SCROLL
window.addEventListener("scroll", () => {
    // Si la suma de lo que hemos bajado + el tamaño de la pantalla es casi igual al total de la página...
    if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 100) {
        paginaActual++; // Pasamos a la página 2, 3, 4...
        const busqueda = document.getElementById("inputBuscar").value.trim();
        cargarProductos(busqueda, categoriaActiva, true); // true = es un scroll
    }
});


