<?php
// ============================================================
// ARCHIVO: back-end/searchProducts.php
//
// Busquda de los productos para el searchProducts.js
// que a su vez lo usa main.html
// ============================================================

// 1. Declarar desde el principio que devolveremos JSON
// (Es mejor poner los headers al inicio)
header('Content-Type: application/json');

// 1. CONEXIÓN A LA BASE DE DATOS
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "marketplace";

$conn = new mysqli($servername, $username, $password, $dbname);

// Si la conexión falla, avisamos y detenemos todo
if ($conn->connect_error) {
    echo json_encode(["error" => "Error de conexión a la base de datos."]);
    exit;
}

// ============================================================
// 2. CONFIGURACIÓN DEL SCROLL INFINITO (Paginación)
// ============================================================
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$limite = 12; // Cantidad de tarjetas que cargan por cada bajón de scroll
$offset = ($pagina - 1) * $limite;


// ============================================================
// 3. LA CONSULTA BASE
// ============================================================
// Nota: Revisa que el nombre de las columnas coincida con las de tu tabla
$sql = "SELECT id_publicacion, titulo, descripcion, precio, categoria, imagen, id_usuario
        FROM publicacion
        WHERE 1=1";


// ============================================================
// 4. LOS FILTROS DINÁMICOS (Se van pegando a la consulta si existen)
// ============================================================

// A) Filtro por la barra de búsqueda
if (isset($_GET['buscar']) && !empty(trim($_GET['buscar']))) {
    // real_escape_string nos protege de hackeos básicos
    $busqueda = $conn->real_escape_string(trim($_GET['buscar']));
    $sql .= " AND titulo LIKE '%$busqueda%'";
}

// B) Filtro por los botones de categorías
if (isset($_GET['categoria']) && !empty(trim($_GET['categoria'])) && $_GET['categoria'] !== 'Todos') {
    $categoria = $conn->real_escape_string(trim($_GET['categoria']));
    $sql .= " AND categoria = '$categoria'";
}


// ============================================================
// 5. EL TOQUE FINAL (Orden y Límites)
// ============================================================
// Ordenamos para que salgan los productos más nuevos primero,
// y le pegamos el freno del paginador hasta el final.
$sql .= " ORDER BY id_publicacion DESC LIMIT $limite OFFSET $offset";


// ============================================================
// 6. EJECUCIÓN Y ENVÍO A JAVASCRIPT
// ============================================================
$resultado = $conn->query($sql);
$productos = [];

if ($resultado && $resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $productos[] = $fila;
    }
}

// Imprimimos la lista de productos en formato JSON para que tu main la lea
echo json_encode($productos);

// Cerramos la conexión para no gastar memoria del servidor
$conn->close();
?>