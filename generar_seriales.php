<?php

$databaseUrl = getenv('DATABASE_URL');

$url = parse_url($databaseUrl);

$host = $url['host'];
$user = $url['user'];
$pass = $url['pass'];
$db   = ltrim($url['path'], '/');
$port = $url['port'];

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die("Error conexión: " . $conn->connect_error);
}

function generarSerial() {
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $serial = 'ALC-';

    for ($i = 0; $i < 3; $i++) {
        for ($j = 0; $j < 4; $j++) {
            $serial .= $chars[rand(0, strlen($chars)-1)];
        }
        if ($i < 2) $serial .= '-';
    }

    return $serial;
}

for ($i = 0; $i < 5000; $i++) {

    $serial = generarSerial();

    $stmt = $conn->prepare("INSERT IGNORE INTO licencias (serial) VALUES (?)");
    $stmt->bind_param("s", $serial);
    $stmt->execute();
}

echo "5000 seriales generados";
