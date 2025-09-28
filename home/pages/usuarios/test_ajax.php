<?php
// Test directo para verificar las consultas
include("../../../conexion.php");

echo "<h3>Test de conexión y consultas</h3>";

// Test tiendas
echo "<h4>Tiendas:</h4>";
$sql = "SELECT cod_tienda, nombre FROM tienda ORDER BY nombre ASC";
$res = mysqli_query($conn, $sql);
if ($res) {
    echo "Consulta exitosa. Registros encontrados: " . mysqli_num_rows($res) . "<br>";
    while ($row = mysqli_fetch_assoc($res)) {
        echo "- " . $row['cod_tienda'] . ": " . $row['nombre'] . "<br>";
    }
} else {
    echo "Error en consulta tiendas: " . mysqli_error($conn) . "<br>";
}

echo "<br>";

// Test grupos
echo "<h4>Grupos:</h4>";
$sql2 = "SELECT codigo, nombre_grupo FROM grupo_user ORDER BY nombre_grupo ASC";
$res2 = mysqli_query($conn, $sql2);
if ($res2) {
    echo "Consulta exitosa. Registros encontrados: " . mysqli_num_rows($res2) . "<br>";
    while ($row = mysqli_fetch_assoc($res2)) {
        echo "- " . $row['codigo'] . ": " . $row['nombre_grupo'] . "<br>";
    }
} else {
    echo "Error en consulta grupos: " . mysqli_error($conn) . "<br>";
}

echo "<br><h4>Test AJAX:</h4>";
echo "<a href='usuarios_ajax.php' target='_blank'>Test usuarios_ajax.php</a>";
?>