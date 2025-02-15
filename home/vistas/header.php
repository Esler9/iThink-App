<?php
session_start();
$User = $_SESSION["username"];
$cod_user = $_SESSION['cod_user'];

include($_SERVER['DOCUMENT_ROOT'].'/home/Setting.php');

if ($mantenimiento == true) {
  header('Location: /home/mantenimiento.php');
  exit;
}

if (!isset($User)) {
  header('Location: /home/login.php');
  exit();
}

include($_SERVER['DOCUMENT_ROOT'].'/home/conexion.php');
include($_SERVER['DOCUMENT_ROOT'].'/home/vistas/logica/ac_permiso.php');

// Obtención de tiendas a las que tiene acceso el usuario
$sql = "SELECT cod_tienda FROM `asignacion_tienda` WHERE cod_user = $cod_user";
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