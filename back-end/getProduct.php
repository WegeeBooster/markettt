<?php
// ============================================================
// ARCHIVO: back-end/getProduct.php
// Este archivo sirve para hacer la busqueda por un producto
// en especifico y devolverlo en JSON, lo usa product.js que
// a su vez lo usa products.html
// ============================================================
header('Content-Type: application/json');

// 1. Obtener el ID del producto desde la URL (GET)
$id_publicacion = isset($_GET['id']) ? $_GET['id'] : null;

if ($id_publicacion === null) {
    echo json_encode(["error" => "No se proporcionó un ID de producto."]);
    exit;
}

// 2. Datos de conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "marketplace";

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    echo json_encode(["error" => "Error de conexión a la base de datos: " . $conn->connect_error]);
    exit;
}

// 3. Preparar la consulta SQL con JOIN
// AGREGAMOS u.horario y u.foto_de_perfil para tener los datos completos del vendedor
$sql = "SELECT p.*,
               p.id_usuario AS id_vendedor,
               u.nombre AS nombre_vendedor,
               u.departamento AS contacto_vendedor,
               u.horario,
               u.foto_de_perfil
        FROM publicacion p
        JOIN usuario u ON p.id_usuario = u.id_usuario
        WHERE p.id_publicacion = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_publicacion);
$stmt->execute();
$result = $stmt->get_result();

// 4. Procesar el resultado y devolver JSON
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // -- INICIO DEL NUEVO BLOQUE DEL VENDEDOR --

    // A) Procesamos la foto de perfil (híbrido: ruta texto o binario BLOB)
    $foto_vendedor = null;
    if (!empty($row['foto_de_perfil'])) {
        if (strpos($row['foto_de_perfil'], '../') === 0 || strpos($row['foto_de_perfil'], 'http') === 0) {
            $foto_vendedor = $row['foto_de_perfil'];
        }
    }

    // B) Consultamos las redes sociales de este vendedor en específico
    $whatsapp = "";
    $instagram = "";
    $id_vend = $row['id_vendedor'];

    $sql_red = "SELECT tipo, enlace FROM red WHERE id_usuario = ?";
    $stmtRed = $conn->prepare($sql_red);
    $stmtRed->bind_param("i", $id_vend);
    $stmtRed->execute();
    $resRed = $stmtRed->get_result();

    while ($red = $resRed->fetch_assoc()) {
        if (strtolower($red['tipo']) === 'whatsapp') {
            $whatsapp = $red['enlace'];
        } elseif (strtolower($red['tipo']) === 'instagram') {
            $instagram = $red['enlace'];
        }
    }
    $stmtRed->close();

    // C) Empaquetamos la info del vendedor dentro del mismo resultado
    // Así tu JavaScript viejo sigue funcionando con data.titulo y el nuevo usa data.vendedor.foto
    $row['vendedor'] = [
        "nombre"       => $row['nombre_vendedor'],
        "departamento" => $row['contacto_vendedor'],
        "horario"      => $row['horario'],
        "foto"         => $foto_vendedor,
        "whatsapp"     => $whatsapp,
        "instagram"    => $instagram
    ];

    // Limpiamos la cadena binaria gigante de la foto original para no hacer pesado el JSON final
    unset($row['foto_de_perfil']);

    // -- FIN DEL NUEVO BLOQUE DEL VENDEDOR --

    echo json_encode($row);
} else {
    echo json_encode(["error" => "Producto no encontrado."]);
}

$stmt->close();
$conn->close();
?>