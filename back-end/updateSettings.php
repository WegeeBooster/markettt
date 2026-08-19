<?php
//  updateSetings.php
// Archivo complementario para setings.js
//Este script utiliza una transacción de MySQL (BEGIN WORK / COMMIT)
//para asegurar que se actualicen las dos tablas al mismo tiempo (usuario y red)
//de manera limpia.

session_start();
header('Content-Type: application/json');

// 1. CONTROL DE ACCESO
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(["error" => "Debes iniciar sesión para modificar tu perfil."]);
    exit;
}

$conn = new mysqli("localhost", "root", "", "marketplace");
if ($conn->connect_error) {
    echo json_encode(["error" => "Error de conexión con la base de datos."]);
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

// 2. FILTRAR Y SANITIZAR TEXTOS
$nombre = $conn->real_escape_string($_POST['nombre']);
$departamento = $conn->real_escape_string($_POST['departamento']);
$horario = $conn->real_escape_string($_POST['horario']);
$whatsapp = $conn->real_escape_string($_POST['whatsapp']);
$instagram = $conn->real_escape_string($_POST['instagram']);

// INICIAMOS TRANSACCIÓN PARA ASEGURAR QUE AMBAS TABLAS SE ACTUALICEN CORRECTAMENTE
$conn->begin_transaction();

try {
    // 3. PROCESAR FOTO DE PERFIL (SI SUBIERON UNA NUEVA)
    $ruta_foto_sql = "";
    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['foto_perfil']['tmp_name'];
        $file_name = $_FILES['foto_perfil']['name'];
        $extension = pathinfo($file_name, PATHINFO_EXTENSION);

        // Generamos un nombre único para que no se sobreescriban fotos
        $nuevo_nombre_foto = "avatar_" . $id_usuario . "_" . uniqid() . "." . $extension;
        $directorio_subida = "../uploads/" . $nuevo_nombre_foto;

        if (move_uploaded_file($file_tmp, $directorio_subida)) {
            $ruta_db_foto = "../uploads/" . $nuevo_nombre_foto;
            // Modificamos el SQL para incluir la foto de perfil en la actualización
            $ruta_foto_sql = ", foto_de_perfil = '$ruta_db_foto'";
        }
    }

    // 4. ACTUALIZAR TABLA USUARIO
    $sql_usuario = "UPDATE usuario SET nombre = ?, departamento = ?, horario = ? $ruta_foto_sql WHERE id_usuario = ?";
    $stmt_user = $conn->prepare($sql_usuario);
    $stmt_user->bind_param("sssi", $nombre, $departamento, $horario, $id_usuario);
    $stmt_user->execute();
    $stmt_user->close();

    // 5. ACTUALIZAR REDES SOCIALES (WHATSAPP E INSTAGRAM)
    // Para simplificar y no hacer inserts manuales complicados, borramos las redes anteriores de este usuario y metemos las nuevas actualizadas
    $sql_borrar_redes = "DELETE FROM red WHERE id_usuario = ?";
    $stmt_del = $conn->prepare($sql_borrar_redes);
    $stmt_del->bind_param("i", $id_usuario);
    $stmt_del->execute();
    $stmt_del->close();

    // Insertar WhatsApp si no está vacío
    if (!empty($whatsapp)) {
        $tipo_wa = "whatsapp";
        $sql_ins_wa = "INSERT INTO red (id_usuario, tipo, enlace) VALUES (?, ?, ?)";
        $stmt_wa = $conn->prepare($sql_ins_wa);
        $stmt_wa->bind_param("iss", $id_usuario, $tipo_wa, $whatsapp);
        $stmt_wa->execute();
        $stmt_wa->close();
    }

    // Insertar Instagram si no está vacío
    if (!empty($instagram)) {
        $tipo_ig = "instagram";
        $sql_ins_ig = "INSERT INTO red (id_usuario, tipo, enlace) VALUES (?, ?, ?)";
        $stmt_ig = $conn->prepare($sql_ins_ig);
        $stmt_ig->bind_param("iss", $id_usuario, $tipo_ig, $instagram);
        $stmt_ig->execute();
        $stmt_ig->close();
    }

    // SI TODO SALIÓ BIEN, GUARDAMOS LOS CAMBIOS EN DISCO
    $conn->commit();
    echo json_encode(["exito" => true, "mensaje" => "¡Configuración guardada correctamente!"]);

} catch (Exception $e) {
    // SI ALGO FALLÓ, HACEMOS UN CTRL+Z GLOBAL PARA PROTEGER LA INTEGRIDAD
    $conn->rollback();
    echo json_encode(["error" => "No se pudieron actualizar los datos de configuración."]);
}

$conn->close();
?>