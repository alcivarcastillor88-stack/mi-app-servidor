<?php
$apiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';

if ($apiKey !== '9F7X-SECURE-2026-ALCIVAR') {
    echo json_encode(["error" => "Acceso no autorizado"]);
    exit;
}

header("Content-Type: application/json");

$databaseUrl = getenv("DATABASE_URL");

if (!$databaseUrl) {
    echo json_encode(["error" => "DATABASE_URL no configurada"]);
    exit;
}

$url = parse_url($databaseUrl);

$host = $url["host"];
$user = $url["user"];
$pass = $url["pass"];
$db   = ltrim($url["path"], "/");
$port = $url["port"];

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    echo json_encode(["error" => "Error de conexión"]);
    exit;
}

$result = $conn->query("SELECT * FROM licencias");

$datos = [];

while ($fila = $result->fetch_assoc()) {
    $datos[] = $fila;
}

echo json_encode($datos);

$conn->close();
