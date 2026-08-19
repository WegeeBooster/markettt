// ============================================================
// ARCHIVO: javascript/login.js
// ============================================================

document.addEventListener("DOMContentLoaded", () => {

    const formLogin = document.getElementById("form-login");
    const divError = document.getElementById("mensaje-error");

    formLogin.addEventListener("submit", async (evento) => {

        // 1. Evitamos recargar la página
        evento.preventDefault();

        // 2. Obtener valores
        const correo = document.getElementById("correo").value.trim();
        const password = document.getElementById("contra").value;

        // 3. Validar campos vacíos
        if (!correo || !password) {
            divError.style.color = "red";
            divError.textContent = " Completa todos los campos.";
            return;
        }

        // 4. Desactivar botón para evitar spam
        const btn = formLogin.querySelector("button");
        btn.disabled = true;

        // Mensaje de carga
        divError.textContent = "Verificando credenciales...";
        divError.style.color = "blue";

        try {
            // 5. Enviar datos al backend
            const respuesta = await fetch("../back-end/login.php", {
                method: "POST",
                credentials: "include",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    correo: correo,
                    password: password
                })
            });

            const datos = await respuesta.json();

            // 6. Revisar respuesta
            if (datos.exito === true) {

                divError.style.color = "green";
                divError.textContent = datos.mensaje + " Redirigiendo...";

                setTimeout(() => {
                    window.location.href = "main.html";
                }, 1000);

            } else {

                divError.style.color = "red";
                divError.textContent = "Error " + datos.error;

            }

        } catch (error) {

            console.error("Error en la petición:", error);
            divError.style.color = "red";
            divError.textContent = "Error al conectar con el servidor.";

        } finally {

            // 7. Reactivar botón
            btn.disabled = false;

        }
    });
});