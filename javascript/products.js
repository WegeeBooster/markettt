// ============================================================
// ARCHIVO: javascript/producto.js
// Este archivo sirve para el funcionamiento de products.html
// ============================================================

document.addEventListener("DOMContentLoaded", async () => {
    try {
        // Verificar la sesión
        const sesionValida = await verificarSesion();
        if (!sesionValida) return; // Si no está logueado, verificarSesion redirige solo

        // Leer el ID del producto de la URL
        const parametrosURL = new URLSearchParams(window.location.search);
        const idProducto = parametrosURL.get("id");

        if (idProducto) {
            console.log("Cargando detalles del producto ID:", idProducto);
            await cargarDetallesDelProducto(idProducto);
        } else {
            document.body.style.display = "block";
            mostrarMensajeError("Error: No se seleccionó ningún producto en la URL.");
        }
    } catch (error) {
        console.error("Error crítico en la inicialización:", error);
        document.body.style.display = "block";
        mostrarMensajeError("Ocurrió un error inesperado al cargar la interfaz.");
    }
});

// ------------------------------------------------------------
// Función principal: Pide datos a PHP y llena la página
// ------------------------------------------------------------
async function cargarDetallesDelProducto(id) {
    // 1. Mostrar estado de carga (opcional, para mejor experiencia)
    const tituloElemento = document.getElementById("prod-titulo");
    tituloElemento.textContent = "Cargando producto...";

    try {
        // 2. Hacer la petición a PHP
        const respuesta = await fetch(`../back-end/getProduct.php?id=${encodeURIComponent(id)}`);
        if (!respuesta.ok) {
            throw new Error("Error del servidor: " + respuesta.status);
        }

        const datos = await respuesta.json();

        // 3. Verificar si PHP nos mandó un error (ej: producto no encontrado)
        if (datos.error) {
            mostrarMensajeError(` ${datos.error}`);
            return;
        }

        console.log("Datos recibidos:", datos);

        // 4. Llenar la información del PRODUCTO en el HTML
        document.getElementById("prod-categoria").textContent = datos.categoria || "Categoría";
        document.getElementById("prod-titulo").textContent = datos.titulo || "Producto sin título";
        document.getElementById("prod-precio").textContent = formataPrecio(datos.precio);
        document.getElementById("prod-descripcion").textContent = datos.descripcion || "Este producto no tiene una descripción detallada.";

        // ============================================================
        // 5. NUEVO: Llenar la información completa del VENDEDOR
        // ============================================================
        if (datos.vendedor) {
            // Textos básicos
            document.getElementById("vend-nombre").textContent = datos.vendedor.nombre || "Vendedor Desconocido";
            document.getElementById("vend-contacto").textContent = datos.vendedor.departamento || "Sin información";

            const txtHorario = document.getElementById("vend-horario");
            if (txtHorario) txtHorario.textContent = "Horario: " + (datos.vendedor.horario || "No especificado");

            // Foto de perfil
            const imgFoto = document.getElementById("vend-foto");
            if (imgFoto && datos.vendedor.foto) {
                imgFoto.src = datos.vendedor.foto;
            }

            // Botón de WhatsApp
            const btnWa = document.getElementById("btn-whatsapp");
            if (btnWa) {
                if (datos.vendedor.whatsapp) {
                    let waUrl = datos.vendedor.whatsapp.startsWith("http")
                        ? datos.vendedor.whatsapp
                        : `https://wa.me/${datos.vendedor.whatsapp}`;
                    btnWa.href = waUrl;
                    btnWa.style.display = "inline-block";
                } else {
                    btnWa.style.display = "none";
                }
            }

            // Botón de Instagram
            const btnIg = document.getElementById("btn-instagram");
            if (btnIg) {
                if (datos.vendedor.instagram) {
                    let igUser = datos.vendedor.instagram.replace("@", "");
                    let igUrl = datos.vendedor.instagram.startsWith("http")
                        ? datos.vendedor.instagram
                        : `https://instagram.com/${igUser}`;
                    btnIg.href = igUrl;
                    btnIg.style.display = "inline-block";
                } else {
                    btnIg.style.display = "none";
                }
            }
        }

        // 6. Configurar la acción del botón chat (Chat Interno)
        const btnContactar = document.getElementById("btn-contactar");
        if (btnContactar) {
            btnContactar.onclick = () => {
                // Mandamos al usuario a chat.html con los IDs necesarios en la URL
                const urlChat = `chat.html?vendedor=${encodeURIComponent(datos.id_vendedor)}&producto=${encodeURIComponent(id)}`;
                window.location.href = urlChat;
            };
        }

        // 7. --- MAGIA PARA LAS FOTOS ---
        // 'datos.imagen' esta en un formato JSON
        let urlsImagenes = [];

        if (datos.imagen) {
            try {
                // 1. Intentamos desempaquetar el JSON de la base de datos.
                urlsImagenes = JSON.parse(datos.imagen);
            } catch (e) {
                // 2. Si falla el JSON, tratamos la ruta como una única imagen.
                urlsImagenes = [datos.imagen];
            }
        }

        // Llamamos a la función que dibuja el carrusel
        mostrarImagenes(urlsImagenes);

    } catch (error) {
        console.error("Error al cargar producto:", error);
        mostrarMensajeError(`Ocurrió un error al conectar con el servidor: ${error.message}`);
    }
}


