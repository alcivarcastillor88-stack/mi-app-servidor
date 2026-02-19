<?php

$url = getenv("DATABASE_URL");

$db = parse_url($url);

$host = $db["host"];
$user = $db["user"];
$pass = $db["pass"];
$dbname = ltrim($db["path"], '/');
$port = $db["port"];

$conexion = new mysqli($host, $user, $pass, $dbname, $port);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$sql = "CREATE TABLE IF NOT EXISTS licencias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    serial VARCHAR(50) NOT NULL,
    estado VARCHAR(20) DEFAULT 'disponible',
    dispositivo_id VARCHAR(255),
    fecha_activacion DATETIME
)";

if ($conexion->query($sql) === TRUE) {
    echo "Tabla creada correctamente 🚀";
} else {
    echo "Error creando tabla: " . $conexion->error;
}

$conexion->close();

?>

