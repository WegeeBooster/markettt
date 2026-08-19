<?php
// ============================================================
// ARCHIVO: back-end/sendMessage.php
// ============================================================
session_start();
header('Content-Type: application/json');

ini_set('display_errors', 1);
error_reporting(E_ALL);

// 1. Verificamos que el usuario haya iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(["exito" => false, "error" => "Debes iniciar sesión para enviar mensajes."]);
    exit;
}

// 2. Recibimos los datos desde JavaScript
$datos = json_decode(file_get_contents("php://input"), true);
$id_remitente = $_SESSION['id_usuario']; //  usamos la sesión del login!
$id_destinatario = $datos['id_destinatario'] ?? null;
// CORRECCIÓN: Leemos 'id_producto' del JSON, pero lo guardamos en la variable $id_publicacion para la BD
$id_publicacion = $datos['id_producto'] ?? null;
$contenido = $datos['contenido'] ?? '';

if (!$id_destinatario || !$id_publicacion || empty(trim($contenido))) {
    echo json_encode(["exito" => false, "error" => "Faltan datos para enviar el mensaje."]);
    exit;
}

// 3. Conexión a la BD
$conn = new mysqli("localhost", "root", "", "marketplace");
if ($conn->connect_error) {
    echo json_encode(["exito" => false, "error" => "Error de BD."]);
    exit;
}

// 4. Guardamos el mensaje en la base de datos
$sql = "INSERT INTO mensaje (id_remitente, id_destinatario, id_publicacion, contenido) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiis", $id_remitente, $id_destinatario, $id_publicacion, $contenido);

if ($stmt->execute()) {
    echo json_encode(["exito" => true, "mensaje" => "Mensaje enviado."]);
} else {
    echo json_encode(["exito" => false, "error" => "No se pudo guardar el mensaje."]);
}

$stmt->close();
$conn->close();
?>