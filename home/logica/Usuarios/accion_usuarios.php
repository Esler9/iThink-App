<?php
session_start();
if (!isset($_SESSION["username"])) {
    header('location:login.php');
    exit();
}

ini_set('display_errors', 1);
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

include("../../../conexion.php");

// pequeña función helper para depuración segura
function debug_and_exit($msg, $data = []) {
    error_log("accion_usuarios.php DEBUG: " . $msg . " | " . print_r($data, true));
    echo "<pre>";
    echo htmlspecialchars($msg) . "\n\n";
    echo htmlspecialchars(print_r($data, true));
    echo "</pre>";
    exit();
}

$accion = isset($_GET['accion']) ? $_GET['accion'] : '';
$date = date('Y-m-d H:i:s');

switch ($accion) {

    case "0": // Crear usuario
        $user = trim($_POST['c_user'] ?? '');
        $password = $_POST['c_password'] ?? '';
        $cod_empleado = trim($_POST['c_cod_empleado'] ?? '');
        $email = trim($_POST['c_email'] ?? '');
        $id_group_user = trim($_POST['c_id_group'] ?? '');
        $cod_tienda = trim($_POST['c_cod_tienda'] ?? '');
        $state = isset($_POST['c_state']) ? (int)$_POST['c_state'] : 0;
        $email_active = isset($_POST['c_email_active']) ? 1 : 0;

        if ($user === '' || $password === '') {
            debug_and_exit("Validation error: DataMissing", ['POST' => $_POST]);
        }

        // verificar usuario único (tabla y columna exactas según tu esquema)
        $stmt = $conn->prepare("SELECT COUNT(*) FROM `Usuarios` WHERE `User` = ?");
        if (!$stmt) debug_and_exit("Prepare failed (select count)", ['error' => $conn->error]);
        $stmt->bind_param("s", $user);
        $stmt->execute();
        $stmt->bind_result($cnt);
        $stmt->fetch();
        $stmt->close();
        if ($cnt > 0) {
            debug_and_exit("UserExists", ['user' => $user]);
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        // Inserción sin columnas de fecha (según tu esquema)
        $stmt = $conn->prepare("INSERT INTO `Usuarios` (`User`, `Password`, `Cod_Empleado`, `email`, `id_group_user`, `cod_tienda`, `State`, `email_active`) VALUES (?,?,?,?,?,?,?,?)");
        if (!$stmt) debug_and_exit("Prepare failed (insert)", ['error' => $conn->error]);
        // tipos: 6 strings (user,hash,cod_empleado,email,id_group_user,cod_tienda), 2 ints (state,email_active)
        $stmt->bind_param("ssssssii", $user, $hash, $cod_empleado, $email, $id_group_user, $cod_tienda, $state, $email_active);
        $ok = $stmt->execute();
        $last_id = $conn->insert_id;
        $stmt->close();

        if ($ok) {
            debug_and_exit("Insert successful", ['Codigo_insertado' => $last_id]);
        } else {
            debug_and_exit("Insert failed", ['error' => $conn->error]);
        }
        break;

    case "1": // Editar usuario
        $codigo = isset($_POST['codigo']) ? (int)$_POST['codigo'] : 0;
        if ($codigo === 0) {
            debug_and_exit("NoId for update", ['POST' => $_POST]);
        }
        $user = trim($_POST['user'] ?? '');
        $password = $_POST['password'] ?? '';
        $cod_empleado = trim($_POST['cod_empleado'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $id_group_user = trim($_POST['id_group'] ?? '');
        $cod_tienda = trim($_POST['cod_tienda'] ?? '');
        $state = isset($_POST['state']) ? (int)$_POST['state'] : 0;
        $email_active = isset($_POST['email_active']) ? 1 : 0;

        if ($user === '') {
            debug_and_exit("Validation error: DataMissing on update", ['POST' => $_POST]);
        }

        // verificar usuario único (excepto el propio)
        $stmt = $conn->prepare("SELECT COUNT(*) FROM `Usuarios` WHERE `User` = ? AND `Codigo` <> ?");
        if (!$stmt) debug_and_exit("Prepare failed (select count update)", ['error' => $conn->error]);
        $stmt->bind_param("si", $user, $codigo);
        $stmt->execute();
        $stmt->bind_result($cnt);
        $stmt->fetch();
        $stmt->close();
        if ($cnt > 0) {
            debug_and_exit("UserExists (update)", ['user' => $user, 'Codigo' => $codigo]);
        }

        if (!empty($password)) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            // UPDATE sin date_update
            $stmt = $conn->prepare("UPDATE `Usuarios` SET `User`=?, `Password`=?, `Cod_Empleado`=?, `email`=?, `id_group_user`=?, `cod_tienda`=?, `State`=?, `email_active`=? WHERE `Codigo`=?");
            if (!$stmt) debug_and_exit("Prepare failed (update pw)", ['error' => $conn->error]);
            // tipos: 6 strings, 3 ints (state,email_active,codigo)
            $stmt->bind_param("ssssssiii", $user, $hash, $cod_empleado, $email, $id_group_user, $cod_tienda, $state, $email_active, $codigo);
        } else {
            $stmt = $conn->prepare("UPDATE `Usuarios` SET `User`=?, `Cod_Empleado`=?, `email`=?, `id_group_user`=?, `cod_tienda`=?, `State`=?, `email_active`=? WHERE `Codigo`=?");
            if (!$stmt) debug_and_exit("Prepare failed (update no pw)", ['error' => $conn->error]);
            // tipos: 5 strings, 3 ints (state,email_active,codigo)
            $stmt->bind_param("sssssiii", $user, $cod_empleado, $email, $id_group_user, $cod_tienda, $state, $email_active, $codigo);
        }
        $ok = $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();

        if ($ok) {
            debug_and_exit("Update successful", ['Codigo' => $codigo, 'affected_rows' => $affected]);
        } else {
            debug_and_exit("Update failed", ['error' => $conn->error]);
        }
        break;

    case "2": // Eliminar usuario
        $codigo = isset($_POST['codigo']) ? (int)$_POST['codigo'] : 0;
        if ($codigo === 0) {
            debug_and_exit("NoId for delete", ['POST' => $_POST]);
        }
        $stmt = $conn->prepare("DELETE FROM `Usuarios` WHERE `Codigo` = ?");
        if (!$stmt) debug_and_exit("Prepare failed (delete)", ['error' => $conn->error]);
        $stmt->bind_param("i", $codigo);
        $ok = $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();

        if ($ok) {
            debug_and_exit("Delete successful", ['Codigo' => $codigo, 'affected_rows' => $affected]);
        } else {
            debug_and_exit("Delete failed", ['error' => $conn->error]);
        }
        break;

    default:
        $debugData = [
            'accion' => $accion,
            'GET' => $_GET,
            'POST_keys' => array_keys($_POST),
            'SESSION_keys' => array_keys($_SESSION),
            'conn_present' => isset($conn) ? true : false,
            'mysqli_error' => isset($conn) ? mysqli_error($conn) : 'no $conn'
        ];
        debug_and_exit("Acción no reconocida o falta parámetro 'accion'. Datos de depuración:", $debugData);
        break;
}
?>