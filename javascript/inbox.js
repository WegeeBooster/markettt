// ============================================================
// ARCHIVO: javascript/bandeja.js
// ============================================================

document.addEventListener("DOMContentLoaded", () => {

    const contenedorLista = document.getElementById("contenedor-lista-chats");

    async function cargarBandeja() {
        try {
            const respuesta = await fetch("../back-end/getInbox.php");
            const conversaciones = await respuesta.json();

            // Si hay error de sesión, lo regresamos al login
            if (conversaciones.error) {
                alert("Tu sesión ha expirado o no has iniciado sesión.");
                window.location.href = "login.html";
                return;
            }

            // Si no tiene ningún mensaje guardado
            if (conversaciones.length === 0) {
                contenedorLista.innerHTML = `
                    <div class="estado-vacio">
                        <h3>Aún no tienes mensajes</h3>
                        <p>Explora el Marketplace y contacta a un vendedor para empezar a chatear.</p>
                    </div>`;
                return;
            }

            contenedorLista.innerHTML = "";

            conversaciones.forEach(conv => {

                const fecha = new Date(conv.fecha_envio).toLocaleDateString("es-MX", {
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });

                // ✅ CORREGIDO id_publicacion
                const urlChat = `chat.html?vendedor=${encodeURIComponent(conv.id_contacto)}&producto=${encodeURIComponent(conv.id_publicacion)}`;

                const htmlTarjeta = `
                    <a href="${urlChat}" class="tarjeta-chat">
                        <div class="avatar-chat">👤</div>
                        <div class="info-chat">
                            <h3 class="nombre-contacto">${conv.nombre_contacto}</h3>
                            <p class="titulo-producto-chat">📦 ${conv.titulo_producto}</p>
                            <p class="ultimo-mensaje">${conv.ultimo_mensaje}</p>
                        </div>
                        <div style="font-size: 12px; color: #aaa; white-space: nowrap;">
                            ${fecha}
                        </div>
                    </a>
                `;

                contenedorLista.insertAdjacentHTML('beforeend', htmlTarjeta);
            });

        } catch (error) {
            console.error("Error al cargar la bandeja:", error);
            contenedorLista.innerHTML =
                '<div class="estado-vacio" style="color:red;">⚠️ Hubo un error al cargar tus mensajes.</div>';
        }
    }

    cargarBandeja();
});