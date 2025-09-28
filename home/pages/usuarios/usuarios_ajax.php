<?php
session_start();
header('Content-Type: application/json');
include("../../../conexion.php");

$action = isset($_POST['action']) ? $_POST['action'] : '';
$response = [];

// Test directo si no hay action (para debug)
if (!$action && isset($_GET['test'])) {
    // Test tiendas
    $sql_tiendas = "SELECT cod_tienda, nombre FROM tienda ORDER BY nombre ASC";
    $res_tiendas = mysqli_query($conn, $sql_tiendas);
    $tiendas_test = [];
    if ($res_tiendas) {
        while ($row = mysqli_fetch_assoc($res_tiendas)) {
            $tiendas_test[] = $row;
        }
    }
    
    // Test grupos
    $sql_grupos = "SELECT codigo, nombre_grupo FROM grupo_user ORDER BY nombre_grupo ASC";
    $res_grupos = mysqli_query($conn, $sql_grupos);
    $grupos_test = [];
    if ($res_grupos) {
        while ($row = mysqli_fetch_assoc($res_grupos)) {
            $grupos_test[] = $row;
        }
    }
    
    echo json_encode([
        'test' => 'OK',
        'conexion' => mysqli_ping($conn) ? 'OK' : 'FAIL',
        'tiendas_count' => count($tiendas_test),
        'tiendas' => $tiendas_test,
        'grupos_count' => count($grupos_test),
        'grupos' => $grupos_test,
        'post_data' => $_POST,
        'get_data' => $_GET
    ]);
    exit;
}

if ($action === 'get_tiendas') {
    $sql = "SELECT cod_tienda, nombre FROM tienda ORDER BY nombre ASC";
    $res = mysqli_query($conn, $sql);
    $tiendas = [];
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $tiendas[] = $row;
        }
    } else {
        // Error en la consulta
        $tiendas = ['error' => mysqli_error($conn), 'sql' => $sql];
    }
    echo json_encode($tiendas);
    exit;
}

if ($action === 'get_grupos') {
    $sql = "SELECT codigo, nombre_grupo FROM grupo_user ORDER BY nombre_grupo ASC";
    $res = mysqli_query($conn, $sql);
    $grupos = [];
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $grupos[] = $row;
        }
    } else {
        // Error en la consulta
        $grupos = ['error' => mysqli_error($conn), 'sql' => $sql];
    }
    echo json_encode($grupos);
    exit;
}

// Crear usuario
if ($action === 'create_user') {
    $user = trim($_POST['user'] ?? '');
    $cod_empleado = trim($_POST['cod_empleado'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $id_group = trim($_POST['id_group'] ?? '');
    $cod_tienda = trim($_POST['cod_tienda'] ?? '');
    $state = trim($_POST['state'] ?? '1');
    if ($user && $password) {
        $pass_hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO Usuarios (`User`, Cod_Empleado, email, Password, id_group_user, cod_tienda, State) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ssssisi', $user, $cod_empleado, $email, $pass_hash, $id_group, $cod_tienda, $state);
        $ok = mysqli_stmt_execute($stmt);
        if ($ok) {
            $response = ['success' => true, 'message' => 'Usuario creado correctamente'];
        } else {
            $response = ['success' => false, 'message' => 'Error al crear usuario: '.mysqli_error($conn)];
        }
    } else {
        $response = ['success' => false, 'message' => 'Usuario y contraseña son obligatorios'];
    }
    echo json_encode($response);
    exit;
}

// Editar usuario
if ($action === 'edit_user') {
    $codigo = trim($_POST['codigo'] ?? '');
    $user = trim($_POST['user'] ?? '');
    $cod_empleado = trim($_POST['cod_empleado'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $email_active = isset($_POST['email_active']) ? 1 : 0;
    $id_group = trim($_POST['id_group'] ?? '');
    $cod_tienda = trim($_POST['cod_tienda'] ?? '');
    $state = trim($_POST['state'] ?? '1');
    $password = trim($_POST['password'] ?? '');
    if ($codigo && $user) {
        $sql = "UPDATE Usuarios SET `User`=?, Cod_Empleado=?, email=?, email_active=?, id_group_user=?, cod_tienda=?, State=?";
        $params = [$user, $cod_empleado, $email, $email_active, $id_group, $cod_tienda, $state];
        $types = 'sssissi';
        if ($password) {
            $pass_hash = password_hash($password, PASSWORD_DEFAULT);
            $sql .= ", Password=?";
            $params[] = $pass_hash;
            $types .= 's';
        }
        $sql .= " WHERE Codigo=?";
        $params[] = $codigo;
        $types .= 'i';
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        $ok = mysqli_stmt_execute($stmt);
        if ($ok) {
            $response = ['success' => true, 'message' => 'Usuario actualizado'];
        } else {
            $response = ['success' => false, 'message' => 'Error al actualizar: '.mysqli_error($conn)];
        }
    } else {
        $response = ['success' => false, 'message' => 'Datos insuficientes'];
    }
    echo json_encode($response);
    exit;
}

// Eliminar usuario
if ($action === 'delete_user') {
    $codigo = trim($_POST['codigo'] ?? '');
    if ($codigo) {
        $sql = "DELETE FROM Usuarios WHERE Codigo=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $codigo);
        $ok = mysqli_stmt_execute($stmt);
        if ($ok) {
            $response = ['success' => true, 'message' => 'Usuario eliminado'];
        } else {
            $response = ['success' => false, 'message' => 'Error al eliminar: '.mysqli_error($conn)];
        }
    } else {
        $response = ['success' => false, 'message' => 'Código no recibido'];
    }
    echo json_encode($response);
    exit;
}

// Activar/desactivar email
if ($action === 'toggle_email') {
    $codigo = trim($_POST['Codigo'] ?? '');
    $email_active = isset($_POST['email_active']) ? (int)$_POST['email_active'] : 0;
    if ($codigo) {
        $sql = "UPDATE Usuarios SET email_active=? WHERE Codigo=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ii', $email_active, $codigo);
        $ok = mysqli_stmt_execute($stmt);
        if ($ok) {
            $response = ['success' => true, 'message' => 'Estado de email actualizado'];
        } else {
            $response = ['success' => false, 'message' => 'Error: '.mysqli_error($conn)];
        }
    } else {
        $response = ['success' => false, 'message' => 'Código no recibido'];
    }
    echo json_encode($response);
    exit;
}

// Si no coincide ninguna acción
$response = ['success' => false, 'message' => 'Acción no válida'];
echo json_encode($response);
