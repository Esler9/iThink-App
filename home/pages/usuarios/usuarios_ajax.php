<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
if (session_status() == PHP_SESSION_NONE) session_start();

include_once("../../../conexion.php");

// Seguridad mínima: si no hay conexión, redirigir
if (!isset($conn) || !$conn) {
  $_SESSION['error_usuario'] = "No hay conexión a la base de datos.";
  header('Location: listado_users.php');
  exit;
}

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

switch ($action) {

  case 'create_user':
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      $_SESSION['error_usuario'] = "Método inválido.";
      dbg("create_user: método inválido: ".$_SERVER['REQUEST_METHOD']);
      header('Location: listado_users.php'); exit;
    }

    // Escapar valores (estilo accion.php)
    $user = isset($_POST['user']) ? mysqli_real_escape_string($conn, trim($_POST['user'])) : '';
    $cod_empleado = isset($_POST['cod_empleado']) ? mysqli_real_escape_string($conn, trim($_POST['cod_empleado'])) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $state = isset($_POST['state']) ? mysqli_real_escape_string($conn, trim($_POST['state'])) : '1';
    $cod_tienda = isset($_POST['cod_tienda']) ? intval($_POST['cod_tienda']) : 0;
    $email = isset($_POST['email']) ? mysqli_real_escape_string($conn, trim($_POST['email'])) : '';
    $email_active = isset($_POST['email_active']) ? 1 : 0;
    $id_group = isset($_POST['id_group']) ? intval($_POST['id_group']) : 0;

    dbg("create_user: datos recibidos user={$user} cod_empleado={$cod_empleado} cod_tienda={$cod_tienda} id_group={$id_group} email_active={$email_active}");

    if ($user === '' || $password === '' || $id_group <= 0 || $cod_tienda <= 0 || $email === '') {
      $_SESSION['error_usuario'] = "Faltan campos requeridos.";
      dbg("create_user: faltan campos");
      header('Location: listado_users.php'); exit;
    }

    // Verificar unicidad y existencia (puede comentar para debug)
    $q = "SELECT 1 FROM `Usuarios` WHERE `User` = '{$user}' LIMIT 1";
    dbg("create_user: query check user: ".$q);
    $r = mysqli_query($conn, $q);
    if ($r === false) {
      $err = "DB error (check user): " . mysqli_error($conn) . " SQL: {$q}";
      dbg($err);
      file_put_contents('/tmp/usuarios_debug.log', date('c')." ".$err.PHP_EOL, FILE_APPEND);
      $_SESSION['error_usuario'] = "Error DB al verificar usuario.";
      if ($show_debug) $_SESSION['error_usuario'] .= " Detalle: ".mysqli_error($conn);
      header('Location: listado_users.php'); exit;
    }
    if (mysqli_num_rows($r) > 0) {
      $_SESSION['error_usuario'] = "El usuario ya existe.";
      dbg("create_user: usuario ya existe");
      header('Location: listado_users.php'); exit;
    }

    // Hash y preparar INSERT
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $hash_q = mysqli_real_escape_string($conn, $hash);

    $sql = "INSERT INTO `Usuarios` (`User`, `Cod_Empleado`, `Password`, `State`, `cod_tienda`, `email`, `email_active`, `id_group_user`)
            VALUES ('{$user}', '{$cod_empleado}', '{$hash_q}', '{$state}', {$cod_tienda}, '{$email}', {$email_active}, {$id_group})";
    dbg("create_user: SQL insert: ".$sql);

    if (mysqli_query($conn, $sql)) {
      $_SESSION['ok_usuario'] = "Usuario creado correctamente.";
      dbg("create_user: insert OK id=".mysqli_insert_id($conn));
      header('Location: listado_users.php'); exit;
    } else {
      $err = "Error al crear usuario: " . mysqli_error($conn) . " | SQL: " . $sql;
      dbg($err);
      file_put_contents('/tmp/usuarios_debug.log', date('c')." ".$err.PHP_EOL, FILE_APPEND);
      $_SESSION['error_usuario'] = "Error al crear usuario.";
      if ($show_debug) $_SESSION['error_usuario'] .= " Detalle: ".mysqli_error($conn);
      header('Location: listado_users.php'); exit;
    }
    break;

  case 'update_user':
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      $_SESSION['error_usuario'] = "Método inválido.";
      header('Location: listado_users.php');
      exit;
    }
    $codigo = isset($_POST['codigo']) ? intval($_POST['codigo']) : 0;
    if ($codigo <= 0) {
      $_SESSION['error_usuario'] = "Código inválido.";
      header('Location: listado_users.php');
      exit;
    }
    $user = isset($_POST['user']) ? mysqli_real_escape_string($conn, trim($_POST['user'])) : '';
    $cod_empleado = isset($_POST['cod_empleado']) ? mysqli_real_escape_string($conn, trim($_POST['cod_empleado'])) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $state = isset($_POST['state']) ? mysqli_real_escape_string($conn, trim($_POST['state'])) : '1';
    $cod_tienda = isset($_POST['cod_tienda']) ? intval($_POST['cod_tienda']) : 0;
    $email = isset($_POST['email']) ? mysqli_real_escape_string($conn, trim($_POST['email'])) : '';
    $email_active = isset($_POST['email_active']) ? 1 : 0;
    $id_group = isset($_POST['id_group']) ? intval($_POST['id_group']) : 0;

    if ($user === '' || $id_group <= 0 || $cod_tienda <= 0 || $email === '') {
      $_SESSION['error_usuario'] = "Faltan campos requeridos.";
      header('Location: listado_users.php');
      exit;
    }

    // Validar existencia grupo/tienda
    $q = "SELECT 1 FROM `grupo_user` WHERE codigo = {$id_group} LIMIT 1";
    $r = mysqli_query($conn, $q);
    if ($r === false || mysqli_num_rows($r) === 0) {
      $_SESSION['error_usuario'] = "Grupo inválido.";
      header('Location: listado_users.php');
      exit;
    }
    $q = "SELECT 1 FROM `tienda` WHERE cod_tienda = {$cod_tienda} LIMIT 1";
    $r = mysqli_query($conn, $q);
    if ($r === false || mysqli_num_rows($r) === 0) {
      $_SESSION['error_usuario'] = "Tienda inválida.";
      header('Location: listado_users.php');
      exit;
    }

    if ($password !== '') {
      $hash = password_hash($password, PASSWORD_DEFAULT);
      $hash_q = mysqli_real_escape_string($conn, $hash);
      $sql = "UPDATE `Usuarios` SET `User`='{$user}', `Cod_Empleado`='{$cod_empleado}', `Password`='{$hash_q}', `State`='{$state}', cod`_tienda`={$cod_tienda}, `email`='{$email}', `email_active`={$email_active}, `id_group_user`={$id_group} WHERE Codigo = {$codigo}";
    } else {
      $sql = "UPDATE `Usuarios` SET `User`='{$user}', `Cod_Empleado`='{$cod_empleado}', `State`='{$state}', `cod_tienda`={$cod_tienda}, `email`='{$email}', `email_active`={$email_active}, `id_group_user`={$id_group} WHERE Codigo = {$codigo}";
    }

    if (mysqli_query($conn, $sql)) {
      $_SESSION['ok_usuario'] = "Usuario actualizado.";
      header('Location: listado_users.php');
      exit;
    } else {
      $_SESSION['error_usuario'] = "Error al actualizar usuario: " . mysqli_error($conn);
      header('Location: listado_users.php');
      exit;
    }
    break;

  case 'delete_user':
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      $_SESSION['error_usuario'] = "Método inválido.";
      header('Location: listado_users.php');
      exit;
    }
    $codigo = isset($_POST['codigo']) ? intval($_POST['codigo']) : 0;
    if ($codigo <= 0) {
      $_SESSION['error_usuario'] = "Código inválido.";
      header('Location: listado_users.php');
      exit;
    }
    $sql = "DELETE FROM `Usuarios` WHERE Codigo = {$codigo}";
    if (mysqli_query($conn, $sql)) {
      $_SESSION['ok_usuario'] = "Usuario eliminado.";
      header('Location: listado_users.php');
      exit;
    } else {
      $_SESSION['error_usuario'] = "Error al eliminar usuario: " . mysqli_error($conn);
      header('Location: listado_users.php');
      exit;
    }
    break;

  // Manejo inmediato de listados (debe devolver JSON sin redirigir)
  case 'list_groups':
    header('Content-Type: application/json; charset=utf-8');
    $out = [];
    $sql = "SELECT codigo, nombre_grupo FROM grupo_user ORDER BY nombre_grupo ASC";
    if ($res = mysqli_query($conn, $sql)) {
      while ($r = mysqli_fetch_assoc($res)) $out[] = ['codigo' => $r['codigo'], 'nombre' => $r['nombre_grupo']];
      echo json_encode($out);
      exit;
    } else {
      echo json_encode(['error' => mysqli_error($conn)]);
      exit;
    }
    break;

  case 'list_tiendas':
    header('Content-Type: application/json; charset=utf-8');
    $out = [];
    $sql = "SELECT cod_tienda, nombre FROM tienda ORDER BY nombre ASC";
    if ($res = mysqli_query($conn, $sql)) {
      while ($r = mysqli_fetch_assoc($res)) $out[] = ['cod_tienda' => $r['cod_tienda'], 'nombre' => $r['nombre']];
      echo json_encode($out);
      exit;
    } else {
      echo json_encode(['error' => mysqli_error($conn)]);
      exit;
    }
    break;

  default:
    $_SESSION['error_usuario'] = "Acción no válida.";
    header('Location: listado_users.php');
    exit;
}

// === DEPURACIÓN (añadir al inicio del archivo, tras include_once("../../../conexion.php");) ===
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$DEBUG_LOG = '/tmp/usuarios_debug.log';
function dbg($msg) {
  global $DEBUG_LOG;
  // evita escribir si el log no es escribible
  @file_put_contents($DEBUG_LOG, date('c').' '.$msg.PHP_EOL, FILE_APPEND);
}

$show_debug = (isset($_REQUEST['debug']) && $_REQUEST['debug'] == '1');

dbg("=== usuarios_ajax.php iniciando ===");
dbg("Action: ".(isset($_REQUEST['action'])?$_REQUEST['action']:'(none)')." Method: ".$_SERVER['REQUEST_METHOD']);
dbg("GET: ".json_encode($_GET));
dbg("POST: ".json_encode($_POST));
dbg("SESSION: ".json_encode(array_diff_key($_SESSION, array_flip(['__ci_last_regenerate']))));
