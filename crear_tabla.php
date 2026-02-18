<?php

$host = getenv("MYSQLHOST");
$user = getenv("MYSQLUSER");
$password = getenv("MYSQLPASSWORD");
$database = getenv("MYSQLDATABASE");
$port = getenv("MYSQLPORT");

$conexion = new mysqli($host, $user, $password, $database, $port);

$sql = "CREATE TABLE IF NOT EXISTS licencias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    serial VARCHAR(50) NOT NULL,
    estado VARCHAR(20) DEFAULT 'disponible',
    dispositivo_id VARCHAR(255),
    fecha_activacion DATETIME
)";

if ($conexion->query($sql) === TRUE) {
    echo "Tabla creada correctamente";
} else {
    echo "Error creando tabla";
}

$conexion->close();

?>
