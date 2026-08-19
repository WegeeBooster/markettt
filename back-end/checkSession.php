<?php
// ============================================================
// ARCHIVO: back-end/checkSession.php
// Este archivo sirve para verificar si un usuario a iniciado sesion
// ============================================================
session_start();
header('Content-Type: application/json');

// Revisamos si existe la "pulsera VIP"
if (isset($_SESSION['id_usuario'])) {
    echo json_encode([
        "logeado" => true,
        "nombre" => $_SESSION['nombre'],
        "id" => $_SESSION['id_usuario']
    ]);
} else {
    echo json_encode(["logeado" => false]);
}
?>