<?php
// ============================================================
// ARCHIVO: back-end/post.php
// ============================================================
session_start();
header('Content-Type: application/json');

// 1. VERIFICAMOS EL CADENERO
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(["error" => "Debes iniciar sesión para publicar."]);
    exit;
}

// 2. CONEXIÓN A LA BASE DE DATOS
$conn = new mysqli("localhost", "root", "", "marketplace");

if ($conn->connect_error) {
    echo json_encode(["error" => "Error de conexión a la base de datos."]);
    exit;
}

// 3. RECIBIMOS LOS DATOS DE TEXTO
$id_vendedor = $_SESSION['id_usuario'];
$titulo = $conn->real_escape_string($_POST['titulo']);
$precio = (float)$_POST['precio'];
$categoria = $conn->real_escape_string($_POST['categoria']);
$estado = $conn->real_escape_string($_POST['estado']); // <--- Campo nuevo
$descripcion = isset($_POST['descripcion']) ? $conn->real_escape_string($_POST['descripcion']) : "";


// 4. LÓGICA DE MÚLTIPLES IMÁGENES
// Verificamos si mandaron el arreglo de 'imagenes'
if (isset($_FILES['imagenes']) && !empty($_FILES['imagenes']['name'][0])) {

    $carpeta_destino = "../uploads/";
    $rutas_guardadas = []; // Aquí iremos metiendo las fotos que sí se guarden

    // Recorremos cada archivo que nos mandaron desde el HTML
    foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmp_name) {
        if ($_FILES['imagenes']['error'][$key] === UPLOAD_ERR_OK) {

            $nombre_original = basename($_FILES['imagenes']['name'][$key]);
            // El truco PRO para que no choquen los nombres
            $nombre_final = uniqid() . "_" . $nombre_original;
            $ruta_completa = $carpeta_destino . $nombre_final;

            // Si se movió con éxito, la anotamos en nuestra lista
            if (move_uploaded_file($tmp_name, $ruta_completa)) {
                $rutas_guardadas[] = $ruta_completa;
            }
        }
    }

    // Si logramos guardar al menos una foto, procedemos a la Base de Datos
    if (count($rutas_guardadas) > 0) {

        // 5. GUARDAMOS EN LA BASE DE DATOS
        // Convertimos la lista de rutas en un solo texto JSON
        $imagenes_json = json_encode($rutas_guardadas);

        // Ojo: agregué la columna 'estado' a tu consulta SQL
        $sql = "INSERT INTO publicacion (titulo, descripcion, precio, categoria, estado, imagen, id_usuario)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);

        // s=string, d=double(decimal), i=integer
        $stmt->bind_param("ssdsssi", $titulo, $descripcion, $precio, $categoria, $estado, $imagenes_json, $id_vendedor);

        if ($stmt->execute()) {
            echo json_encode(["exito" => true, "mensaje" => "Producto publicado."]);
        } else {
            echo json_encode(["error" => "Error al guardar en la base de datos: " . $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(["error" => "Hubo un problema al guardar las imágenes en el servidor."]);
    }

} else {
    echo json_encode(["error" => "Debes subir al menos una imagen."]);
}

$conn->close();
?>