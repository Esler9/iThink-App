<?php
session_start();
include_once "../../../conexion.php";

// CSRF
if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
  echo json_encode(['ok'=>false, 'msg'=>'CSRF inválido.']);
  exit;
}

$action = $_POST['form_action'] ?? '';
$flash_message = '';
$flash_success = false;

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
    $flash_message = 'Usuario y contraseña son obligatorios.';
  } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $flash_message = 'Email inválido.';
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
        $flash_message = 'Usuario creado correctamente.';
        $flash_success = true;
      } else {
        $flash_message = 'No se pudo crear el usuario. ' . ($err ?: '');
      }
    } else {
      $flash_message = 'Error al preparar INSERT.';
    }
  }
}

if ($action === 'edit_user') {
  // ...igual que tu lógica actual...
}

if ($action === 'delete_user') {
  // ...igual que tu lógica actual...
}

echo json_encode(['ok'=>$flash_success, 'msg'=>$flash_message]);
exit;
?>