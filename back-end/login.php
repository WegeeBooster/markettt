<?php
// ============================================================
// ARCHIVO: back-end/login.php
// Este archivo valida el inicio de sesion
// ============================================================
// 1. ARRANCAMOS LA SESIÓN (CRÍTICO)
session_start();
header('Content-Type: application/json');

// 2. Recibir los datos que manda JavaScript en formato JSON
$datos = json_decode(file_get_contents("php://input"), true);
$correo = $datos['correo'] ?? '';
$password_ingresada = $datos['password'] ?? '';

if (empty($correo) || empty($password_ingresada)) {
    echo json_encode(["exito" => false, "error" => "Por favor, llena todos los campos."]);
    exit;
}

// 3. Conexión a la Base de Datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "marketplace";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["exito" => false, "error" => "Error de conexión a la BD."]);
    exit;
}

// 4. Buscar al usuario por su correo
$sql = "SELECT id_usuario, nombre, password FROM usuario WHERE correo = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $correo);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    $usuario = $resultado->fetch_assoc();

    // 5. Verificar contraseña
    // Comparamos texto plano para que funcione provisionalmente.
    // En el futuro, lo ideal es usar password_verify() para mayor seguridad.
    if ($password_ingresada === $usuario['password']) {

         session_regenerate_id(true);
        // ¡ÉXITO! LE PONEMOS LA PULSERA VIP AL USUARIO
        $_SESSION['id_usuario'] = $usuario['id_usuario'];
        $_SESSION['nombre'] = $usuario['nombre'];

        echo json_encode(["exito" => true, "mensaje" => "Bienvenido " . $usuario['nombre']]);
    } else {
        echo json_encode(["exito" => false, "error" => "Contraseña incorrecta."]);
    }
} else {
    echo json_encode(["exito" => false, "error" => "Este correo no está registrado."]);
}

$stmt->close();
$conn->close();
?>