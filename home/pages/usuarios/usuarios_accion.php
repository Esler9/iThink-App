<?php
// Este archivo es la versión renombrada de usuarios_Accion.php -> usuarios_accion.php
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

// --- NUEVA DEPURACIÓN: headers, raw input, superglobals ---
$HDRS = function_exists('getallheaders') ? getallheaders() : [];
$RAW_INPUT = @file_get_contents('php://input');
dbg("REQ HEADERS: ".json_encode($HDRS));
dbg("RAW INPUT: " . $RAW_INPUT);
dbg("_GET: ".json_encode($_GET));
dbg("_POST: ".json_encode($_POST));
dbg("_REQUEST: ".json_encode($_REQUEST));
dbg("_COOKIE: ".json_encode($_COOKIE));
dbg("_SESSION keys: ".json_encode(isset($_SESSION)?array_keys($_SESSION):[]));
dbg("REMOTE_ADDR: ".($_SERVER['REMOTE_ADDR'] ?? 'unknown')." URI: ".($_SERVER['REQUEST_URI'] ?? ''));

// Preferir datos desde POST
$DATA = $_POST;
dbg("_POST explicit: ".json_encode($DATA));

// helper para obtener valor bruto (POST preferente, luego REQUEST)
function gv($k){
  global $DATA;
  if (isset($DATA[$k])) return $DATA[$k];
  if (isset($_REQUEST[$k])) return $_REQUEST[$k];
  return null;
}

// Guardar estructura para incluirla en JSON cuando debug=1
$DEBUG_RECEIVED = [
  'headers' => $HDRS,
  'raw_input' => $RAW_INPUT,
  '_GET' => $_GET,
  '_POST' => $_POST,
  '_REQUEST' => $_REQUEST,
  '_COOKIE' => $_COOKIE,
  'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? null,
  'request_uri' => $_SERVER['REQUEST_URI'] ?? null
];

// si no hay conexión DB
if (!isset($conn) || !$conn) {
  dbg("No DB connection");
  if (isset($_REQUEST['ajax']) && $_REQUEST['ajax']=='1') {
    $resp = ['error'=>'No DB connection'];
    if (isset($_REQUEST['debug']) && $_REQUEST['debug']=='1') $resp['debug_received'] = $DEBUG_RECEIVED;
    json_resp($resp,500);
  }
  $_SESSION['error_usuario'] = "Error de conexión a BD.";
  header('Location: listado_users.php'); exit;
}

// --- Modificar finish para adjuntar debug_received cuando corresponde ---
$is_ajax = ((isset($DATA['ajax']) && $DATA['ajax']=='1') || (isset($_REQUEST['ajax']) && $_REQUEST['ajax']=='1'));

function finish($data, $redirect=true, $is_ajax=false){
  global $DEBUG_RECEIVED;
  if ($is_ajax) {
    if (isset($_REQUEST['debug']) && $_REQUEST['debug']=='1') $data['debug_received'] = $DEBUG_RECEIVED;
    return json_resp($data, isset($data['error'])?500:200);
  }
  if (isset($data['success']) && $data['success']) $_SESSION['ok_usuario'] = $data['message'] ?? 'OK';
  else $_SESSION['error_usuario'] = $data['message'] ?? ($data['error'] ?? 'Error');
  header('Location: listado_users.php');
  exit;
}

// Helper: leer campo desde POST preferente y escapar
function rstr($k){ global $conn, $DATA; 
  if (isset($DATA[$k])) return mysqli_real_escape_string($conn, trim($DATA[$k]));
  if (isset($_REQUEST[$k])) return mysqli_real_escape_string($conn, trim($_REQUEST[$k]));
  return '';
}

// Asegurar action (POST preferente)
$action = gv('action') ?? '';

// --- RUTINAS ---
switch ($action) {

  case 'create_user':
    // permitir GET/POST según requerimiento (evitar usar GET para password en producción)
    $user = rstr('user');
    $cod_empleado = rstr('cod_empleado');
    $password = gv('password') ?? '';
    $state = rstr('state') ?: '1';
    $cod_tienda = intval(gv('cod_tienda') ?? 0);
    $email = rstr('email');
    $email_active = (gv('email_active') !== null && gv('email_active') !== '') ? 1 : 0;
    $id_group = intval(gv('id_group') ?? 0);

    dbg("create_user payload user={$user} cod_tienda={$cod_tienda} id_group={$id_group}");

    // validación detallada (muestra campos faltantes si debug=1)
    $missing = [];
    if ($user === '') $missing[] = 'user';
    if ($password === '') $missing[] = 'password';
    if ($id_group <= 0) $missing[] = 'id_group';
    if ($cod_tienda <= 0) $missing[] = 'cod_tienda';
    if ($email === '') $missing[] = 'email';

    if (!empty($missing)) {
      $detail = 'Faltan campos requeridos: ' . implode(', ', $missing);
      // si debug=1 añadimos lo que realmente llegó
      if (isset($_REQUEST['debug']) && $_REQUEST['debug'] == '1') {
        $received = print_r($_REQUEST, true);
        $detail .= "\n\nREQUEST recibida:\n" . $received;
      }
      finish(['error'=>true,'message'=>$detail], true, $is_ajax);
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
    $codigo = intval(gv('codigo') ?? 0);
    if ($codigo <= 0) finish(['error'=>true,'message'=>'Código inválido'], true, $is_ajax);

    $user = rstr('user');
    $cod_empleado = rstr('cod_empleado');
    $password = gv('password') ?? '';
    $state = rstr('state') ?: '1';
    $cod_tienda = intval(gv('cod_tienda') ?? 0);
    $email = rstr('email');
    $email_active = (gv('email_active') !== null && gv('email_active') !== '') ? 1 : 0;
    $id_group = intval(gv('id_group') ?? 0);

    // validación detallada para update
    $missing = [];
    if ($user === '') $missing[] = 'user';
    if ($id_group <= 0) $missing[] = 'id_group';
    if ($cod_tienda <= 0) $missing[] = 'cod_tienda';
    if ($email === '') $missing[] = 'email';

    if (!empty($missing)) {
      $detail = 'Faltan campos requeridos: ' . implode(', ', $missing);
      $msg = (isset($_REQUEST['debug']) && $_REQUEST['debug']=='1') ? $detail : 'Faltan campos requeridos.';
      finish(['error'=>true,'message'=>$msg], true, $is_ajax);
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
    $codigo = intval(gv('codigo') ?? 0);
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