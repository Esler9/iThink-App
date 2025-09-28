<?php
header('Content-Type: application/json; charset=utf-8');
if (session_status() == PHP_SESSION_NONE) session_start();

include_once("../../../conexion.php");

function resp($data, $code = 200) {
  http_response_code($code);
  echo json_encode($data);
  exit;
}

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

if (!isset($conn) || !$conn) {
  resp(['error' => 'No DB connection'], 500);
}

switch ($action) {
  case 'list_groups':
    $out = [];
    $sql = "SELECT codigo, nombre_grupo FROM grupo_user ORDER BY nombre_grupo ASC";
    $res = mysqli_query($conn, $sql);
    if ($res) {
      while ($r = mysqli_fetch_assoc($res)) $out[] = ['codigo' => $r['codigo'], 'nombre' => $r['nombre_grupo']];
      resp($out);
    } else {
      resp(['error' => 'DB error', 'detail' => mysqli_error($conn), 'sql' => $sql], 500);
    }
    break;

  case 'list_tiendas':
    $out = [];
    $sql = "SELECT cod_tienda, nombre FROM tienda ORDER BY nombre ASC";
    $res = mysqli_query($conn, $sql);
    if ($res) {
      while ($r = mysqli_fetch_assoc($res)) $out[] = ['cod_tienda' => $r['cod_tienda'], 'nombre' => $r['nombre']];
      resp($out);
    } else {
      resp(['error' => 'DB error', 'detail' => mysqli_error($conn), 'sql' => $sql], 500);
    }
    break;

  case 'create_user':
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') resp(['error' => 'POST requerido'], 405);

    // Tomar y escapar valores (estilo accion.php)
    $user = isset($_POST['user']) ? mysqli_real_escape_string($conn, trim($_POST['user'])) : '';
    $cod_empleado = isset($_POST['cod_empleado']) ? mysqli_real_escape_string($conn, trim($_POST['cod_empleado'])) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $state = isset($_POST['state']) ? mysqli_real_escape_string($conn, trim($_POST['state'])) : '1';
    $cod_tienda = isset($_POST['cod_tienda']) ? intval($_POST['cod_tienda']) : 0;
    $email = isset($_POST['email']) ? mysqli_real_escape_string($conn, trim($_POST['email'])) : '';
    $email_active = isset($_POST['email_active']) ? 1 : 0;
    $id_group = isset($_POST['id_group']) ? intval($_POST['id_group']) : 0;

    if ($user === '' || $password === '' || $id_group <= 0 || $cod_tienda <= 0 || $email === '') {
      resp(['error' => 'Faltan campos requeridos'], 400);
    }

    // Verificar usuario único
    $q = "SELECT 1 FROM `Usuarios` WHERE `User` = '{$user}' LIMIT 1";
    $r = mysqli_query($conn, $q);
    if ($r === false) resp(['error' => 'DB error', 'detail' => mysqli_error($conn), 'sql' => $q], 500);
    if (mysqli_num_rows($r) > 0) resp(['error' => 'Usuario ya existe'], 409);

    // Verificar grupo existe
    $q = "SELECT 1 FROM `grupo_user` WHERE codigo = {$id_group} LIMIT 1";
    $r = mysqli_query($conn, $q);
    if ($r === false) resp(['error' => 'DB error', 'detail' => mysqli_error($conn), 'sql' => $q], 500);
    if (mysqli_num_rows($r) === 0) resp(['error' => 'Grupo no existe'], 400);

    // Verificar tienda existe
    $q = "SELECT 1 FROM `tienda` WHERE cod_tienda = {$cod_tienda} LIMIT 1";
    $r = mysqli_query($conn, $q);
    if ($r === false) resp(['error' => 'DB error', 'detail' => mysqli_error($conn), 'sql' => $q], 500);
    if (mysqli_num_rows($r) === 0) resp(['error' => 'Tienda no existe'], 400);

    // Hash y escape
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $hash_q = mysqli_real_escape_string($conn, $hash);

    // Insert (estilo accion.php)
    $sql = "INSERT INTO `Usuarios` (`User`, `Cod_Empleado`, `Password`, `State`, `cod_tienda`, `email`, `email_active`, `id_group_user`)
            VALUES ('{$user}', '{$cod_empleado}', '{$hash_q}', '{$state}', {$cod_tienda}, '{$email}', {$email_active}, {$id_group})";
    if (mysqli_query($conn, $sql)) {
      resp(['success' => true, 'message' => 'Usuario creado', 'id' => mysqli_insert_id($conn)]);
    } else {
      resp(['error' => 'Insert failed', 'detail' => mysqli_error($conn), 'sql' => $sql], 500);
    }
    break;

  case 'get_user':
    $codigo = isset($_GET['codigo']) ? intval($_GET['codigo']) : 0;
    if ($codigo <= 0) resp(['error' => 'Código inválido'], 400);
    $sql = "SELECT Codigo, `User`, Cod_Empleado, State, cod_tienda, email, email_active, id_group_user FROM `Usuarios` WHERE Codigo = {$codigo} LIMIT 1";
    $res = mysqli_query($conn, $sql);
    if ($res === false) resp(['error' => 'DB error', 'detail' => mysqli_error($conn), 'sql' => $sql], 500);
    $row = mysqli_fetch_assoc($res);
    if ($row) resp(['success' => true, 'data' => $row]);
    resp(['error' => 'Usuario no encontrado'], 404);
    break;

  case 'update_user':
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') resp(['error' => 'POST requerido'], 405);
    $codigo = isset($_POST['codigo']) ? intval($_POST['codigo']) : 0;
    if ($codigo <= 0) resp(['error' => 'Código inválido'], 400);

    $user = isset($_POST['user']) ? mysqli_real_escape_string($conn, trim($_POST['user'])) : '';
    $cod_empleado = isset($_POST['cod_empleado']) ? mysqli_real_escape_string($conn, trim($_POST['cod_empleado'])) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $state = isset($_POST['state']) ? mysqli_real_escape_string($conn, trim($_POST['state'])) : '1';
    $cod_tienda = isset($_POST['cod_tienda']) ? intval($_POST['cod_tienda']) : 0;
    $email = isset($_POST['email']) ? mysqli_real_escape_string($conn, trim($_POST['email'])) : '';
    $email_active = isset($_POST['email_active']) ? 1 : 0;
    $id_group = isset($_POST['id_group']) ? intval($_POST['id_group']) : 0;

    if ($user === '' || $id_group <= 0 || $cod_tienda <= 0 || $email === '') {
      resp(['error' => 'Faltan campos requeridos'], 400);
    }

    // Validar existencia grupo/tienda
    $q = "SELECT 1 FROM `grupo_user` WHERE codigo = {$id_group} LIMIT 1";
    $r = mysqli_query($conn, $q);
    if ($r === false) resp(['error' => 'DB error', 'detail' => mysqli_error($conn), 'sql' => $q], 500);
    if (mysqli_num_rows($r) === 0) resp(['error' => 'Grupo no existe'], 400);

    $q = "SELECT 1 FROM `tienda` WHERE cod_tienda = {$cod_tienda} LIMIT 1";
    $r = mysqli_query($conn, $q);
    if ($r === false) resp(['error' => 'DB error', 'detail' => mysqli_error($conn), 'sql' => $q], 500);
    if (mysqli_num_rows($r) === 0) resp(['error' => 'Tienda no existe'], 400);

    // Construir SQL según si cambia password
    if ($password !== '') {
      $hash = password_hash($password, PASSWORD_DEFAULT);
      $hash_q = mysqli_real_escape_string($conn, $hash);
      $sql = "UPDATE `Usuarios` SET `User` = '{$user}', `Cod_Empleado` = '{$cod_empleado}', `Password` = '{$hash_q}', `State` = '{$state}', `cod_tienda` = {$cod_tienda}, `email` = '{$email}', `email_active` = {$email_active}, `id_group_user` = {$id_group} WHERE Codigo = {$codigo}";
    } else {
      $sql = "UPDATE `Usuarios` SET `User` = '{$user}', `Cod_Empleado` = '{$cod_empleado}', `State` = '{$state}', `cod_tienda` = {$cod_tienda}, `email` = '{$email}', `email_active` = {$email_active}, `id_group_user` = {$id_group} WHERE Codigo = {$codigo}";
    }

    if (mysqli_query($conn, $sql)) {
      resp(['success' => true, 'message' => 'Usuario actualizado']);
    } else {
      resp(['error' => 'Update failed', 'detail' => mysqli_error($conn), 'sql' => $sql], 500);
    }
    break;

  case 'delete_user':
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') resp(['error' => 'POST requerido'], 405);
    $codigo = isset($_POST['codigo']) ? intval($_POST['codigo']) : 0;
    if ($codigo <= 0) resp(['error' => 'Código inválido'], 400);
    $sql = "DELETE FROM `Usuarios` WHERE Codigo = {$codigo}";
    if (mysqli_query($conn, $sql)) {
      resp(['success' => true, 'message' => 'Usuario eliminado']);
    } else {
      resp(['error' => 'Delete failed', 'detail' => mysqli_error($conn), 'sql' => $sql], 500);
    }
    break;

  default:
    resp(['error' => 'Acción no válida'], 400);
    break;
}
?>