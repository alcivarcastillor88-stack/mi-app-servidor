<?php
header("Content-Type: application/json");

// 🔐 Validar API KEY
$apiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';

if ($apiKey !== '9F7X-SECURE-2026-ALCIVAR') {
    echo json_encode(["error" => "Acceso no autorizado"]);
    exit;
}

// 📥 Recibir datos
$data = json_decode(file_get_contents("php://input"), true);

$serial = $data['serial'] ?? '';
$dispositivo_id = $data['dispositivo_id'] ?? '';

if (!$serial || !$dispositivo_id) {
    echo json_encode(["error" => "Datos incompletos"]);
    exit;
}

// 🔌 Conexión BD
$conn = new mysqli("localhost", "usuario", "password", "basedatos");

if ($conn->connect_error) {
    echo json_encode(["error" => "Error de conexión"]);
    exit;
}

// 🔍 Buscar licencia
$stmt = $conn->prepare("SELECT estado, dispositivo_id FROM licencias WHERE serial = ?");
$stmt->bind_param("s", $serial);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["error" => "Licencia no encontrada"]);
    exit;
}

$row = $result->fetch_assoc();

// 🟢 Si está disponible → activar
if ($row['estado'] === "disponible") {

    $update = $conn->prepare("UPDATE licencias 
                              SET estado='activado', 
                                  dispositivo_id=?, 
                                  fecha_activacion=NOW() 
                              WHERE serial=?");
    $update->bind_param("ss", $dispositivo_id, $serial);
    $update->execute();

    echo json_encode(["success" => true]);
    exit;
}

// 🟡 Si ya está activada
if ($row['estado'] === "activado") {

    // ✅ Mismo dispositivo → permitir
    if ($row['dispositivo_id'] === $dispositivo_id) {
        echo json_encode(["success" => true]);
        exit;
    }

    // ❌ Otro dispositivo → bloquear
    echo json_encode([
        "error" => "Licencia ya vinculada a otro dispositivo"
    ]);
    exit;
}
?>
