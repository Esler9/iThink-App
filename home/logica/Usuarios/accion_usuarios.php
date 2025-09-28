<?php
session_start();
if (!isset($_SESSION["username"])) {
    header('location:login.php');
    exit();
}

ini_set('display_errors', 1);
error_reporting(E_ALL);
// Mostrar errores de mysqli como excepciones (útil para debug temporal)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

include("../../../conexion.php");

// pequeña función helper para depuración segura
function debug_and_exit($msg, $data = []) {
    // registrar en el log de errores
    error_log("accion_usuarios.php DEBUG: " . $msg . " | " . print_r($data, true));
    // mostrar en pantalla para depuración rápida (quitar en producción)
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
        $id_group = trim($_POST['c_id_group'] ?? '');
        $cod_tienda = trim($_POST['c_cod_tienda'] ?? '');
        $state = trim($_POST['c_state'] ?? '');
        $email_active = isset($_POST['c_email_active']) ? 1 : 0;

        if ($user === '' || $password === '') {
            debug_and_exit("Validation error: DataMissing", ['POST' => $_POST]);
        }

        // verificar usuario único
        $stmt = $conn->prepare("SELECT COUNT(*) FROM usuarios WHERE user = ?");
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
        $stmt = $conn->prepare("INSERT INTO usuarios (user, password, cod_empleado, email, id_group, cod_tienda, state, email_active, date_created) VALUES (?,?,?,?,?,?,?,?,?)");
        if (!$stmt) debug_and_exit("Prepare failed (insert)", ['error' => $conn->error]);
        $stmt->bind_param("sssssssis", $user, $hash, $cod_empleado, $email, $id_group, $cod_tienda, $state, $email_active, $date);
        $ok = $stmt->execute();
        $last_id = $conn->insert_id;
        $stmt->close();

        if ($ok) {
            debug_and_exit("Insert successful", ['codigo_insertado' => $last_id]);
        } else {
            debug_and_exit("Insert failed", ['error' => $conn->error]);
        }
        break;

    case "1": // Editar usuario
        $codigo = $_POST['codigo'] ?? '';
        if ($codigo === '') {
            debug_and_exit("NoId for update", ['POST' => $_POST]);
        }
        $user = trim($_POST['user'] ?? '');
        $password = $_POST['password'] ?? '';
        $cod_empleado = trim($_POST['cod_empleado'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $id_group = trim($_POST['id_group'] ?? '');
        $cod_tienda = trim($_POST['cod_tienda'] ?? '');
        $state = trim($_POST['state'] ?? '');
        $email_active = isset($_POST['email_active']) ? 1 : 0;

        if ($user === '') {
            debug_and_exit("Validation error: DataMissing on update", ['POST' => $_POST]);
        }

        // verificar usuario único (excepto el propio)
        $stmt = $conn->prepare("SELECT COUNT(*) FROM usuarios WHERE user = ? AND codigo <> ?");
        if (!$stmt) debug_and_exit("Prepare failed (select count update)", ['error' => $conn->error]);
        $stmt->bind_param("si", $user, $codigo);
        $stmt->execute();
        $stmt->bind_result($cnt);
        $stmt->fetch();
        $stmt->close();
        if ($cnt > 0) {
            debug_and_exit("UserExists (update)", ['user' => $user, 'codigo' => $codigo]);
        }

        if (!empty($password)) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE usuarios SET user=?, password=?, cod_empleado=?, email=?, id_group=?, cod_tienda=?, state=?, email_active=?, date_update=? WHERE codigo=?");
            if (!$stmt) debug_and_exit("Prepare failed (update pw)", ['error' => $conn->error]);
            $stmt->bind_param("ssssssissi", $user, $hash, $cod_empleado, $email, $id_group, $cod_tienda, $state, $email_active, $date, $codigo);
        } else {
            $stmt = $conn->prepare("UPDATE usuarios SET user=?, cod_empleado=?, email=?, id_group=?, cod_tienda=?, state=?, email_active=?, date_update=? WHERE codigo=?");
            if (!$stmt) debug_and_exit("Prepare failed (update no pw)", ['error' => $conn->error]);
            $stmt->bind_param("sssssssi", $user, $cod_empleado, $email, $id_group, $cod_tienda, $state, $email_active, $date, $codigo);
        }
        $ok = $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();

        if ($ok) {
            debug_and_exit("Update successful", ['codigo' => $codigo, 'affected_rows' => $affected]);
        } else {
            debug_and_exit("Update failed", ['error' => $conn->error]);
        }
        break;

    case "2": // Eliminar usuario
        $codigo = $_POST['codigo'] ?? '';
        if ($codigo === '') {
            debug_and_exit("NoId for delete", ['POST' => $_POST]);
        }
        $stmt = $conn->prepare("DELETE FROM usuarios WHERE codigo = ?");
        if (!$stmt) debug_and_exit("Prepare failed (delete)", ['error' => $conn->error]);
        $stmt->bind_param("i", $codigo);
        $ok = $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();

        if ($ok) {
            debug_and_exit("Delete successful", ['codigo' => $codigo, 'affected_rows' => $affected]);
        } else {
            debug_and_exit("Delete failed", ['error' => $conn->error]);
        }
        break;

    default:
        // Mensaje de depuración breve y registro para investigar 500/acción desconocida
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