// ------------------------------------------------------------
// Funciones auxiliares
// ------------------------------------------------------------

// Formatea un número a precio mexicano: 1200 -> "$1,200.00"
function formataPrecio(precio) {
    if (!precio || isNaN(precio)) return "$0.00";
    return "$" + parseFloat(precio).toLocaleString("es-MX", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

// Crea el contenedor deslizable con todas las fotos
// ============================================================
// CARRUSEL TIPO FACEBOOK MARKETPLACE (Con Swipe y Miniaturas)
// ============================================================
function mostrarImagenes(imagenes) {
    const contenedorPrincipal = document.getElementById("prod-imagen");
    contenedorPrincipal.innerHTML = "";

    // Limpiamos la lista por si vienen URLs vacías
    const imagenesValidas = imagenes.filter(url => url && url.trim() !== "");

    if (imagenesValidas.length === 0) {
        contenedorPrincipal.innerHTML = `<div style="padding: 50px; text-align: center; color: #888;">📦 Sin imágenes</div>`;
        return;
    }

    // 1. INYECTAMOS HTML Y CSS DIRECTO AL CONTENEDOR
    let html = `
        <style>
            /* Estructura Principal */
            .fb-carousel { width: 100%; border-radius: 8px; overflow: hidden; display: flex; flex-direction: column; background: whitesmoke; }
            
            /* Área de la foto grande */
            .fb-track-container { position: relative; width: 100%; height: 400px; overflow: hidden; touch-action: pan-y; }
            
            /* El riel invisible que deslizamos de izquierda a derecha */
            .fb-track { display: flex; width: 100%; height: 100%; transition: transform 0.3s ease-out; }
            
            /* El cuadrado individual de cada foto */
            .fb-slide { flex: 0 0 100%; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; }
            .fb-slide img { max-width: 100%; max-height: 100%; object-fit: contain; pointer-events: none; }

            /* Las flechas flotantes */
            .fb-btn { position: absolute; top: 50%; transform: translateY(-50%); background: rgba(255,255,255,0.9); border: none; width: 45px; height: 45px; border-radius: 50%; font-size: 20px; color: #333; cursor: pointer; z-index: 10; display: flex; align-items: center; justify-content: center; transition: background 0.2s, opacity 0.2s; box-shadow: 0 2px 5px rgba(0,0,0,0.3); }
            .fb-btn:hover { background: #fff; }
            .fb-btn:disabled { opacity: 0; cursor: default; pointer-events: none; }
            .fb-btn-prev { left: 15px; }
            .fb-btn-next { right: 15px; }

            /* Barra de miniaturas de abajo */
            .fb-thumbnails { display: flex; gap: 8px; padding: 12px; background: whitesmoke; overflow-x: auto; scrollbar-width: none; justify-content: center;}
            .fb-thumbnails::-webkit-scrollbar { display: none; }
            
            /* Cada miniatura */
            .fb-thumb { width: 60px; height: 60px; object-fit: cover; border-radius: 6px; cursor: pointer; opacity: 0.4; transition: all 0.2s ease; border: 2px solid transparent; flex-shrink: 0; }
            .fb-thumb.active { opacity: 1; border-color: #0084ff; transform: scale(1.05); }
        </style>
        
        <div class="fb-carousel">
            <div class="fb-track-container" id="swipeArea">
                <button class="fb-btn fb-btn-prev" id="btnPrev" ${imagenesValidas.length <= 1 ? 'disabled' : ''}>❮</button>
                
                <div class="fb-track" id="fbTrack">
                    ${imagenesValidas.map(url => `
                        <div class="fb-slide"><img src="${url.trim()}" alt="Producto"></div>
                    `).join('')}
                </div>

                <button class="fb-btn fb-btn-next" id="btnNext" ${imagenesValidas.length <= 1 ? 'disabled' : ''}>❯</button>
            </div>
            
            <div class="fb-thumbnails">
                ${imagenesValidas.map((url, i) => `
                    <img src="${url.trim()}" class="fb-thumb ${i === 0 ? 'active' : ''}" data-index="${i}">
                `).join('')}
            </div>
        </div>
    `;

    contenedorPrincipal.innerHTML = html;

    // 2. LÓGICA DE MOVIMIENTO Y SWIPE
    let indiceActual = 0;
    const track = document.getElementById("fbTrack");
    const btnPrev = document.getElementById("btnPrev");
    const btnNext = document.getElementById("btnNext");
    const thumbs = document.querySelectorAll(".fb-thumb");
    const swipeArea = document.getElementById("swipeArea");

    function moverA(indice) {
        indiceActual = indice;
        track.style.transform = `translateX(-${indiceActual * 100}%)`;

        btnPrev.disabled = indiceActual === 0;
        btnNext.disabled = indiceActual === imagenesValidas.length - 1;

        thumbs.forEach(t => t.classList.remove("active"));
        thumbs[indiceActual].classList.add("active");
        thumbs[indiceActual].scrollIntoView({ behavior: "smooth", block: "nearest", inline: "center" });
    }

    if(btnPrev) btnPrev.addEventListener("click", () => { if (indiceActual > 0) moverA(indiceActual - 1); });
    if(btnNext) btnNext.addEventListener("click", () => { if (indiceActual < imagenesValidas.length - 1) moverA(indiceActual + 1); });

    thumbs.forEach(thumb => {
        thumb.addEventListener("click", (e) => moverA(parseInt(e.target.dataset.index)));
    });

    let touchStartX = 0;
    let touchEndX = 0;

    swipeArea.addEventListener("touchstart", e => {
        touchStartX = e.changedTouches[0].screenX;
    }, {passive: true});

    swipeArea.addEventListener("touchend", e => {
        touchEndX = e.changedTouches[0].screenX;
        let dif = touchStartX - touchEndX;

        if (dif > 50 && indiceActual < imagenesValidas.length - 1) {
            moverA(indiceActual + 1);
        } else if (dif < -50 && indiceActual > 0) {
            moverA(indiceActual - 1);
        }
    }, {passive: true});
}

function mostrarMensajeError(mensaje) {
    const main = document.querySelector(".contenedor-producto");
    main.innerHTML = `
        <div style="text-align: center; color: red; padding: 40px; border: 2px dashed red; border-radius: 12px; grid-column: 1 / -1;">
            <h2>Error al cargar el producto</h2>
            <p>${mensaje}</p>
            <br>
            <button onclick="window.location.href='main.html'" style="padding: 10px 20px; cursor: pointer;">Volver al MarketPlace</button>
        </div>
    `;
}

async function verificarSesion() {
    try {
        const respuesta = await fetch("../back-end/checkSession.php", {
                credentials: "include" });
        const datos = await respuesta.json();

        if (datos.logeado ) {
            console.log("Sesión activa para:", datos.nombre);
            document.body.style.display = "block";
            return true;
        }else{
             window.location.href = "login.html";
             return false;
        }
    } catch (error) {
         console.log("Error al verificar sesion "+error);
         window.location.href = "login.html";
         return false;
    }
}