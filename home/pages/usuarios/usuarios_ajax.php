<?php
session_start();
include_once "../../../conexion.php";

// CSRF simple
if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
  echo json_encode(['ok'=>false, 'msg'=>'CSRF inválido.']);
  exit;
}

$action = $_POST['form_action'] ?? '';
$response = ['ok'=>false, 'msg'=>'Acción no válida.'];

if ($action === 'create_user') {
  $user        = trim($_POST['User'] ?? '');
  $codEmp      = trim($_POST['Cod_Empleado'] ?? '');
  $email       = trim($_POST['email'] ?? '');
  $emailActive = isset($_POST['email_active']) ? (int)$_POST['email_active'] : 0;
  $idGroup     = ($_POST['id_group_user'] === '' ? null : (int)$_POST['id_group_user']);
  $codTienda   = trim($_POST['cod_tienda'] ?? '');
  $state       = isset($_POST['State']) ? (int)$_POST['State'] : 1;
  $password    = $_POST['Password'] ?? '';

  if ($user === '' || $password === '') {
    $response['msg'] = 'Usuario y contraseña son obligatorios.';
  } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['msg'] = 'Email inválido.';
  } else {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO usuarios (user, cod_empleado, email, email_active, id_group_user, cod_tienda, state, password)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    if ($stmt = mysqli_prepare($conn, $sql)) {
      mysqli_stmt_bind_param($stmt, "sssiiiss",
        $user, $codEmp, $email, $emailActive, $idGroup, $codTienda, $state, $hash
      );
      $ok = @mysqli_stmt_execute($stmt);
      $err = mysqli_error($conn);
      mysqli_stmt_close($stmt);

      if ($ok) {
        $response = ['ok'=>true, 'msg'=>'Usuario creado correctamente.'];
      } else {
        $response['msg'] = 'No se pudo crear el usuario. ' . ($err ?: '');
      }
    } else {
      $response['msg'] = 'Error al preparar INSERT.';
    }
  }
}

if ($action === 'edit_user') {
  $codigo = (int)($_POST['Codigo'] ?? 0);
  $user = trim($_POST['User'] ?? '');
  if ($codigo <= 0 || $user === '') {
    $response['msg'] = 'Datos inválidos.';
  } else {
    $sql = "UPDATE usuarios SET user=? WHERE codigo=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $user, $codigo);
    if (mysqli_stmt_execute($stmt)) {
      $response = ['ok'=>true, 'msg'=>'Usuario actualizado.'];
    } else {
      $response['msg'] = 'Error al actualizar: ' . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
  }
}

if ($action === 'delete_user') {
  $codigo = (int)($_POST['Codigo'] ?? 0);
  if ($codigo <= 0) {
    $response['msg'] = 'Código inválido.';
  } else {
    $sql = "DELETE FROM usuarios WHERE codigo=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $codigo);
    if (mysqli_stmt_execute($stmt)) {
      $response = ['ok'=>true, 'msg'=>'Usuario eliminado.'];
    } else {
      $response['msg'] = 'Error al eliminar: ' . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
  }
}

echo json_encode($response);
exit;
?>
