//settings.js
//Al cargar la página, hace un fetch para traer los datos que el usuario ya tiene
// guardados y rellenar los inputs (así no tiene que escribir todo desde cero).
//
document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("form-config");
    const imgPreview = document.getElementById("img-preview");
    const fotoInput = document.getElementById("foto_perfil");
    const alertMsg = document.getElementById("alert-msg");

    // === ACCIÓN 1: TRAER DATOS PREVIOS DESDE LA BD AL CARGAR ===
    async function cargarDatosExistentes() {
        try {
            // Hacemos la petición a tu archivo PHP tal cual lo tienes
            const response = await fetch("../back-end/getPerfil.php");
            const data = await response.json();

            if (data.exito === false) {
                mostrarAlerta(data.error || "Error al obtener perfil", "error");
                return;
            }

            // CORRECCIÓN: Tu PHP manda los datos directo en la raíz de 'data'
            document.getElementById("nombre").value = data.nombre || "";
            document.getElementById("departamento").value = data.departamento || "";
            document.getElementById("horario").value = data.horario || "";
            document.getElementById("correo").value = data.correo || "...";


            // CORRECCIÓN: Tu PHP codifica la foto en Base64 y la nombra como 'foto'
            if (data.foto) {
                imgPreview.src = data.foto;
            } else {
                imgPreview.src = "../uploads/default-avatar.jpg"; // Por si no tiene
            }

            // --- REDES SOCIALES ---
            // Como tu getPerfil.php actual no trae las redes, las dejamos listas para cuando
            // se guarden mediante updateConfig.php o puedes añadirlas en un fetch secundario si lo requieres.
            if (data.whatsapp) document.getElementById("whatsapp").value = data.whatsapp;
            if (data.instagram) document.getElementById("instagram").value = data.instagram;

        } catch (error) {
            console.error("Error cargando los datos del usuario:", error);
            mostrarAlerta("No se pudieron recuperar tus datos actuales.", "error");
        }
    }

    // === ACCIÓN 2: PREVISUALIZACIÓN DE FOTO LOCAL ===
    fotoInput.addEventListener("change", function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imgPreview.src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });

    // === ACCIÓN 3: ENVIAR ACTUALIZACIONES EN SEGUNDO PLANO ===
    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        const formData = new FormData(form);

        try {
            const respuesta = await fetch("../back-end/updateSettings.php", {
                method: "POST",
                body: formData
            });
            const resultado = await respuesta.json();

            if (resultado.exito) {
                mostrarAlerta(resultado.mensaje, "success");
                window.scrollTo({ top: 0, behavior: 'smooth' });
                setTimeout(() => {
                    window.location.href = "perfil.html";
                }, 1800);
            } else {
                mostrarAlerta(resultado.error, "error");
            }
        } catch (error) {
            mostrarAlerta("Error crítico: no se pudo establecer comunicación con el servidor.", "error");
        }
    });

    function mostrarAlerta(msg, tipo) {
        alertMsg.textContent = msg;
        alertMsg.style.display = "block";
        alertMsg.className = `alert alert-${tipo}`;
    }

    // Arrancamos la carga automática al entrar a la ventana
    cargarDatosExistentes();
});