// ============================================================
// ARCHIVO: javascript/chat.js
// ============================================================

document.addEventListener("DOMContentLoaded", () => {

    const parametrosURL = new URLSearchParams(window.location.search);
    const idVendedor = parametrosURL.get("vendedor");
    const idProducto = parametrosURL.get("producto");

    // ========================================================
    // CARGAR CABECERA
    // ========================================================
    async function cargarCabecera() {
        try {
            const respuesta = await fetch(
                `../back-end/getProduct.php?id=${encodeURIComponent(idProducto)}`
            );

            const datos = await respuesta.json();

            if (!datos.error) {
                document.getElementById("chat-nombre-vendedor").textContent =
                    datos.nombre_vendedor;

                document.getElementById("chat-titulo-producto").textContent =
                    datos.titulo;
            }
        } catch (error) {
            console.error("Error al cargar la cabecera:", error);
        }
    }

    if (!idVendedor || !idProducto) {
        alert("Error: Faltan datos para iniciar el chat.");
        window.location.href = "main.html";
        return;
    }

    const cajaMensajes = document.getElementById("caja-mensajes");
    const inputMensaje = document.getElementById("input-mensaje");
    const btnEnviar = document.getElementById("btn-enviar-mensaje");

    let offsetMensajes = 0;
    let cargandoHistorial = false;
    let mensajesCargados = new Set();

    // ========================================================
    // CARGAR HISTORIAL
    // ========================================================
    async function cargarHistorial(esScrollHaciaArriba = false) {

        if (cargandoHistorial) return;
        cargandoHistorial = true;

        try {

            const url =
                `../back-end/loadMessage.php` +
                `?vendedor=${encodeURIComponent(idVendedor)}` +
                `&producto=${encodeURIComponent(idProducto)}` +
                `&offset=${offsetMensajes}`;

            const respuesta = await fetch(url);
            const mensajes = await respuesta.json();

            if (mensajes.error) {
                console.error("Error del servidor:", mensajes.error);
                cargandoHistorial = false;
                return;
            }

            const fragmento = document.createDocumentFragment();
            const alturaAnterior = cajaMensajes.scrollHeight;
            let nuevosAgregados = false;

            if (offsetMensajes === 0 && mensajesCargados.size === 0) {
                cajaMensajes.innerHTML = '';
            }

            mensajes.forEach(msg => {

                if (!mensajesCargados.has(msg.id_mensaje)) {

                    mensajesCargados.add(msg.id_mensaje);
                    nuevosAgregados = true;

                    const divGlobo = document.createElement("div");

                    if (msg.id_remitente == idVendedor) {
                        divGlobo.className = "burbuja mensaje-recibido";
                    } else {
                        divGlobo.className = "burbuja mensaje-enviado";
                    }

                    divGlobo.textContent = msg.contenido;
                    fragmento.appendChild(divGlobo);
                }
            });

            if (nuevosAgregados) {

                if (esScrollHaciaArriba) {

                    cajaMensajes.prepend(fragmento);
                    cajaMensajes.scrollTop =
                        cajaMensajes.scrollHeight - alturaAnterior;

                } else {

                    cajaMensajes.appendChild(fragmento);
                    cajaMensajes.scrollTop = cajaMensajes.scrollHeight;

                }
            }

        } catch (error) {
            console.error("Fallo de red:", error);
        }

        cargandoHistorial = false;
    }

    // ========================================================
    // SCROLL HACIA ARRIBA
    // ========================================================
    cajaMensajes.addEventListener("scroll", function () {

        if (this.scrollTop === 0) {
            offsetMensajes += 30;
            cargarHistorial(true);
        }
    });

    // ========================================================
    // ENVIAR MENSAJE
    // ========================================================
    async function enviarMensaje() {

        const texto = inputMensaje.value.trim();
        if (texto === "") return;

        inputMensaje.value = "";

        try {


            const respuesta = await fetch("../back-end/sendMessage.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    id_destinatario: idVendedor,
                    id_publicacion: idProducto,
                    contenido: texto
                })
            });

            const resultado = await respuesta.json();

            if (resultado.exito) {
                cargarHistorial();
            } else {
                alert("⚠️ " + resultado.error);
            }

        } catch (error) {
            console.error("Error al enviar el mensaje:", error);
        }
    }

    // ========================================================
    // EVENTOS
    // ========================================================
    btnEnviar.addEventListener("click", enviarMensaje);

    inputMensaje.addEventListener("keydown", (evento) => {
        if (evento.key === "Enter") {
            evento.preventDefault();
            enviarMensaje();
        }
    });

    // ========================================================
    // ARRANQUE
    // ========================================================
    cargarHistorial();
    cargarCabecera();

    setInterval(cargarHistorial, 3000);
});