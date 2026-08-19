<?php
// ============================================================
// ARCHIVO: back-end/getPerfil.php (ACTUALIZADO Y COMPATIBLE)
// Devuelve los datos del perfil del usuario en sesión e incluye redes sociales
// ============================================================
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(["exito" => false, "error" => "No hay sesión activa."]);
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

$servername  = "localhost";
$db_user     = "root";
$db_password = "";
$dbname      = "marketplace";

$conn = new mysqli($servername, $db_user, $db_password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["exito" => false, "error" => "Error de conexión."]);
    exit;
}

// 1. Datos base del usuario
$stmt = $conn->prepare("SELECT nombre, correo, departamento, horario, tipo_usuario, foto_de_perfil FROM usuario WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(["exito" => false, "error" => "Usuario no encontrado."]);
    $stmt->close(); $conn->close(); exit;
}

$u = $res->fetch_assoc();
$stmt->close();

// 2. DETECTAR LA FOTO ES RUTA (TEXTO)
$foto = null;
if (!empty($u['foto_de_perfil'])) {
    // Si la cadena empieza con "../" o "http", es una ruta de archivo de texto directa
    if (strpos($u['foto_de_perfil'], '../') === 0 || strpos($u['foto_de_perfil'], 'http') === 0) {
        $foto = $u['foto_de_perfil'];
    }
}

// 3. NUEVO: Obtener las redes sociales del usuario (WhatsApp e Instagram)
$whatsapp = "";
$instagram = "";
$stmtRed = $conn->prepare("SELECT tipo, enlace FROM red WHERE id_usuario = ?");
$stmtRed->bind_param("i", $id_usuario);
$stmtRed->execute();
$resRed = $stmtRed->get_result();

while ($r = $resRed->fetch_assoc()) {
    if (strtolower($r['tipo']) === 'whatsapp') {
        $whatsapp = $r['enlace'];
    } elseif (strtolower($r['tipo']) === 'instagram') {
        $instagram = $r['enlace'];
    }
}
$stmtRed->close();

// 4. Publicaciones del usuario (Mantenemos intacto)
$stmtPub = $conn->prepare("SELECT id_publicacion, titulo, precio, categoria, imagen FROM publicacion WHERE id_usuario = ? ORDER BY id_publicacion DESC");
$stmtPub->bind_param("i", $id_usuario);
$stmtPub->execute();
$resPub = $stmtPub->get_result();

$publicaciones = [];
while ($p = $resPub->fetch_assoc()) {
    $publicaciones[] = $p;
}
$stmtPub->close();

// 5. Reseñas recibidas (Mantenemos intacto)
$stmtRes = $conn->prepare("
    SELECT r.calificacion, r.comentario, u.nombre AS revisor, r.id_resena
    FROM resena r
    JOIN publicacion p ON r.id_publicacion = p.id_publicacion
    JOIN usuario u ON r.id_usuario = u.id_usuario
    WHERE p.id_usuario = ?
    ORDER BY r.id_resena DESC
    LIMIT 5
");
$stmtRes->bind_param("i", $id_usuario);
$stmtRes->execute();
$resReseñas = $stmtRes->get_result();

$resenas = [];
while ($r = $resReseñas->fetch_assoc()) {
    $resenas[] = $r;
}
$stmtRes->close();

// 6. Promedio de calificación (Mantenemos intacto)
$stmtProm = $conn->prepare("
    SELECT AVG(r.calificacion) AS promedio, COUNT(*) AS total
    FROM resena r
    JOIN publicacion p ON r.id_publicacion = p.id_publicacion
    WHERE p.id_usuario = ?
");
$stmtProm->bind_param("i", $id_usuario);
$stmtProm->execute();
$resProm = $stmtProm->get_result()->fetch_assoc();
$stmtProm->close();

$conn->close();

// 7. RESPUESTA JSON FINAL (Conservamos estructura exacta y añadimos las nuevas llaves)
echo json_encode([
    "exito"        => true,
    "nombre"       => $u['nombre'],
    "correo"       => $u['correo'],
    "departamento" => $u['departamento'],
    "horario"      => $u['horario'],
    "tipo_usuario" => $u['tipo_usuario'],
    "foto"         => $foto,
    "whatsapp"     => $whatsapp,   // <-- Agregado para el formulario
    "instagram"    => $instagram,  // <-- Agregado para el formulario
    "publicaciones"=> $publicaciones,
    "resenas"      => $resenas,
    "promedio"     => round($resProm['promedio'] ?? 0, 1),
    "total_resenas"=> (int)($resProm['total'] ?? 0)
]);
exit;
?>