<?php
// ============================================================
// ARCHIVO: back-end/loadMessage.php
// Sirve para la busqueda de todos los mensajes(en un
// chat en especifico y que carga de 30 en 30 mensajes)
// que el usuario a tenido con otro, se usa en chat.js
// ============================================================
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(["error" => "No autorizado."]);
    exit;
}

$mi_id = $_SESSION['id_usuario'];
$id_vendedor = $_GET['vendedor'] ?? null;
$id_producto = $_GET['producto'] ?? null;
// Recibimos el offset (0 por defecto) sirve para el scroll
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

if (!$id_vendedor || !$id_producto) {
    echo json_encode(["error" => "Faltan parámetros."]);
    exit;
}

$conn = new mysqli("localhost", "root", "", "marketplace");

if ($conn->connect_error) {
    echo json_encode(["error" => "Error BD"]);
    exit;
}

// Buscamos los mensajes donde TÚ seas el remitente y ÉL el destinatario, o viceversa.
// Lo ordenamos DESC (del más nuevo al más viejo) y le ponemos LIMIT 30
$sql = "SELECT * FROM mensaje
        WHERE id_publicacion = ?
        AND ((id_remitente = ? AND id_destinatario = ?)
        OR (id_remitente = ? AND id_destinatario = ?))
        ORDER BY fecha_envio DESC LIMIT 30 OFFSET ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iiiiii", $id_producto, $mi_id, $id_vendedor, $id_vendedor, $mi_id, $offset);
$stmt->execute();
$resultado = $stmt->get_result();

$mensajes = [];
while ($fila = $resultado->fetch_assoc()) {
    $mensajes[] = $fila;
}


// Como sacamos los más recientes primero (DESC), el mensaje de "hoy" quedó arriba
// y el de "ayer" quedó abajo.
// Usamos array_reverse para voltearlos y que en tu pantalla se vean cronológicos.
$mensajes = array_reverse($mensajes);

echo json_encode($mensajes);

$stmt->close();
$conn->close();
?>