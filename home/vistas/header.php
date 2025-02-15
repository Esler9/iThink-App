<?php
session_start();

$User = $_SESSION["username"];
$cod_user = $_SESSION["cod_user"];

// Definir la ruta base global
$basePath = $_SERVER["DOCUMENT_ROOT"] . "/home";

// Incluir archivos usando rutas absolutas
include($basePath . "/Setting.php");

if ($mantenimiento == true) {
    header("Location: " . $basePath . "/mantenimiento.php");
    exit;
}

if (!isset($User)) {
    header("Location: " . $basePath . "/login.php");
    exit();
}

include($basePath . "/conexion.php");
include($basePath . "/vistas/logica/ac_permiso.php");

// Obtención de tiendas a las que tiene acceso el usuario
$sql = "SELECT cod_tienda FROM asignacion_tienda WHERE cod_user = $cod_user";
$consulta = mysqli_query($conn, $sql);
$marcas = array();
while ($fila = mysqli_fetch_array($consulta)) {
    $marcas[] = $fila['cod_tienda'];
}
if (!empty($marcas)) {
    $tiendas = implode(', ', $marcas);
    $_SESSION['tiendas'] = $tiendas;
} else {
    $_SESSION['tiendas'] = "";
}
$tiendas = $_SESSION['tiendas'];
?>