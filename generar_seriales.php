<?php

$conn = new mysqli(
    getenv("MYSQLHOST"),
    getenv("MYSQLUSER"),
    getenv("MYSQLPASSWORD"),
    getenv("MYSQLDATABASE"),
    getenv("MYSQLPORT")
);

if ($conn->connect_error) {
    die("Error conexión");
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
