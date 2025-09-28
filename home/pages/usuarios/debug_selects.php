<?php
include("../../../conexion.php");
header('Content-Type: application/json');

// Test tiendas
$sql_tiendas = "SELECT cod_tienda, nombre FROM tienda ORDER BY nombre ASC";
$res_tiendas = mysqli_query($conn, $sql_tiendas);
$tiendas_test = [];
$error_tiendas = null;
if ($res_tiendas) {
    while ($row = mysqli_fetch_assoc($res_tiendas)) {
        $tiendas_test[] = $row;
    }
} else {
    $error_tiendas = mysqli_error($conn);
}

// Test grupos
$sql_grupos = "SELECT codigo, nombre_grupo FROM grupo_user ORDER BY nombre_grupo ASC";
$res_grupos = mysqli_query($conn, $sql_grupos);
$grupos_test = [];
$error_grupos = null;
if ($res_grupos) {
    while ($row = mysqli_fetch_assoc($res_grupos)) {
        $grupos_test[] = $row;
    }
} else {
    $error_grupos = mysqli_error($conn);
}

echo json_encode([
    'tiendas_count' => count($tiendas_test),
    'tiendas' => $tiendas_test,
    'tiendas_error' => $error_tiendas,
    'grupos_count' => count($grupos_test),  
    'grupos' => $grupos_test,
    'grupos_error' => $error_grupos,
    'sql_tiendas' => $sql_tiendas,
    'sql_grupos' => $sql_grupos
], JSON_PRETTY_PRINT);
?>