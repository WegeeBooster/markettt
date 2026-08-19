<?php
// ============================================================
// ARCHIVO: back-end/geInbox.php
//
//  Busqueda de los ultimos mensajes que ha tenido el usuario
//  con otras personas
// ============================================================
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(["error" => "No autorizado."]);
    exit;
}

$mi_id = $_SESSION['id_usuario'];
$conn = new mysqli("localhost", "root", "", "marketplace");

if ($conn->connect_error) {
    echo json_encode(["error" => "Error en la conexión BD."]);
    exit;
}

// LA CONSULTA MÁGICA:
// 1. Busca el último mensaje de cada conversación en la que participas.
// 2. Hace JOIN con 'publicacion' para saber de qué producto hablan.
// 3. Hace JOIN con 'usuario' para traer el nombre de LA OTRA PERSONA.
$sql = "
    SELECT
        m.id_publicacion,
        m.contenido AS ultimo_mensaje,
        m.fecha_envio,
        p.titulo AS titulo_producto,
        u_otro.id_usuario AS id_contacto,
        u_otro.nombre AS nombre_contacto
    FROM mensaje m
    JOIN publicacion p ON m.id_publicacion = p.id_publicacion
    JOIN usuario u_otro ON u_otro.id_usuario = IF(m.id_remitente = ?, m.id_destinatario, m.id_remitente)
    WHERE m.id_mensaje IN (
        SELECT MAX(id_mensaje)
        FROM mensaje
        WHERE id_remitente = ? OR id_destinatario = ?
        GROUP BY id_publicacion, LEAST(id_remitente, id_destinatario), GREATEST(id_remitente, id_destinatario)
    )
    ORDER BY m.fecha_envio DESC
";

$stmt = $conn->prepare($sql);
// Pasamos nuestro ID 3 veces porque la consulta lo necesita en diferentes lugares para comparar
$stmt->bind_param("iii", $mi_id, $mi_id, $mi_id);
$stmt->execute();
$resultado = $stmt->get_result();

$conversaciones = [];
while ($fila = $resultado->fetch_assoc()) {
    $conversaciones[] = $fila;
}

echo json_encode($conversaciones);

$stmt->close();
$conn->close();
?>