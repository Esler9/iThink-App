<?php

function conectar() {
    $user = "u229362226_ithink_pruebas";
    $pass = "H3xa124578.";
    $server = "localhost";
    $db = "u229362226_ithink_pruebas";

    // Intentar conexión
    $conn = mysqli_connect($server, $user, $pass, $db);

    // Verificar si la conexión es exitosa
    if (!$conn) {
        die("Error al conectar a la base de datos: " . mysqli_connect_error());
    }

    return $conn;
}

$conn =conectar();
?>