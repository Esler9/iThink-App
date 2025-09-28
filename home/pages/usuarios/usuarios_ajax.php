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
    if ($res = mysqli_query($conn, $sql)) {
      while ($r = mysqli_fetch_assoc($res)) {
        $out[] = ['codigo' => $r['codigo'], 'nombre' => $r['nombre_grupo']];
      }
      resp($out);
    } else {
      resp(['error' => mysqli_error($conn)], 500);
    }
    break;

  case 'list_tiendas':
    $out = [];
    $sql = "SELECT cod_tienda, nombre FROM tienda ORDER BY nombre ASC";
    if ($res = mysqli_query($conn, $sql)) {
      while ($r = mysqli_fetch_assoc($res)) {
        $out[] = ['cod_tienda' => $r['cod_tienda'], 'nombre' => $r['nombre']];
      }
      resp($out);
    } else {
      resp(['error' => mysqli_error($conn)], 500);
    }
    break;

  case 'create_user':
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') resp(['error' => 'POST requerido'], 405);
    $user = isset($_POST['user']) ? trim($_POST['user']) : '';
    $cod_empleado = isset($_POST['cod_empleado']) ? trim($_POST['cod_empleado']) : null;
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $state = isset($_POST['state']) ? trim($_POST['state']) : '1';
    $cod_tienda = isset($_POST['cod_tienda']) ? trim($_POST['cod_tienda']) : null;
    $email = isset($_POST['email']) ? trim($_POST['email']) : null;
    $email_active = isset($_POST['email_active']) ? 1 : 0;
    $id_group = isset($_POST['id_group']) ? intval($_POST['id_group']) : null;

    if ($user === '' || $password === '' || $id_group === null || $cod_tienda === null || $email === null) {
      resp(['error' => 'Faltan campos requeridos'], 400);
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = mysqli_prepare($conn, "INSERT INTO `Usuarios` (`User`, `Cod_Empleado`, `Password`, `State`, `cod_tienda`, `email`, `email_active`, `id_group_user`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) resp(['error' => mysqli_error($conn)], 500);

    // tipos: 6 strings + email_active int + id_group int
    mysqli_stmt_bind_param($stmt, "ssssssii", $user, $cod_empleado, $hash, $state, $cod_tienda, $email, $email_active, $id_group);
    if (mysqli_stmt_execute($stmt)) {
      resp(['success' => true, 'message' => 'Usuario creado', 'id' => mysqli_insert_id($conn)]);
    } else {
      resp(['error' => mysqli_stmt_error($stmt)], 500);
    }
    break;

  case 'get_user':
    $codigo = isset($_GET['codigo']) ? intval($_GET['codigo']) : 0;
    if ($codigo <= 0) resp(['error' => 'Código inválido'], 400);
    $sql = "SELECT Codigo, `User`, Cod_Empleado, State, cod_tienda, email, email_active, id_group_user FROM `Usuarios` WHERE Codigo = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) resp(['error' => mysqli_error($conn)], 500);
    mysqli_stmt_bind_param($stmt, "i", $codigo);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($res === false) resp(['error' => mysqli_error($conn)], 500);
    $row = mysqli_fetch_assoc($res);
    if ($row) {
      resp(['success' => true, 'data' => $row]);
    } else {
      resp(['error' => 'Usuario no encontrado'], 404);
    }
    break;

  case 'update_user':
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') resp(['error' => 'POST requerido'], 405);
    $codigo = isset($_POST['codigo']) ? intval($_POST['codigo']) : 0;
    if ($codigo <= 0) resp(['error' => 'Código inválido'], 400);
    $user = isset($_POST['user']) ? trim($_POST['user']) : '';
    $cod_empleado = isset($_POST['cod_empleado']) ? trim($_POST['cod_empleado']) : null;
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $state = isset($_POST['state']) ? trim($_POST['state']) : '1';
    $cod_tienda = isset($_POST['cod_tienda']) ? trim($_POST['cod_tienda']) : null;
    $email = isset($_POST['email']) ? trim($_POST['email']) : null;
    $email_active = isset($_POST['email_active']) ? 1 : 0;
    $id_group = isset($_POST['id_group']) ? intval($_POST['id_group']) : null;

    if ($user === '' || $id_group === null || $cod_tienda === null || $email === null) {
      resp(['error' => 'Faltan campos requeridos'], 400);
    }

    if ($password !== '') {
      $hash = password_hash($password, PASSWORD_DEFAULT);
      $sql = "UPDATE `Usuarios` SET `User` = ?, `Cod_Empleado` = ?, `Password` = ?, `State` = ?, `cod_tienda` = ?, `email` = ?, `email_active` = ?, `id_group_user` = ? WHERE Codigo = ?";
      $stmt = mysqli_prepare($conn, $sql);
      if (!$stmt) resp(['error' => mysqli_error($conn)], 500);
      mysqli_stmt_bind_param($stmt, "ssssssiii", $user, $cod_empleado, $hash, $state, $cod_tienda, $email, $email_active, $id_group, $codigo);
    } else {
      $sql = "UPDATE `Usuarios` SET `User` = ?, `Cod_Empleado` = ?, `State` = ?, `cod_tienda` = ?, `email` = ?, `email_active` = ?, `id_group_user` = ? WHERE Codigo = ?";
      $stmt = mysqli_prepare($conn, $sql);
      if (!$stmt) resp(['error' => mysqli_error($conn)], 500);
      // tipos: 5 strings + 3 ints (email_active, id_group, codigo)
      mysqli_stmt_bind_param($stmt, "sssssiii", $user, $cod_empleado, $state, $cod_tienda, $email, $email_active, $id_group, $codigo);
    }

    if (mysqli_stmt_execute($stmt)) {
      resp(['success' => true, 'message' => 'Usuario actualizado']);
    } else {
      resp(['error' => mysqli_stmt_error($stmt)], 500);
    }
    break;

  case 'delete_user':
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') resp(['error' => 'POST requerido'], 405);
    $codigo = isset($_POST['codigo']) ? intval($_POST['codigo']) : 0;
    if ($codigo <= 0) resp(['error' => 'Código inválido'], 400);
    $stmt = mysqli_prepare($conn, "DELETE FROM `Usuarios` WHERE Codigo = ?");
    if (!$stmt) resp(['error' => mysqli_error($conn)], 500);
    mysqli_stmt_bind_param($stmt, "i", $codigo);
    if (mysqli_stmt_execute($stmt)) {
      resp(['success' => true, 'message' => 'Usuario eliminado']);
    } else {
      resp(['error' => mysqli_stmt_error($stmt)], 500);
    }
    break;

  default:
    resp(['error' => 'Acción no válida'], 400);
    break;
}
?>