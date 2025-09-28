<?php
session_start();

if (!isset($_SESSION["username"])) {
    header('HTTP/1.1 401 Unauthorized');
    echo 'No autorizado';
    exit();
}

include("../../../conexion.php");


// Fecha para campos date/date_update
$date = date('Y-m-d H:i:s');

// Función auxiliar simple para mostrar resultado en HTML (evita redirecciones automáticas)
function respond($title, $message = '', $data = []) {
    header('Content-Type: text/html; charset=utf-8');
    $data_pretty = $data ? json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '';
    echo '<!doctype html><html><head><meta charset="utf-8"><title>' . htmlspecialchars($title) . '</title></head><body>';
    echo '<h1>' . htmlspecialchars($title) . '</h1>';
    if ($message !== '') echo '<p>' . nl2br(htmlspecialchars($message)) . '</p>';
    if ($data_pretty !== '') {
        echo '<h2>Datos</h2><pre>' . htmlspecialchars($data_pretty, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</pre>';
    }
    echo '<p><button onclick="history.back()">Volver</button></p>';
    echo '</body></html>';
    exit;
}

// Solo aceptar POST para operaciones CRUD
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond('Método no permitido', 'Este endpoint acepta únicamente peticiones POST.');
}

// Acción (viene por GET ?accion=N)
$accion = isset($_GET['accion']) ? (int)$_GET['accion'] : 0;

// Código (puede venir por POST o GET)
$codigo = isset($_POST['codigo']) ? trim($_POST['codigo']) : (isset($_GET['codigo']) ? trim($_GET['codigo']) : '');

// Recolectar y normalizar campos comunes (usar nombre según el formulario)
$c_user = isset($_POST['c_user']) ? trim($_POST['c_user']) : (isset($_POST['user']) ? trim($_POST['user']) : '');
$c_password = isset($_POST['c_password']) ? trim($_POST['c_password']) : (isset($_POST['password']) ? trim($_POST['password']) : '');
$c_cod_empleado = isset($_POST['c_cod_empleado']) ? trim($_POST['c_cod_empleado']) : (isset($_POST['cod_empleado']) ? trim($_POST['cod_empleado']) : '');
$c_email = isset($_POST['c_email']) ? trim($_POST['c_email']) : (isset($_POST['email']) ? trim($_POST['email']) : '');
$c_id_group = isset($_POST['c_id_group']) ? trim($_POST['c_id_group']) : (isset($_POST['id_group']) ? trim($_POST['id_group']) : '');
$c_cod_tienda = isset($_POST['c_cod_tienda']) ? trim($_POST['c_cod_tienda']) : (isset($_POST['cod_tienda']) ? trim($_POST['cod_tienda']) : '');
$c_state = isset($_POST['c_state']) ? trim($_POST['c_state']) : (isset($_POST['state']) ? trim($_POST['state']) : '');
$c_email_active = isset($_POST['c_email_active']) || isset($_POST['email_active']) ? 1 : 0;

$errors = [];

