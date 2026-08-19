// ========================================================
// ARCHIVO: javascript/post.js
// ========================================================
document.addEventListener("DOMContentLoaded", () => {

    // 1. VARIABLES DE LA INTERFAZ
    const inputImagenes = document.getElementById("imagenes"); // ¡Plural!
    const previewContainer = document.getElementById("preview-container");
    const zonaClick = document.getElementById("zona-click-fotos");
    const textoFotos = document.getElementById("texto-fotos");

    // 2. MAGIA DEL CLIC (Convertimos la caja en botón)
    zonaClick.addEventListener("click", () => {
        inputImagenes.click();
    });

    // 3. LÓGICA PARA LA VISTA PREVIA (Múltiples fotos)
    inputImagenes.addEventListener("change", function() {
        previewContainer.innerHTML = ""; // Limpiamos fotos anteriores por si cambia de opinión

        const archivos = Array.from(this.files); // Convertimos las fotos en una lista real

        // Freno: Máximo 5 fotos
        if (archivos.length > 5) {
            alert("¡Tranquilo broder! Máximo 5 fotos por producto.");
            this.value = ""; // Borramos su selección
            textoFotos.style.display = "block"; // Regresamos el texto
            return;
        }

        // Si sí hay fotos, escondemos el texto de "Haz clic aquí"
        if (archivos.length > 0) {
            textoFotos.style.display = "none";
        } else {
            textoFotos.style.display = "block";
        }

        // Dibujamos una miniatura por cada foto seleccionada
        archivos.forEach(archivo => {
            const lector = new FileReader();
            lector.onload = function(e) {
                const img = document.createElement("img");
                img.src = e.target.result;
                // Le damos estilo a las miniaturas para que se vean bonitas
                img.style.width = "100px";
                img.style.height = "100px";
                img.style.objectFit = "cover";
                img.style.borderRadius = "8px";
                img.style.boxShadow = "0 2px 5px rgba(0,0,0,0.2)";

                previewContainer.appendChild(img);
            }
            lector.readAsDataURL(archivo);
        });
    });

    // 4. LÓGICA PARA ENVIAR EL FORMULARIO
    const formPublicar = document.getElementById("formPublicar");
    const mensajeRespuesta = document.getElementById("mensaje-respuesta");

    formPublicar.addEventListener("submit", async (evento) => {
        evento.preventDefault(); // Evitamos que la página se recargue

        const btn = formPublicar.querySelector('button');
        btn.textContent = "Subiendo...";
        btn.disabled = true;

        try {
            // FormData empaca los textos y las 5 fotos automáticamente
            const datosFormulario = new FormData(formPublicar);

            const respuesta = await fetch("../back-end/post.php", {
                method: "POST",
                body: datosFormulario
            });

            const resultado = await respuesta.json();

            if (resultado.exito) {
                mensajeRespuesta.style.color = "green";
                mensajeRespuesta.textContent = "¡Producto publicado con éxito! Redirigiendo...";
                // Lo mandamos al main
                setTimeout(() => { window.location.href = "main.html"; }, 1500);
            } else {
                mensajeRespuesta.style.color = "red";
                mensajeRespuesta.textContent = resultado.error || "Hubo un error al publicar.";
                btn.textContent = "Subir";
                btn.disabled = false;
            }

        } catch (error) {
            console.error("Error:", error);
            mensajeRespuesta.style.color = "red";
            mensajeRespuesta.textContent = "Error de red al intentar subir el producto.";
            btn.textContent = "Subir";
            btn.disabled = false;
        }
    });
});