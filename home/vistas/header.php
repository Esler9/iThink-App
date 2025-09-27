<?php

session_start();

// Definir rutas globales (filesystem y URL base)
define('APP_ROOT', realpath(__DIR__ . '/..')); // carpeta "home"
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443 ? 'https://' : 'http://';
$documentRoot = realpath($_SERVER['DOCUMENT_ROOT']);
$basePath = str_replace('\\', '/', str_replace($documentRoot, '', APP_ROOT));
$basePath = rtrim($basePath, '/');
define('APP_URL', $protocol . $_SERVER['HTTP_HOST'] . $basePath);

// Variables de sesión
$User = isset($_SESSION["username"]) ? $_SESSION["username"] : null;
$cod_user = isset($_SESSION['cod_user']) ? $_SESSION['cod_user'] : null;

// Includes usando rutas absolutas
include_once(APP_ROOT . '/Setting.php');
include_once(APP_ROOT . '/conexion.php');
include_once(APP_ROOT . '/vistas/logica/ac_permiso.php');

// Mantenimiento (ruta URL global)
if (isset($mantenimiento) && $mantenimiento == true) {
  header('Location: ' . APP_URL . '/mantenimiento.php');
  exit;
}

// Validar sesión (ruta URL global -- ajustar si login está en otra ubicación)
if (!isset($User)) {
  header('Location: ' . APP_URL . '/login.php');
  exit();
}

// Obtención de tiendas a las que tiene acceso el usuario
$marcas = array();
if (!empty($cod_user)) {
  $sql = "SELECT cod_tienda FROM `asignacion_tienda` WHERE cod_user = " . intval($cod_user);
  $consulta = mysqli_query($conn, $sql);
  if ($consulta) {
    while ($fila = mysqli_fetch_array($consulta)) {
      $marcas[] = $fila['cod_tienda'];
    }
  }
}
if (!empty($marcas)) {
  $tiendas = implode(', ', $marcas);
  $_SESSION['tiendas'] = $tiendas;
} else {
  // Asegurar que la sesión tiene la clave aunque esté vacía
  $_SESSION['tiendas'] = isset($_SESSION['tiendas']) ? $_SESSION['tiendas'] : '';
}
$tiendas = $_SESSION['tiendas'];
?>