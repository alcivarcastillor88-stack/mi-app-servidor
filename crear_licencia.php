<?php
header("Content-Type: application/json");

// 🔐 Validar API KEY
$apiKey = isset($_SERVER['HTTP_X_API_KEY']) ? $_SERVER['HTTP_X_API_KEY'] : '';

if ($apiKey !== '9F7X-SECURE-2026-ALCIVAR') {
    echo json_encode(array("error" => "Acceso no autorizado"));
    exit;
}

// 📥 Recibir datos
$input = file_get_contents("php://input");
$data = json_decode($input, true);

$serial = isset($data['serial']) ? $data['serial'] : '';

if (!$serial) {
    echo json_encode(array("error" => "Serial requerido"));
    exit;
}

// 🔌 Conexión usando DATABASE_URL
$databaseUrl = getenv("DATABASE_URL");

if (!$databaseUrl) {
    echo json_encode(array("error" => "DATABASE_URL no configurada"));
    exit;
}

$url = parse_url($databaseUrl);

$host = isset($url['host']) ? $url['host'] : '';
$user = isset($url['user']) ? $url['user'] : '';
$pass = isset($url['pass']) ? $url['pass'] : '';
$port = isset($url['port']) ? $url['port'] : 3306;
$db   = isset($url['path']) ? str_replace('/', '', $url['path']) : '';

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    echo json_encode(array("error" => "Error de conexión"));
    exit;
}

// 🔎 Verificar si ya existe
$stmt = $conn->prepare("SELECT id FROM licencias WHERE serial = ?");
$stmt->bind_param("s", $serial);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(array("error" => "La licencia ya existe"));
    exit;
}

// 🟢 Insertar licencia
$insert = $conn->prepare("INSERT INTO licencias (serial, estado) VALUES (?, 'disponible')");
$insert->bind_param("s", $serial);
$insert->execute();

echo json_encode(array(
    "success" => true,
    "serial" => $serial
));
?>
