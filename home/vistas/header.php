<?php
// Mostrar errores temporalmente para depuración (Quitar en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 1);

session_start();

// Definir APP_ROOT apuntando a la carpeta 'home'
$appRoot = realpath(__DIR__ . '/..');
if ($appRoot === false) {
  $appRoot = realpath(__DIR__);
}
define('APP_ROOT', $appRoot);

// Intento de determinar APP_URL asegurando que la base pública comience en '/home'
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
$normalized = str_replace('\\', '/', APP_ROOT);

// Forzar '/home' como base pública si existe en la ruta de filesystem
if (strpos($normalized, '/home') !== false) {
  $basePath = '/home';
} else {
  // Fallback: calcular respecto al document root
  $documentRoot = realpath($_SERVER['DOCUMENT_ROOT']) ?: '';
  $basePath = str_replace('\\', '/', str_replace($documentRoot, '', APP_ROOT));
  $basePath = '/' . trim($basePath, '/');
  if ($basePath === '//') $basePath = '/';
}

// Asegurar formato correcto y definir APP_URL
$basePath = '/' . ltrim($basePath, '/');
define('APP_URL', rtrim($protocol . $host . $basePath, '/'));

// Archivo de log local en home para revisar errores
ini_set('error_log', APP_ROOT . '/php-error.log');

// Variables de sesión
$User = isset($_SESSION["username"]) ? $_SESSION["username"] : null;
$cod_user = isset($_SESSION['cod_user']) ? $_SESSION['cod_user'] : null;

// Includes usando rutas absolutas basadas en APP_ROOT
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
if (!empty($cod_user) && isset($conn)) {
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
  $_SESSION['tiendas'] = isset($_SESSION['tiendas']) ? $_SESSION['tiendas'] : '';
}
$tiendas = $_SESSION['tiendas'];
?>