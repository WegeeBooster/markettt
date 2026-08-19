<?php
// ============================================================
// ARCHIVO: back-end/logout.php
// Este archivo sirve para cerra sesion correctamente, anteriormente
// le pusimos una marca al usuario para identificar que habia iniciado
// sesion, este archivo quita esa marca
// ============================================================
// 1. Reanudamos la sesión actual para saber a quién vamos a "correr"
session_start();
// 2. Vaciamos todos los datos (nombre, id, etc.)
session_unset();
// 3. Destruimos la sesión por completo en el servidor (le quitamos la pulsera)
session_destroy();
// 4. Lo mandamos al login de forma segura
header("Location: ../html/login.html");
exit;
?>