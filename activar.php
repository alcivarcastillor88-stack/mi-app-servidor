<?php
$headers = getallheaders();
$apiKey = getenv("API_KEY");

if (!isset($headers["X-API-KEY"]) || $headers["X-API-KEY"] !== $apiKey) {
    http_response_code(403);
    echo json_encode(["error" => "Acceso no autorizado"]);
    exit;
}

header("Content-Type: application/json");

$databaseUrl = getenv("DATABASE_URL");

$url = parse_url($databaseUrl);

$conn = new mysqli(
    $url["host"],
    $url["user"],
    $url["pass"],
    ltrim($url["path"], "/"),
    $url["port"]
);

if ($conn->connect_error) {
    echo json_encode(["error" => "Error de conexión"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["serial"]) || !isset($data["dispositivo_id"])) {
    echo json_encode(["error" => "Faltan datos"]);
    exit;
}

$serial = $data["serial"];
$dispositivo = $data["dispositivo_id"];

// Verificar si existe y está disponible
$stmt = $conn->prepare("SELECT estado FROM licencias WHERE serial = ?");
$stmt->bind_param("s", $serial);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo json_encode(["error" => "Licencia no encontrada"]);
    exit;
}

$fila = $result->fetch_assoc();

if ($fila["estado"] !== "disponible") {
    echo json_encode(["error" => "Licencia ya activada"]);
    exit;
}

// Activar licencia
$stmt = $conn->prepare("
    UPDATE licencias 
    SET estado='activado',
        dispositivo_id=?,
        fecha_activacion=NOW()
    WHERE serial=?
");

$stmt->bind_param("ss", $dispositivo, $serial);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["error" => "No se pudo activar"]);
}

$conn->close();
