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

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["serial"])) {
    echo json_encode(["error" => "Falta el serial"]);
    exit;
}

$serial = $data["serial"];

$stmt = $conn->prepare("INSERT INTO licencias (serial) VALUES (?)");
$stmt->bind_param("s", $serial);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "id" => $stmt->insert_id
    ]);
} else {
    echo json_encode(["error" => "No se pudo insertar"]);
}

$stmt->close();
$conn->close();
