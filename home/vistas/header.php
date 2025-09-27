<?php
// Mostrar errores temporalmente para depuración (Quitar en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 1);

session_start();

// Definir rutas base
$dirVistas = __DIR__; // .../home/vistas
$appHome = realpath($dirVistas . '/..'); // .../home
$projectRoot = realpath($dirVistas . '/../../'); // .../iThink-App (intento)
if ($appHome === false) $appHome = realpath($dirVistas);
if ($projectRoot === false) $projectRoot = $appHome;

define('APP_ROOT', $appHome);        // apunta a .../home
define('PROJECT_ROOT', $projectRoot); // apunta al proyecto raíz (iThink-App)

// Intento de determinar APP_URL asegurando que la base pública comience en '/home'
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
$normalized = str_replace('\\', '/', APP_ROOT);
if (strpos($normalized, '/home') !== false) {
  $basePath = '/home';
} else {
  $documentRoot = realpath($_SERVER['DOCUMENT_ROOT']) ?: '';
  $basePath = str_replace('\\', '/', str_replace($documentRoot, '', APP_ROOT));
  $basePath = '/' . trim($basePath, '/');
  if ($basePath === '//') $basePath = '/';
}
$basePath = '/' . ltrim($basePath, '/');
define('APP_URL', rtrim($protocol . $host . $basePath, '/'));
ini_set('error_log', APP_ROOT . '/php-error.log');

// Helper: intentar incluir desde varias rutas candidatas
function includeAny(array $candidates) {
  foreach ($candidates as $path) {
    // si ya es absoluta, usarla; si no, probar PROJECT_ROOT y APP_ROOT
    $pathsToTry = [];
    if (strpos($path, '/') === 0 || preg_match('/^[A-Za-z]:\\\\/', $path)) {
      $pathsToTry[] = $path;
    } else {
      $pathsToTry[] = PROJECT_ROOT . '/' . ltrim($path, '/');
      $pathsToTry[] = APP_ROOT . '/' . ltrim($path, '/');
      $pathsToTry[] = __DIR__ . '/' . ltrim($path, '/');
    }
    foreach ($pathsToTry as $p) {
      if (file_exists($p)) {
        include_once($p);
        return true;
      }
    }
  }
  // si no se incluyó ninguno, escribir en log
  error_log("includeAny: no se encontró ninguno de: " . implode(', ', $candidates));
  return false;
}

// Intentar incluir archivos necesarios desde múltiples ubicaciones
includeAny(['Setting.php', 'home/Setting.php', 'config/Setting.php']);
includeAny(['conexion.php', 'home/conexion.php', 'config/conexion.php']);
includeAny(['vistas/logica/ac_permiso.php', 'logica/ac_permiso.php', 'home/vistas/logica/ac_permiso.php', 'home/logica/ac_permiso.php']);

// Comprobación de existencia de la función antes de usarla
if (!function_exists('Tiene_permiso')) {
  error_log("Función Tiene_permiso() no definida. Verifica ac_permiso.php incluido desde: " . PROJECT_ROOT . " o " . APP_ROOT);
  // Mostrar mensaje legible en vez de fatal
  header('HTTP/1.1 500 Internal Server Error');
  echo "Error en el servidor: módulo de permisos no cargado. Revisa los logs.";
  exit();
}

// Mantenimiento (ruta URL global)
if (isset($mantenimiento) && $mantenimiento == true) {
  header('Location: ' . APP_URL . '/mantenimiento.php');
  exit;
}

// Variables de sesión
$User = isset($_SESSION["username"]) ? $_SESSION["username"] : null;
$cod_user = isset($_SESSION['cod_user']) ? $_SESSION['cod_user'] : null;

// Validar sesión (ruta URL global -- ajustar si login está en otra ubicación)
if (!isset($User)) {
  header('Location: ' . APP_URL . '/login.php');
  exit();
}

// Obtención de tiendas a las que tiene acceso el usuario (si existe $conn)
$marcas = array();
if (!empty($cod_user) && isset($conn)) {
  $sql = "SELECT cod_tienda FROM `asignacion_tienda` WHERE cod_user = " . intval($cod_user);
  $consulta = mysqli_query($conn, $sql);
  if ($consulta) {
    while ($fila = mysqli_fetch_array($consulta)) {
      $marcas[] = $fila['cod_tienda'];
    }
  } else {
    error_log("Consulta tiendas falló: " . (isset($conn) ? mysqli_error($conn) : 'sin $conn'));
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