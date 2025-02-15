<?php
session_start();

$User    = $_SESSION["username"];
$cod_user = $_SESSION["cod_user"];

// Incluir archivos utilizando rutas absolutas basadas en DOCUMENT_ROOT
require_once($_SERVER["DOCUMENT_ROOT"] . "/App.ithinkguatemala.com/home/Setting.php");

if ($mantenimiento === true) {
    header("Location: /App.ithinkguatemala.com/home/mantenimiento.php");
    exit;
}

if (!isset($User)) {
    header("Location: /App.ithinkguatemala.com/home/login.php");
    exit();
}

require_once($_SERVER["DOCUMENT_ROOT"] . "/App.ithinkguatemala.com/home/conexion.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/App.ithinkguatemala.com/home/vistas/logica/ac_permiso.php");

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