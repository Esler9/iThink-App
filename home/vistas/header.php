<?php
session_start();

$User    = isset($_SESSION["username"]) ? $_SESSION["username"] : null;
$cod_user = isset($_SESSION["cod_user"]) ? $_SESSION["cod_user"] : null;

// Definir la ruta base global como constante
define("BASE_PATH", "C:/xampp/htdocs/App.ithinkguatemala.com/home");

// Verificar que la carpeta exista (opcional)
if (!file_exists(BASE_PATH)) {
    die("No se encontró la carpeta base: " . BASE_PATH);
}

require_once(BASE_PATH . "/Setting.php");

// Si el sitio está en mantenimiento, redirige
if ($mantenimiento == true) {
    header("Location: " . BASE_PATH . "/mantenimiento.php");
    exit;
}

// Si el usuario no está autenticado, redirige al login
if (!isset($User)) {
    header("Location: " . BASE_PATH . "/login.php");
    exit();
}

require_once(BASE_PATH . "/conexion.php");
require_once(BASE_PATH . "/vistas/logica/ac_permiso.php");

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