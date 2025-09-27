<?php
// Mostrar errores temporalmente para depuración (Quitar en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php-error.log');

session_start();
$User = $_SESSION["username"];
$cod_user = $_SESSION['cod_user'];

include('../Setting.php');

if ($mantenimiento == true) {
  header('location:../mantenimiento.php');
  exit;
}

if (!isset($User)) {
  header('location:login.php');
  exit();
}

include("../conexion.php");
include("logica/ac_permiso.php");

// Obtención de tiendas a las que tiene acceso el usuario
$sql = "SELECT cod_tienda FROM `asignacion_tienda` where cod_user = $cod_user";
$consulta = mysqli_query($conn, $sql);
while ($fila = mysqli_fetch_array($consulta)) {
  $marcas[] = $fila['cod_tienda'];
}
if (!empty($marcas)) {
  $tiendas = implode(', ', $marcas);
  $_SESSION['tiendas'] = $tiendas;
}
$tiendas = $_SESSION['tiendas'];
?>