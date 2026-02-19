<?php

$databaseUrl = getenv("DATABASE_URL");

if (!$databaseUrl) {
    die("DATABASE_URL no está configurada");
}

$url = parse_url($databaseUrl);

$host = $url["host"];
$user = $url["user"];
$pass = $url["pass"];
$db   = ltrim($url["path"], "/");
$port = $url["port"];

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$sql = "CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
)";

if ($conn->query($sql) === TRUE) {
    echo "Tabla creada correctamente 🚀";
} else {
    echo "Error creando tabla: " . $conn->error;
}

$conn->close();