// Validaciones básicas según acción
if ($accion === 0) { // crear
    if ($c_user === '') $errors[] = 'Usuario requerido';
    if ($c_password === '') $errors[] = 'Contraseña requerida';
}
if ($c_email !== '' && !filter_var($c_email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email inválido';

if (!empty($errors)) {
    respond('Errores de validación', implode("\n", $errors), $_POST);
}

// Conexión: $conn (incluida desde conexion.php). Usamos escapes simples para claridad.
switch ($accion) {
    case 0: // Crear usuario
        $user = mysqli_real_escape_string($conn, $c_user);
        $password = $c_password;
        $cod_empleado = mysqli_real_escape_string($conn, $c_cod_empleado);
        $email = mysqli_real_escape_string($conn, $c_email);
        $id_group = mysqli_real_escape_string($conn, $c_id_group);
        $cod_tienda = mysqli_real_escape_string($conn, $c_cod_tienda);
        $state = ($c_state !== '') ? 1 : 0;
        $email_active = $c_email_active ? 1 : 0;

        // Verificar existencia
        $sql_chk = "SELECT COUNT(*) AS contar FROM Usuarios WHERE `User` = '$user'";
        $res = mysqli_query($conn, $sql_chk);
        $row = $res ? mysqli_fetch_assoc($res) : null;
        if ($row && (int)$row['contar'] > 0) {
            respond('Usuario duplicado', "El usuario '$user' ya existe.", $_POST);
        }

        // Insertar
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $password_hash_esc = mysqli_real_escape_string($conn, $password_hash);
        $sql = "INSERT INTO Usuarios (`User`,`Cod_Empleado`,`email`,`email_active`,`State`,`cod_tienda`,`id_group_user`,`Password`,`date_update`,`date`)
                VALUES ('$user', '$cod_empleado', '$email', '$email_active', '$state', '$cod_tienda', '$id_group', '$password_hash_esc', '$date', '$date')";
        $ok = mysqli_query($conn, $sql);
        if ($ok) {
            respond('Usuario creado', "Usuario '$user' creado correctamente.", ['user' => $user]);
        } else {
            respond('Error BD', mysqli_error($conn), $_POST);
        }
        break;

    case 1: // Editar usuario
        if ($codigo === '') {
            respond('Código faltante', 'No se recibió el código del usuario.', $_POST);
        }

        $user = mysqli_real_escape_string($conn, $c_user);
        $password = $c_password;
        $cod_empleado = mysqli_real_escape_string($conn, $c_cod_empleado);
        $email = mysqli_real_escape_string($conn, $c_email);
        $id_group = mysqli_real_escape_string($conn, $c_id_group);
        $cod_tienda = mysqli_real_escape_string($conn, $c_cod_tienda);
        $state = ($c_state !== '') ? 1 : 0;
        $email_active = $c_email_active ? 1 : 0;
        $codigo_esc = mysqli_real_escape_string($conn, $codigo);

        // Comprobar usuario duplicado (si cambia el nombre)
        if ($user !== '') {
            $sql_chk = "SELECT Codigo FROM Usuarios WHERE `User` = '$user' AND Codigo <> '$codigo_esc' LIMIT 1";
            $res = mysqli_query($conn, $sql_chk);
            if ($res && mysqli_num_rows($res) > 0) {
                respond('Usuario duplicado', "El usuario '$user' ya está en uso por otro registro.", $_POST);
            }
        }

        // Preparar UPDATE
        if ($password !== '') {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $password_hash_esc = mysqli_real_escape_string($conn, $password_hash);
            $sql = "UPDATE Usuarios SET `User` = '$user', `Cod_Empleado` = '$cod_empleado', `email` = '$email', `email_active` = '$email_active',
                    `State` = '$state', `cod_tienda` = '$cod_tienda', `id_group_user` = '$id_group', `Password` = '$password_hash_esc', date_update = '$date'
                    WHERE Codigo = '$codigo_esc'";
        } else {
            $sql = "UPDATE Usuarios SET `User` = '$user', `Cod_Empleado` = '$cod_empleado', `email` = '$email', `email_active` = '$email_active',
                    `State` = '$state', `cod_tienda` = '$cod_tienda', `id_group_user` = '$id_group', date_update = '$date'
                    WHERE Codigo = '$codigo_esc'";
        }

        $ok = mysqli_query($conn, $sql);
        if ($ok) {
            respond('Usuario actualizado', "El usuario con código $codigo ha sido actualizado.", ['codigo' => $codigo]);
        } else {
            respond('Error BD', mysqli_error($conn), $_POST);
        }
        break;

    case 2: // Eliminar usuario
        $codigo_del = isset($_POST['codigo']) ? trim($_POST['codigo']) : '';
        if ($codigo_del === '') {
            respond('Código faltante', 'No se recibió el código para eliminar.', $_POST);
        }
        $codigo_del_esc = mysqli_real_escape_string($conn, $codigo_del);
        $sql = "DELETE FROM Usuarios WHERE Codigo = '$codigo_del_esc'";
        $ok = mysqli_query($conn, $sql);
        if ($ok) {
            respond('Usuario eliminado', "Usuario con código $codigo_del eliminado.", ['codigo' => $codigo_del]);
        } else {
            respond('Error BD', mysqli_error($conn), $_POST);
        }
        break;

    case 3: // Toggle email_active (u otra acción)
        $codigo_toggle = isset($_POST['codigo']) ? trim($_POST['codigo']) : '';
        if ($codigo_toggle === '') {
            respond('Código faltante', 'No se recibió el código para la acción.', $_POST);
        }
        $email_active = isset($_POST['email_active']) ? 1 : 0;
        $codigo_toggle_esc = mysqli_real_escape_string($conn, $codigo_toggle);
        $sql = "UPDATE Usuarios SET email_active = '$email_active', date_update = '$date' WHERE Codigo = '$codigo_toggle_esc'";
        $ok = mysqli_query($conn, $sql);
        if ($ok) {
            respond('Estado actualizado', "email_active = $email_active para código $codigo_toggle.", ['codigo' => $codigo_toggle]);
        } else {
            respond('Error BD', mysqli_error($conn), $_POST);
        }
        break;

    default:
        respond('Acción desconocida', "Acción '$accion' no reconocida.", $_POST);
        break;
}
?>