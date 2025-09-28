<?php
session_start();

// Conexión y permisos (ajusta rutas si es necesario)
include_once __DIR__ . '/../../../conexion.php';        // conexion.php en raíz del proyecto
include_once __DIR__ . '/../ac_permiso.php';            // ac_permiso.php en /home/logica/

// Comprobar sesión básica
if (!isset($_SESSION["username"]) || !isset($_SESSION['cod_user'])) {
    header('Location: /home/login.php');
    exit();
}

$User = $_SESSION["username"];
$cod_user = $_SESSION['cod_user'];

// Helper para redirigir con mensaje en sesión
function redirect_back($msg_key = null, $msg = null, $ok = true) {
    if ($msg_key !== null) {
        $_SESSION[$msg_key] = $msg;
        $_SESSION[$msg_key . '_ok'] = $ok ? 1 : 0;
    }
    header('Location: /home/pages/usuarios/listado_users.php');
    exit();
}

// Sólo usuarios con permiso de gestión pueden ejecutar acciones mutantes
$action = isset($_REQUEST['action']) ? trim($_REQUEST['action']) : '';

if ($action === '') {
    // Si no hay acción, volver al listado
    redirect_back();
}

// Validar conexión
if (empty($conn) || mysqli_connect_errno()) {
    error_log("accion_usuarios: conexión inválida");
    redirect_back('error_usuario', 'Problema de conexión con la base de datos', false);
}

// Función de limpieza básica
function clean($v) {
    return trim($v);
}

// CREATE
if ($action === 'create') {
    if (!Tiene_permiso($permisos_user, 'crear-usuarios')) {
        redirect_back(null, null, false);
    }

    $user = isset($_POST['c_user']) ? clean($_POST['c_user']) : '';
    $cod_empleado = isset($_POST['c_cod_empleado']) ? clean($_POST['c_cod_empleado']) : '';
    $email = isset($_POST['c_email']) ? filter_var($_POST['c_email'], FILTER_SANITIZE_EMAIL) : '';
    $email_active = isset($_POST['c_email_active']) ? 1 : 0;
    $id_group = isset($_POST['c_id_group']) && $_POST['c_id_group'] !== '' ? intval($_POST['c_id_group']) : null;
    $cod_tienda = isset($_POST['c_cod_tienda']) && $_POST['c_cod_tienda'] !== '' ? intval($_POST['c_cod_tienda']) : null;
    $state = isset($_POST['c_state']) ? (intval($_POST['c_state']) ? 1 : 0) : 1;
    $password = isset($_POST['c_password']) ? $_POST['c_password'] : '';

    if ($user === '' || $password === '') {
        redirect_back('error_usuario', 'Usuario y contraseña son obligatorios', false);
    }

    $pass_hash = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO `Usuarios` (`User`, `Cod_Empleado`, `email`, `email_active`, `id_group_user`, `cod_tienda`, `State`, `Password`)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        error_log('accion_usuarios create prepare: ' . mysqli_error($conn));
        redirect_back('error_usuario', 'Error al crear usuario', false);
    }
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
    $ok = mysqli_stmt_execute($stmt);
    if (!$ok) {
        error_log('accion_usuarios create exec: ' . mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt);
        redirect_back('error_usuario', 'No fue posible crear el usuario', false);
    }
    mysqli_stmt_close($stmt);
    redirect_back('ok_usuario', 'Usuario creado correctamente', true);
    // END CREATE
}

