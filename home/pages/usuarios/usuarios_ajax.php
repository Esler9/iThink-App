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
      header('Location: listado_users.php');
      exit;
    }

    // Tomar y escapar valores (estilo accion.php)
    $user = isset($_POST['user']) ? mysqli_real_escape_string($conn, trim($_POST['user'])) : '';
    $cod_empleado = isset($_POST['cod_empleado']) ? mysqli_real_escape_string($conn, trim($_POST['cod_empleado'])) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $state = isset($_POST['state']) ? mysqli_real_escape_string($conn, trim($_POST['state'])) : '1';
    $cod_tienda = isset($_POST['cod_tienda']) ? intval($_POST['cod_tienda']) : 0;
    $email = isset($_POST['email']) ? mysqli_real_escape_string($conn, trim($_POST['email'])) : '';
    $email_active = isset($_POST['email_active']) ? 1 : 0;
    $id_group = isset($_POST['id_group']) ? intval($_POST['id_group']) : 0;

    // Validaciones
    if ($user === '' || $password === '' || $id_group <= 0 || $cod_tienda <= 0 || $email === '') {
      $_SESSION['error_usuario'] = "Faltan campos requeridos.";
      header('Location: listado_users.php');
      exit;
    }

    // Verificar usuario único
    $q = "SELECT 1 FROM `Usuarios` WHERE `User` = '{$user}' LIMIT 1";
    $r = mysqli_query($conn, $q);
    if ($r === false) {
      $_SESSION['error_usuario'] = "Error DB: " . mysqli_error($conn);
      header('Location: listado_users.php');
      exit;
    }
    if (mysqli_num_rows($r) > 0) {
      $_SESSION['error_usuario'] = "El usuario ya existe.";
      header('Location: listado_users.php');
      exit;
    }

    // Verificar grupo y tienda existen
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

    // Hash y escape
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $hash_q = mysqli_real_escape_string($conn, $hash);

    // Insert (estilo accion.php)
    $sql = "INSERT INTO `Usuarios` (`User`, `Cod_Empleado`, `Password`, `State`, `cod_tienda`, `email`, `email_active`, `id_group_user`)
            VALUES ('{$user}', '{$cod_empleado}', '{$hash_q}', '{$state}', {$cod_tienda}, '{$email}', {$email_active}, {$id_group})";

    if (mysqli_query($conn, $sql)) {
      $_SESSION['ok_usuario'] = "Usuario creado correctamente.";
      header('Location: listado_users.php');
      exit;
    } else {
      $_SESSION['error_usuario'] = "Error al crear usuario: " . mysqli_error($conn);
      header('Location: listado_users.php');
      exit;
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
?>