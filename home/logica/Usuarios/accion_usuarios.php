<?php
session_start();

include_once __DIR__ . '/../../../conexion.php';
include_once __DIR__ . '/../ac_permiso.php';

if (!isset($_SESSION["username"]) || !isset($_SESSION['cod_user'])) {
    header('Location: /home/login.php');
    exit();
}
$User = $_SESSION["username"];
$cod_user = $_SESSION['cod_user'];

function redirect_back($msg_key = null, $msg = null, $ok = true) {
    if ($msg_key !== null) {
        $_SESSION[$msg_key] = $msg;
        $_SESSION[$msg_key . '_ok'] = $ok ? 1 : 0;
    }
    header('Location: /home/pages/usuarios/listado_users.php');
    exit();
}

$raw = (isset($_REQUEST['action']) ? trim($_REQUEST['action']) : (isset($_REQUEST['accion']) ? trim($_REQUEST['accion']) : ''));
if ($raw === '') redirect_back();

if (is_numeric($raw)) {
    $map = (int)$raw;
    $action = ($map === 0) ? 'create' : (($map === 1) ? 'update' : (($map === 2) ? 'delete' : (string)$raw));
} else {
    $action = strtolower($raw);
}

if (empty($conn) || mysqli_connect_errno()) {
    error_log("accion_usuarios: conexión inválida");
    redirect_back('error_usuario', 'Problema de conexión con la base de datos', false);
}

function getp() {
    foreach (func_get_args() as $k) {
        if (isset($_POST[$k])) return $_POST[$k];
    }
    return null;
}
function clean($v) { return trim((string)$v); }

