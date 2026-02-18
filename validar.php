<?php

$host = getenv("MYSQLHOST");
$user = getenv("MYSQLUSER");
$password = getenv("MYSQLPASSWORD");
$database = getenv("MYSQLDATABASE");
$port = getenv("MYSQLPORT");

$conexion = new mysqli($host, $user, $password, $database, $port);

if ($conexion->connect_error) {
    die("Error de conexión");
}

$serial = $_POST['serial'];
$device = $_POST['device'];

$resultado = $conexion->query("SELECT * FROM licencias WHERE serial='$serial'");

if ($fila = $resultado->fetch_assoc()) {

    if ($fila['estado'] == 'disponible') {

        $conexion->query("UPDATE licencias 
            SET estado='activado',
                dispositivo_id='$device',
                fecha_activacion=NOW()
            WHERE serial='$serial'");

        echo "OK";

    } else if ($fila['dispositivo_id'] == $device) {

        echo "OK";

    } else {

        echo "USADO";
    }

} else {
    echo "INVALIDO";
}

$conexion->close();

?>
