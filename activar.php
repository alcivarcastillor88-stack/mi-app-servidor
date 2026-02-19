<?php
header("Content-Type: application/json");

// 🔐 Validar API KEY
$apiKey = isset($_SERVER['HTTP_X_API_KEY']) ? $_SERVER['HTTP_X_API_KEY'] : '';

if ($apiKey !== '9F7X-SECURE-2026-ALCIVAR') {
    echo json_encode(array("error" => "Acceso no autorizado"));
    exit;
}

// 📥 Recibir datos JSON
$input = file_get_contents("php://input");
$data = json_decode($input, true);

$serial = isset($data['serial']) ? $data['serial'] : '';
$dispositivo_id = isset($data['dispositivo_id']) ? $data['dispositivo_id'] : '';

if (!$serial || !$dispositivo_id) {
    echo json_encode(array("error" => "Datos incompletos"));
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
    echo json_encode(array(
        "error" => "Error de conexión",
        "detalle" => $conn->connect_error
    ));
    exit;
}

// 🔍 Buscar licencia
$stmt = $conn->prepare("SELECT estado, dispositivo_id FROM licencias WHERE serial = ?");
$stmt->bind_param("s", $serial);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(array("error" => "Licencia no encontrada"));
    exit;
}

$row = $result->fetch_assoc();

// 🟢 Activar si está disponible
if ($row['estado'] === "disponible") {

    $update = $conn->prepare("UPDATE licencias SET estado='activado', dispositivo_id=?, fecha_activacion=NOW() WHERE serial=?");
    $update->bind_param("ss", $dispositivo_id, $serial);
    $update->execute();

    echo json_encode(array("success" => true));
    exit;
}

// 🟡 Si ya está activada
if ($row['estado'] === "activado") {

    if ($row['dispositivo_id'] === $dispositivo_id) {
        echo json_encode(array("success" => true));
        exit;
    }

    echo json_encode(array("error" => "Licencia ya vinculada a otro dispositivo"));
    exit;
}
?>