switch ($action) {
    case 'create':
        if (!Tiene_permiso($permisos_user, 'crear-usuarios')) { redirect_back(null, null, false); }

        $user = clean(getp('c_user', 'user'));
        $cod_empleado = clean(getp('c_cod_empleado', 'cod_empleado'));
        $email = filter_var(getp('c_email', 'email'), FILTER_SANITIZE_EMAIL);
        $email_active = (getp('c_email_active', 'email_active') ? 1 : 0);
        $id_group = getp('c_id_group', 'id_group');
        $cod_tienda = getp('c_cod_tienda', 'cod_tienda');
        $state = getp('c_state', 'state');
        $password = getp('c_password', 'password');

        if ($user === '' || $password === '') {
            redirect_back('error_usuario', 'Usuario y contraseña son obligatorios', false);
        }

        $pass_hash = password_hash($password, PASSWORD_DEFAULT);
        $id_group = ($id_group === '' || is_null($id_group)) ? null : intval($id_group);
        $cod_tienda = ($cod_tienda === '' || is_null($cod_tienda)) ? null : intval($cod_tienda);
        $state = ($state === '' || is_null($state)) ? 1 : (intval($state) ? 1 : 0);

        $sql = "INSERT INTO `Usuarios` (`User`, `Cod_Empleado`, `email`, `email_active`, `id_group_user`, `cod_tienda`, `State`, `Password`)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) { error_log('create prepare: ' . mysqli_error($conn)); redirect_back('error_usuario', 'Error al crear usuario', false); }
        mysqli_stmt_bind_param($stmt, 'sssiisss',
            $user,
            $cod_empleado,
            $email,
            $email_active,
            $id_group,
            $cod_tienda,
            $state,
            $pass_hash
        );
        if (!mysqli_stmt_execute($stmt)) {
            error_log('create exec: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            redirect_back('error_usuario', 'No fue posible crear el usuario', false);
        }
        mysqli_stmt_close($stmt);
        redirect_back('ok_usuario', 'Usuario creado correctamente', true);
        break;

    case 'update':
        if (!Tiene_permiso($permisos_user, 'editar-usuarios')) { redirect_back(null, null, false); }

        $codigo = intval(getp('e_codigo', 'codigo', 'd_codigo'));
        if ($codigo <= 0) redirect_back('error_usuario', 'Código de usuario inválido', false);

        $user = clean(getp('e_user', 'user'));
        $cod_empleado = clean(getp('e_cod_empleado', 'cod_empleado'));
        $email = filter_var(getp('e_email', 'email'), FILTER_SANITIZE_EMAIL);
        $email_active = (getp('e_email_active', 'email_active') ? 1 : 0);
        $id_group = getp('e_id_group', 'id_group');
        $cod_tienda = getp('e_cod_tienda', 'cod_tienda');
        $state = getp('e_state', 'state');
        $password = getp('e_password', 'password');

        $id_group = ($id_group === '' || is_null($id_group)) ? null : intval($id_group);
        $cod_tienda = ($cod_tienda === '' || is_null($cod_tienda)) ? null : intval($cod_tienda);
        $state = ($state === '' || is_null($state)) ? 0 : (intval($state) ? 1 : 0);

        if ($password !== '' && !is_null($password)) {
            $pass_hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE `Usuarios` SET `User` = ?, `Cod_Empleado` = ?, `email` = ?, `email_active` = ?, `id_group_user` = ?, `cod_tienda` = ?, `State` = ?, `Password` = ? WHERE `Codigo` = ?";
            $stmt = mysqli_prepare($conn, $sql);
            if (!$stmt) { error_log('update prepare: ' . mysqli_error($conn)); redirect_back('error_usuario', 'Error al actualizar usuario', false); }
            mysqli_stmt_bind_param($stmt, 'sssiisssi',
                $user, $cod_empleado, $email, $email_active, $id_group, $cod_tienda, $state, $pass_hash, $codigo
            );
        } else {
            $sql = "UPDATE `Usuarios` SET `User` = ?, `Cod_Empleado` = ?, `email` = ?, `email_active` = ?, `id_group_user` = ?, `cod_tienda` = ?, `State` = ? WHERE `Codigo` = ?";
            $stmt = mysqli_prepare($conn, $sql);
            if (!$stmt) { error_log('update prepare: ' . mysqli_error($conn)); redirect_back('error_usuario', 'Error al actualizar usuario', false); }
            mysqli_stmt_bind_param($stmt, 'sssiissi',
                $user, $cod_empleado, $email, $email_active, $id_group, $cod_tienda, $state, $codigo
            );
        }

        if (!mysqli_stmt_execute($stmt)) {
            error_log('update exec: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            redirect_back('error_usuario', 'No fue posible actualizar el usuario', false);
        }
        mysqli_stmt_close($stmt);
        redirect_back('ok_usuario', 'Usuario actualizado correctamente', true);
        break;

    case 'delete':
        if (!Tiene_permiso($permisos_user, 'eliminar-usuarios')) { redirect_back(null, null, false); }

        $codigo = intval(getp('d_codigo', 'codigo', 'e_codigo'));
        if ($codigo <= 0) redirect_back('error_usuario', 'Código de usuario inválido', false);

        $sql = "DELETE FROM `Usuarios` WHERE `Codigo` = ?";
        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) { error_log('delete prepare: ' . mysqli_error($conn)); redirect_back('error_usuario', 'Error al eliminar usuario', false); }
        mysqli_stmt_bind_param($stmt, 'i', $codigo);
        if (!mysqli_stmt_execute($stmt)) {
            error_log('delete exec: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            redirect_back('error_usuario', 'No fue posible eliminar el usuario', false);
        }
        mysqli_stmt_close($stmt);
        redirect_back('ok_usuario', 'Usuario eliminado correctamente', true);
        break;

    case 'toggle_email':
        if (!Tiene_permiso($permisos_user, 'editar-usuarios')) { redirect_back(null, null, false); }

        $codigo = intval(getp('codigo'));
        $value = intval(getp('value'));
        if ($codigo <= 0) redirect_back('error_usuario', 'Código inválido', false);

        $sql = "UPDATE `Usuarios` SET `email_active` = ? WHERE `Codigo` = ?";
        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) { error_log('toggle prepare: ' . mysqli_error($conn)); redirect_back('error_usuario', 'Error al cambiar configuración', false); }
        mysqli_stmt_bind_param($stmt, 'ii', $value, $codigo);
        if (!mysqli_stmt_execute($stmt)) {
            error_log('toggle exec: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            redirect_back('error_usuario', 'No fue posible actualizar', false);
        }
        mysqli_stmt_close($stmt);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => true]);
        exit();

    default:
        redirect_back();
}
?>