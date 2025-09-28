<?php
session_start();
require_once("../../../conexion.php"); // usa $conn

// Debug ligero
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$DEBUG_LOG = '/tmp/usuarios_Accion_debug.log';
function dbg($msg){ global $DEBUG_LOG; @file_put_contents($DEBUG_LOG, date('c').' '.$msg.PHP_EOL, FILE_APPEND); }

function json_resp($data, $code = 200){
  header('Content-Type: application/json; charset=utf-8');
  http_response_code($code);
  echo json_encode($data);
  exit;
}

if (!isset($conn) || !$conn) {
  dbg("No DB connection");
  // si es AJAX pedimos JSON, si no redirigimos
  if (isset($_REQUEST['ajax']) && $_REQUEST['ajax']=='1') json_resp(['error'=>'No DB connection'],500);
  $_SESSION['error_usuario'] = "Error de conexión a BD.";
  header('Location: listado_users.php'); exit;
}

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

dbg("usuarios_Accion action={$action} method={$_SERVER['REQUEST_METHOD']} REQUEST=".json_encode($_REQUEST));

// Acciones que siempre devuelven JSON
if ($action === 'list_groups') {
  $out = [];
  $sql = "SELECT codigo, nombre_grupo FROM grupo_user ORDER BY nombre_grupo ASC";
  $res = mysqli_query($conn, $sql);
  if ($res) {
    while ($r = mysqli_fetch_assoc($res)) $out[] = $r;
    json_resp($out);
  } else json_resp(['error'=>mysqli_error($conn)],500);
}

if ($action === 'list_tiendas') {
  $out = [];
  $sql = "SELECT cod_tienda, nombre FROM tienda ORDER BY nombre ASC";
  $res = mysqli_query($conn, $sql);
  if ($res) {
    while ($r = mysqli_fetch_assoc($res)) $out[] = $r;
    json_resp($out);
  } else json_resp(['error'=>mysqli_error($conn)],500);
}

// obtener usuario (JSON)
if ($action === 'get_user') {
  $codigo = isset($_REQUEST['codigo']) ? intval($_REQUEST['codigo']) : 0;
  if ($codigo <= 0) json_resp(['error'=>'Código inválido'],400);
  $sql = "SELECT Codigo, `User`, Cod_Empleado, State, cod_tienda, email, email_active, id_group_user FROM `Usuarios` WHERE Codigo = {$codigo} LIMIT 1";
  $res = mysqli_query($conn, $sql);
  if ($res) {
    $row = mysqli_fetch_assoc($res);
    if ($row) json_resp(['success'=>true,'data'=>$row]);
    json_resp(['error'=>'Usuario no encontrado'],404);
  } else json_resp(['error'=>mysqli_error($conn)],500);
}

// Para create/update/delete soportamos AJAX (ajax=1 → JSON) o flujo clásico (redirect + $_SESSION)
$is_ajax = (isset($_REQUEST['ajax']) && $_REQUEST['ajax']=='1');
function finish($data, $redirect=true, $is_ajax=false){
  if ($is_ajax) return json_resp($data, isset($data['error'])?500:200);
  if (isset($data['success']) && $data['success']) $_SESSION['ok_usuario'] = $data['message'] ?? 'OK';
  else $_SESSION['error_usuario'] = $data['message'] ?? ($data['error'] ?? 'Error');
  header('Location: listado_users.php');
  exit;
}

// Helper: leer campo desde REQUEST y escapar
function rstr($k){ global $conn; return isset($_REQUEST[$k]) ? mysqli_real_escape_string($conn, trim($_REQUEST[$k])) : ''; }

