<?php

$databaseUrl = getenv('DATABASE_URL');
$url = parse_url($databaseUrl);

$conn = new mysqli(
    $url['host'],
    $url['user'],
    $url['pass'],
    ltrim($url['path'], '/'),
    $url['port']
);

if ($conn->connect_error) {
    die("Error conexión");
}

$result = $conn->query("SELECT serial, estado FROM licencias");

header('Content-Type: text/plain');

while ($row = $result->fetch_assoc()) {
    echo $row['serial'] . " | " . $row['estado'] . "\n";
}
