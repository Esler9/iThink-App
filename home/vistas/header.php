<?php
session_start();

// Definir la ruta absoluta del proyecto y la URL base (ajusta según tu entorno)
define("BASE_PATH", "C:/xampp/htdocs/App.ithinkguatemala.com/home");
define("BASE_URL", "/App.ithinkguatemala.com/home");

$User    = isset($_SESSION["username"]) ? $_SESSION["username"] : null;
$cod_user = isset($_SESSION["cod_user"]) ? $_SESSION["cod_user"] : null;

// Incluir archivo de configuración usando rutas absolutas
require_once(BASE_PATH . "/Setting.php");

if ($mantenimiento === true) {
    header("Location: " . BASE_URL . "/mantenimiento.php");
    exit;
}

if (!$User) {
    header("Location: " . BASE_URL . "/login.php");
    exit();
}

// Incluir archivos necesarios usando rutas absolutas
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