switch ($action) {

  case 'create_user':
    // permitir GET/POST según requerimiento (evitar usar GET para password en producción)
    $user = rstr('user');
    $cod_empleado = rstr('cod_empleado');
    $password = isset($_REQUEST['password']) ? $_REQUEST['password'] : '';
    $state = rstr('state') ?: '1';
    $cod_tienda = isset($_REQUEST['cod_tienda']) ? intval($_REQUEST['cod_tienda']) : 0;
    $email = rstr('email');
    $email_active = isset($_REQUEST['email_active']) ? 1 : 0;
    $id_group = isset($_REQUEST['id_group']) ? intval($_REQUEST['id_group']) : 0;

    dbg("create_user payload user={$user} cod_tienda={$cod_tienda} id_group={$id_group}");

    if ($user === '' || $password === '' || $id_group <= 0 || $cod_tienda <= 0 || $email === '') {
      finish(['error'=>true,'message'=>'Faltan campos requeridos'], true, $is_ajax);
    }

    // comprobar usuario único
    $q = "SELECT 1 FROM `Usuarios` WHERE `User` = '{$user}' LIMIT 1";
    $r = mysqli_query($conn, $q);
    if ($r === false) finish(['error'=>true,'message'=>'DB error: '.mysqli_error($conn)], true, $is_ajax);
    if (mysqli_num_rows($r) > 0) finish(['error'=>true,'message'=>'Usuario ya existe'], true, $is_ajax);

    // comprobar grupo/tienda
    $rg = mysqli_query($conn, "SELECT 1 FROM grupo_user WHERE codigo = {$id_group} LIMIT 1");
    if ($rg === false || mysqli_num_rows($rg) === 0) finish(['error'=>true,'message'=>'Grupo inválido'], true, $is_ajax);
    $rt = mysqli_query($conn, "SELECT 1 FROM tienda WHERE cod_tienda = {$cod_tienda} LIMIT 1");
    if ($rt === false || mysqli_num_rows($rt) === 0) finish(['error'=>true,'message'=>'Tienda inválida'], true, $is_ajax);

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $hash_q = mysqli_real_escape_string($conn, $hash);

    $sql = "INSERT INTO `Usuarios` (`User`, `Cod_Empleado`, `Password`, `State`, `cod_tienda`, `email`, `email_active`, `id_group_user`)
            VALUES ('{$user}','{$cod_empleado}','{$hash_q}','{$state}',{$cod_tienda},'{$email}',{$email_active},{$id_group})";
    dbg("create_user sql: ".$sql);
    if (mysqli_query($conn, $sql)) {
      $id = mysqli_insert_id($conn);
      finish(['success'=>true,'message'=>'Usuario creado','id'=>$id], true, $is_ajax);
    } else finish(['error'=>true,'message'=>'Error al crear: '.mysqli_error($conn)], true, $is_ajax);
    break;

  case 'update_user':
    $codigo = isset($_REQUEST['codigo']) ? intval($_REQUEST['codigo']) : 0;
    if ($codigo <= 0) finish(['error'=>true,'message'=>'Código inválido'], true, $is_ajax);

    $user = rstr('user');
    $cod_empleado = rstr('cod_empleado');
    $password = isset($_REQUEST['password']) ? $_REQUEST['password'] : '';
    $state = rstr('state') ?: '1';
    $cod_tienda = isset($_REQUEST['cod_tienda']) ? intval($_REQUEST['cod_tienda']) : 0;
    $email = rstr('email');
    $email_active = isset($_REQUEST['email_active']) ? 1 : 0;
    $id_group = isset($_REQUEST['id_group']) ? intval($_REQUEST['id_group']) : 0;

    if ($user === '' || $id_group <= 0 || $cod_tienda <= 0 || $email === '') {
      finish(['error'=>true,'message'=>'Faltan campos requeridos'], true, $is_ajax);
    }

    // validar grupo/tienda
    $rg = mysqli_query($conn, "SELECT 1 FROM grupo_user WHERE codigo = {$id_group} LIMIT 1");
    if ($rg === false || mysqli_num_rows($rg) === 0) finish(['error'=>true,'message'=>'Grupo inválido'], true, $is_ajax);
    $rt = mysqli_query($conn, "SELECT 1 FROM tienda WHERE cod_tienda = {$cod_tienda} LIMIT 1");
    if ($rt === false || mysqli_num_rows($rt) === 0) finish(['error'=>true,'message'=>'Tienda inválida'], true, $is_ajax);

    if ($password !== '') {
      $hash = password_hash($password, PASSWORD_DEFAULT);
      $hash_q = mysqli_real_escape_string($conn, $hash);
      $sql = "UPDATE `Usuarios` SET `User`='{$user}', `Cod_Empleado`='{$cod_empleado}', `Password`='{$hash_q}', `State`='{$state}', `cod_tienda`={$cod_tienda}, `email`='{$email}', `email_active`={$email_active}, `id_group_user`={$id_group} WHERE Codigo = {$codigo}";
    } else {
      $sql = "UPDATE `Usuarios` SET `User`='{$user}', `Cod_Empleado`='{$cod_empleado}', `State`='{$state}', `cod_tienda`={$cod_tienda}, `email`='{$email}', `email_active`={$email_active}, `id_group_user`={$id_group} WHERE Codigo = {$codigo}";
    }
    dbg("update_user sql: ".$sql);
    if (mysqli_query($conn, $sql)) finish(['success'=>true,'message'=>'Usuario actualizado'], true, $is_ajax);
    else finish(['error'=>true,'message'=>'Error al actualizar: '.mysqli_error($conn)], true, $is_ajax);
    break;

  case 'delete_user':
    $codigo = isset($_REQUEST['codigo']) ? intval($_REQUEST['codigo']) : 0;
    if ($codigo <= 0) finish(['error'=>true,'message'=>'Código inválido'], true, $is_ajax);
    $sql = "DELETE FROM `Usuarios` WHERE Codigo = {$codigo}";
    dbg("delete_user sql: ".$sql);
    if (mysqli_query($conn, $sql)) finish(['success'=>true,'message'=>'Usuario eliminado'], true, $is_ajax);
    else finish(['error'=>true,'message'=>'Error al eliminar: '.mysqli_error($conn)], true, $is_ajax);
    break;

  case 'list_users':
    $out = [];
    $sql = "SELECT Codigo, `User`, Cod_Empleado, State, cod_tienda, email, email_active, id_group_user FROM `Usuarios` ORDER BY `User` ASC";
    $res = mysqli_query($conn, $sql);
    if ($res) {
      while ($r = mysqli_fetch_assoc($res)) $out[] = $r;
      json_resp($out);
    } else json_resp(['error'=>mysqli_error($conn)],500);
    break;

  default:
    dbg("accion no valida: ".json_encode($_REQUEST));
    if ($is_ajax) json_resp(['error'=>'Acción no válida'],400);
    $_SESSION['error_usuario'] = "Acción no válida.";
    header('Location: listado_users.php'); exit;
}
?>