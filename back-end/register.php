<?php
// ============================================================
// ARCHIVO: back-end/register.php
// Este archivo registra un nuevo usuario
// ============================================================
session_start();
header('Content-Type: application/json');

$datos = json_decode(file_get_contents("php://input"), true);
$nombre       = trim($datos['nombre'] ?? '');
$correo       = trim($datos['correo'] ?? '');
$password     = $datos['password'] ?? '';
$departamento = trim($datos['departamento'] ?? '');

if (empty($nombre) || empty($correo) || empty($password) || empty($departamento)) {
    echo json_encode(["exito" => false, "error" => "Por favor, llena todos los campos."]);
    exit;
}

// Validar formato correo institucional (@unison.mx)
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["exito" => false, "error" => "El correo no tiene un formato válido."]);
    exit;
}

// 2. Extraer el dominio (lo que está después del @)
$partes = explode("@", $correo);
$dominioUsuario = end($partes); // Agarra la última parte

$dominioRequerido = "unison.mx";
// 3. Comparar si es el de la escuela
if ($dominioUsuario !== $dominioRequerido) {
    echo json_encode(["error" => "Acceso denegado. Solo se permiten correos de la Unison."]);
    exit;
}

// Conexión a la BD
$servername = "localhost";
$username   = "root";
$password_db = "";
$dbname     = "marketplace";

$conn = new mysqli($servername, $username, $password_db, $dbname);
if ($conn->connect_error) {
    echo json_encode(["exito" => false, "error" => "Error de conexión a la BD."]);
    exit;
}

// Verificar si el correo ya está registrado
$check = $conn->prepare("SELECT id_usuario FROM usuario WHERE correo = ?");
$check->bind_param("s", $correo);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo json_encode(["exito" => false, "error" => "Este correo ya está registrado."]);
    $check->close();
    $conn->close();
    exit;
}
$check->close();

// Insertar nuevo usuario
$sql  = "INSERT INTO usuario (correo, password, nombre, departamento, tipo_usuario) VALUES (?, ?, ?, ?, 'comprador')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $correo, $password, $nombre, $departamento);

if ($stmt->execute()) {
    $id_nuevo = $conn->insert_id;
    session_regenerate_id(true);
    $_SESSION['id_usuario'] = $id_nuevo;
    $_SESSION['nombre']     = $nombre;
    echo json_encode(["exito" => true, "mensaje" => "¡Bienvenido, " . $nombre . "!"]);
} else {
    echo json_encode(["exito" => false, "error" => "No se pudo crear la cuenta. Inténtalo de nuevo."]);
}

$stmt->close();
$conn->close();
?>
