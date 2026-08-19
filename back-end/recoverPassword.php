<?php
// ============================================================
// ARCHIVO: back-end/recoverPassword.php
// Genera un token de recuperación y simula envío de correo
// ============================================================
session_start();
header('Content-Type: application/json');

$datos  = json_decode(file_get_contents("php://input"), true);
$correo = trim($datos['correo'] ?? '');
$accion = $datos['accion'] ?? 'solicitar'; // 'solicitar' | 'verificar' | 'cambiar'

if (empty($correo)) {
    echo json_encode(["exito" => false, "error" => "Ingresa tu correo institucional."]);
    exit;
}

// Conexión a la BD
$servername  = "localhost";
$db_user     = "root";
$db_password = "";
$dbname      = "marketplace";

$conn = new mysqli($servername, $db_user, $db_password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["exito" => false, "error" => "Error de conexión a la BD."]);
    exit;
}

// ============================================================
// ACCIÓN 1: SOLICITAR CÓDIGO
// ============================================================
if ($accion === 'solicitar') {

    // Verificar que el correo exista
    $stmt = $conn->prepare("SELECT id_usuario, nombre FROM usuario WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {
        echo json_encode(["exito" => false, "error" => "Este correo no está registrado."]);
        $stmt->close(); $conn->close(); exit;
    }

    $usuario = $res->fetch_assoc();
    $stmt->close();

    // Generar código de 6 dígitos
    $codigo  = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $expira  = date('Y-m-d H:i:s', strtotime('+15 minutes'));

    // Guardar token en sesión (en producción se guardaría en BD)
    $_SESSION['recovery_correo'] = $correo;
    $_SESSION['recovery_token']  = $codigo;
    $_SESSION['recovery_expira'] = $expira;

    // NOTA: En producción se enviaría el correo real con PHPMailer o similar.
    // Por ahora devolvemos el código en la respuesta para pruebas.
    echo json_encode([
        "exito"   => true,
        "mensaje" => "Se envió un código a " . $correo,
        "codigo_demo" => $codigo   // QUITAR en producción
    ]);

}

// ============================================================
// ACCIÓN 2: VERIFICAR CÓDIGO
// ============================================================
elseif ($accion === 'verificar') {

    $codigo_ingresado = trim($datos['codigo'] ?? '');

    if (empty($codigo_ingresado)) {
        echo json_encode(["exito" => false, "error" => "Ingresa el código que recibiste."]);
        $conn->close(); exit;
    }

    if (
        !isset($_SESSION['recovery_correo'], $_SESSION['recovery_token'], $_SESSION['recovery_expira']) ||
        $_SESSION['recovery_correo'] !== $correo
    ) {
        echo json_encode(["exito" => false, "error" => "Sesión de recuperación inválida. Vuelve a empezar."]);
        $conn->close(); exit;
    }

    if (strtotime($_SESSION['recovery_expira']) < time()) {
        echo json_encode(["exito" => false, "error" => "El código expiró. Solicita uno nuevo."]);
        unset($_SESSION['recovery_correo'], $_SESSION['recovery_token'], $_SESSION['recovery_expira']);
        $conn->close(); exit;
    }

    if ($_SESSION['recovery_token'] !== $codigo_ingresado) {
        echo json_encode(["exito" => false, "error" => "Código incorrecto."]);
        $conn->close(); exit;
    }

    // Código correcto: marcamos que puede cambiar contraseña
    $_SESSION['recovery_verificado'] = true;
    echo json_encode(["exito" => true, "mensaje" => "Código verificado correctamente."]);

}

// ============================================================
// ACCIÓN 3: CAMBIAR CONTRASEÑA
// ============================================================
elseif ($accion === 'cambiar') {

    $nueva = $datos['nueva_password'] ?? '';

    if (strlen($nueva) < 4) {
        echo json_encode(["exito" => false, "error" => "La contraseña debe tener al menos 4 caracteres."]);
        $conn->close(); exit;
    }

    if (
        !isset($_SESSION['recovery_verificado'], $_SESSION['recovery_correo']) ||
        $_SESSION['recovery_correo'] !== $correo ||
        $_SESSION['recovery_verificado'] !== true
    ) {
        echo json_encode(["exito" => false, "error" => "No tienes autorización para cambiar esta contraseña."]);
        $conn->close(); exit;
    }

    $stmt = $conn->prepare("UPDATE usuario SET password = ? WHERE correo = ?");
    $stmt->bind_param("ss", $nueva, $correo);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        // Limpiar sesión de recuperación
        unset($_SESSION['recovery_correo'], $_SESSION['recovery_token'], $_SESSION['recovery_expira'], $_SESSION['recovery_verificado']);
        echo json_encode(["exito" => true, "mensaje" => "Contraseña actualizada correctamente."]);
    } else {
        echo json_encode(["exito" => false, "error" => "No se pudo actualizar la contraseña."]);
    }
    $stmt->close();

} else {
    echo json_encode(["exito" => false, "error" => "Acción no reconocida."]);
}

$conn->close();
?>
