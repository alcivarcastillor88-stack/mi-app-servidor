<?php
header("Content-Type: application/json");

// 🔐 1️⃣ Validar API KEY
$apiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';

if ($apiKey !== '9F7X-SECURE-2026-ALCIVAR') {
    echo json_encode(["error" => "Acceso no autorizado"]);
    exit;
}

// 📥 2️⃣ Recibir datos JSON
$data = json_decode(file_get_contents("php://input"), true);

$serial = $data['serial'] ?? '';
$dispositivo_id = $data['dispositivo_id'] ?? '';

if (!$serial || !$dispositivo_id) {
    echo json_encode(["error" => "Datos incompletos"]);
    exit;
}

// 🔌 3️⃣ Conexión profesional usando DATABASE_URL (Railway)
$databaseUrl = getenv("DATABASE_URL");

if (!$databaseUrl) {
    echo json_encode(["error" => "DATABASE_URL no configurada"]);
    exit;
}

$url = parse_url($databaseUrl);

$conn = new mysqli(
    $url["host"],
    $url["user"],
    $url["pass"],
    ltrim($url["path"], "/"),
    $url["port"]
);

if ($conn->connect_error) {
    echo json_encode([
        "error" => "Error de conexión a la base de datos",
        "detalle" => $conn->connect_error
    ]);
    exit;
}

// 🔍 4️⃣ Buscar licencia
$stmt = $conn->prepare("SELECT estado, dispositivo_id FROM licencias WHERE serial = ?");
$stmt->bind_param("s", $serial);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["error" => "Licencia no encontrada"]);
    exit;
}

$row = $result->fetch_assoc();

// 🟢 5️⃣ Si está disponible → activar
if ($row['estado'] === "disponible") {

    $update = $conn->prepare("UPDATE licencias 
                              SET estado='activado', 
                                  dispositivo_id=?, 
                                  fecha_activacion=NOW() 
                              WHERE serial=?");
    $update->bind_param("ss", $dispositivo_id, $serial);
    $update->execute();

    echo json_encode(["success" => t]()_