// UPDATE
if ($action === 'update') {
    if (!Tiene_permiso($permisos_user, 'editar-usuarios')) {
        redirect_back(null, null, false);
    }

    $codigo = isset($_POST['e_codigo']) ? intval($_POST['e_codigo']) : 0;
    if ($codigo <= 0) {
        redirect_back('error_usuario', 'Código de usuario inválido', false);
    }

    $user = isset($_POST['e_user']) ? clean($_POST['e_user']) : '';
    $cod_empleado = isset($_POST['e_cod_empleado']) ? clean($_POST['e_cod_empleado']) : '';
    $email = isset($_POST['e_email']) ? filter_var($_POST['e_email'], FILTER_SANITIZE_EMAIL) : '';
    $email_active = isset($_POST['e_email_active']) ? 1 : 0;
    $id_group = isset($_POST['e_id_group']) && $_POST['e_id_group'] !== '' ? intval($_POST['e_id_group']) : null;
    $cod_tienda = isset($_POST['e_cod_tienda']) && $_POST['e_cod_tienda'] !== '' ? intval($_POST['e_cod_tienda']) : null;
    $state = isset($_POST['e_state']) ? (intval($_POST['e_state']) ? 1 : 0) : 0;
    $password = isset($_POST['e_password']) ? $_POST['e_password'] : '';

    // Construir SQL dinámico si no se cambia la contraseña
    if ($password !== '') {
        $pass_hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE `Usuarios` SET `User` = ?, `Cod_Empleado` = ?, `email` = ?, `email_active` = ?, `id_group_user` = ?, `cod_tienda` = ?, `State` = ?, `Password` = ? WHERE `Codigo` = ?";
        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) {
            error_log('accion_usuarios update prepare: ' . mysqli_error($conn));
            redirect_back('error_usuario', 'Error al actualizar usuario', false);
        }
        mysqli_stmt_bind_param($stmt, 'sssiisssi',
            $user,
            $cod_empleado,
            $email,
            $email_active,
            $id_group,
            $cod_tienda,
            $state,
            $pass_hash,
            $codigo
        );
    } else {
        $sql = "UPDATE `Usuarios` SET `User` = ?, `Cod_Empleado` = ?, `email` = ?, `email_active` = ?, `id_group_user` = ?, `cod_tienda` = ?, `State` = ? WHERE `Codigo` = ?";
        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) {
            error_log('accion_usuarios update prepare: ' . mysqli_error($conn));
            redirect_back('error_usuario', 'Error al actualizar usuario', false);
        }
        mysqli_stmt_bind_param($stmt, 'sssiissi',
            $user,
            $cod_empleado,
            $email,
            $email_active,
            $id_group,
            $cod_tienda,
            $state,
            $codigo
        );
    }

    $ok = mysqli_stmt_execute($stmt);
    if (!$ok) {
        error_log('accion_usuarios update exec: ' . mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt);
        redirect_back('error_usuario', 'No fue posible actualizar el usuario', false);
    }
    mysqli_stmt_close($stmt);
    redirect_back('ok_usuario', 'Usuario actualizado correctamente', true);
    // END UPDATE
}

// DELETE
if ($action === 'delete') {
    if (!Tiene_permiso($permisos_user, 'eliminar-usuarios')) {
        redirect_back(null, null, false);
    }

    $codigo = isset($_POST['d_codigo']) ? intval($_POST['d_codigo']) : 0;
    if ($codigo <= 0) {
        redirect_back('error_usuario', 'Código de usuario inválido', false);
    }

    $sql = "DELETE FROM `Usuarios` WHERE `Codigo` = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        error_log('accion_usuarios delete prepare: ' . mysqli_error($conn));
        redirect_back('error_usuario', 'Error al eliminar usuario', false);
    }
    mysqli_stmt_bind_param($stmt, 'i', $codigo);
    $ok = mysqli_stmt_execute($stmt);
    if (!$ok) {
        error_log('accion_usuarios delete exec: ' . mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt);
        redirect_back('error_usuario', 'No fue posible eliminar el usuario', false);
    }
    mysqli_stmt_close($stmt);
    redirect_back('ok_usuario', 'Usuario eliminado correctamente', true);
    // END DELETE
}

// TOGGLE EMAIL (ejemplo para checkbox)
if ($action === 'toggle_email') {
    if (!Tiene_permiso($permisos_user, 'editar-usuarios')) {
        redirect_back(null, null, false);
    }
    $codigo = isset($_POST['codigo']) ? intval($_POST['codigo']) : 0;
    $value = isset($_POST['value']) ? (intval($_POST['value']) ? 1 : 0) : 0;
    if ($codigo <= 0) {
        redirect_back('error_usuario', 'Código inválido', false);
    }
    $sql = "UPDATE `Usuarios` SET `email_active` = ? WHERE `Codigo` = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        error_log('accion_usuarios toggle prepare: ' . mysqli_error($conn));
        redirect_back('error_usuario', 'Error al cambiar configuración', false);
    }
    mysqli_stmt_bind_param($stmt, 'ii', $value, $codigo);
    $ok = mysqli_stmt_execute($stmt);
    if (!$ok) {
        error_log('accion_usuarios toggle exec: ' . mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt);
        redirect_back('error_usuario', 'No fue posible actualizar', false);
    }
    mysqli_stmt_close($stmt);
    // Responder para peticiones AJAX: devolver JSON mínimo
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => true]);
    exit();
}

// Si la acción no coincide, redirigir
redirect_back();
?>