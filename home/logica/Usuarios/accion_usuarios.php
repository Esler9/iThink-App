<?php
session_start();

if (!isset($_SESSION["username"])) {
    header('Location:../../login.php');
    exit();
}

include("../../../conexion.php");
include('../correo.php');
include('../funcion_logica.php');
include("../diseño_correos.php");

function reenviarCorreo($listaCorreos, $identificador, $body) {
    foreach ($listaCorreos as $destinatario) {
        enviar($destinatario, $identificador, $body);
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método no permitido');
}

$accion = isset($_GET['accion']) ? (int)$_GET['accion'] : 0;
$codigo = isset($_POST['codigo']) ? trim($_POST['codigo']) : (isset($_GET['codigo']) ? trim($_GET['codigo']) : '');

$c_user = isset($_POST['c_user']) ? trim($_POST['c_user']) : '';
$c_password = isset($_POST['c_password']) ? trim($_POST['c_password']) : '';
$c_cod_empleado = isset($_POST['c_cod_empleado']) ? trim($_POST['c_cod_empleado']) : '';
$c_email = isset($_POST['c_email']) ? trim($_POST['c_email']) : '';
$c_id_group = isset($_POST['c_id_group']) ? trim($_POST['c_id_group']) : '';
$c_cod_tienda = isset($_POST['c_cod_tienda']) ? trim($_POST['c_cod_tienda']) : '';
$c_state = isset($_POST['c_state']) ? trim($_POST['c_state']) : '';
$c_email_active = isset($_POST['c_email_active']) ? 1 : 0;

$errors = [];

// --- BLOQUE DE CONFIRMACIÓN (muestra los datos POST y espera OK) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['confirmado'])) {
    // Serializa los datos POST para mostrarlos al usuario
    $post_pretty = json_encode($_POST, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    // Escapa la cadena JSON para incluirla como literal JS
    $post_js_literal = json_encode($post_pretty);

    // Construye y devuelve una página HTML que pide confirmación al usuario.
    // Si acepta, se reenvía el POST original añadiendo confirmado=1.
    echo '<!doctype html><html><head><meta charset="utf-8"><title>Confirmar datos POST</title></head><body>';
    echo '<script>';
    echo "var dataStr = {$post_js_literal};";
    echo "var ok = confirm('Datos recibidos:\\n\\n' + dataStr + '\\n\\nPulse Aceptar para continuar o Cancelar para volver.');";
    echo "if (ok) {";
    // crea el formulario con los mismos campos POST
    echo "  var f = document.createElement('form');";
    echo "  f.method = 'post';";
    // conserva el parámetro GET 'accion' en la URL
    $action_url = htmlspecialchars($_SERVER['PHP_SELF'] . '?accion=' . $accion, ENT_QUOTES, 'UTF-8');
    echo "  f.action = " . json_encode($action_url) . ";";
    // añade campos POST originales
    foreach ($_POST as $k => $v) {
        $k_js = json_encode($k);
        $v_js = json_encode($v);
        echo "  var inp = document.createElement('input'); inp.type='hidden'; inp.name={$k_js}; inp.value={$v_js}; f.appendChild(inp);";
    }
    // añade el flag de confirmado para no volver a mostrar el diálogo
    echo "  var conf = document.createElement('input'); conf.type='hidden'; conf.name='confirmado'; conf.value='1'; f.appendChild(conf);";
    echo "  document.body.appendChild(f); f.submit();";
    echo "} else { window.history.back(); }";
    echo "</script></body></html>";
    exit();
}
// --- FIN BLOQUE DE CONFIRMACIÓN ---

if ($c_user === '') $errors[] = 'Usuario requerido';
if ($accion === 0 && $c_password === '') $errors[] = 'Contraseña requerida';
if ($c_email !== '' && !filter_var($c_email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email inválido';

if (!empty($errors)) {
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'errors' => $errors]);
    exit;
}

switch ($accion) {
    case 0:
        if (empty($_POST['c_user']) || empty($_POST['c_password'])) {
            header("Location:../../pages/usuarios/listado_users.php?alert=DataMissing");
            exit();
        }

        $user = mysqli_real_escape_string($conn, $_POST['c_user']);
        $password = $_POST['c_password'];
        $cod_empleado = mysqli_real_escape_string($conn, $_POST['c_cod_empleado'] ?? "");
        $email = mysqli_real_escape_string($conn, $_POST['c_email'] ?? "");
        $id_group = mysqli_real_escape_string($conn, $_POST['c_id_group'] ?? "");
        $cod_tienda = mysqli_real_escape_string($conn, $_POST['c_cod_tienda'] ?? "");
        $state = isset($_POST['c_state']) ? 1 : 0;
        $email_active = isset($_POST['c_email_active']) ? 1 : 0;

        $sql_chk = "SELECT COUNT(*) AS contar FROM Usuarios WHERE `User` = '$user'";
        $consulta = mysqli_query($conn, $sql_chk);
        $existe = mysqli_fetch_array($consulta);

        if ($existe["contar"] == 0) {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO Usuarios (`User`,`Cod_Empleado`,`email`,`email_active`,`State`,`cod_tienda`,`id_group_user`,`Password`,`date_update`,`date`)
                    VALUES ('$user', '$cod_empleado', '$email', '$email_active', '$state', '$cod_tienda', '$id_group', '$password_hash', '$date', '$date')";
            mysqli_query($conn, $sql);
            header("Location:../../pages/usuarios/listado_users.php?alert=0&user=".$user);
            exit();
        } else {
            header("Location:../../pages/usuarios/crear.php?alert=33&user=".$user);
            exit();
        }
        break;

    case 1:
        if (empty($codigo)) {
            header("Location:../../pages/usuarios/listado_users.php?alert=DataMissing");
            exit();
        }

        $user = mysqli_real_escape_string($conn, $_POST['user'] ?? "");
        $password = $_POST['password'] ?? "";
        $cod_empleado = mysqli_real_escape_string($conn, $_POST['cod_empleado'] ?? "");
        $email = mysqli_real_escape_string($conn, $_POST['email'] ?? "");
        $id_group = mysqli_real_escape_string($conn, $_POST['id_group'] ?? "");
        $cod_tienda = mysqli_real_escape_string($conn, $_POST['cod_tienda'] ?? "");
        $state = isset($_POST['state']) ? 1 : 0;
        $email_active = isset($_POST['email_active']) ? 1 : 0;

        if ($user !== "") {
            $sql_chk = "SELECT Codigo FROM Usuarios WHERE `User` = '$user' AND Codigo <> '$codigo'";
            $consulta = mysqli_query($conn, $sql_chk);
            $r = mysqli_fetch_array($consulta);
            if ($r) {
                header("Location:../../pages/usuarios/editar.php?alert=DuplicateUser&codigo=".$codigo);
                exit();
            }
        }

        if ($password !== "") {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE Usuarios SET `User` = '$user', `Cod_Empleado` =cod_empleado', `email` = '$email', `email_active` = '$email_active',
                    `State` = '$state', `cod_tienda` = '$cod_tienda', `id_group_user` = '$id_group', `Password` = '$password_hash', date_update = '$date'
                    WHERE Codigo = '$codigo'";
        } else {
            $sql = "UPDATE Usuarios SET `User` = '$user', `Cod_Empleado` = '$cod_empleado', `email` = '$email', `email_active` = '$email_active',
                    `State` = '$state', `cod_tienda` = '$cod_tienda', `id_group_user` = '$id_group', date_update = '$date'
                    WHERE Codigo = '$codigo'";
        }

        mysqli_query($conn, $sql);
        header("Location:../../pages/usuarios/listado_users.php?alert=1&codigo=".$codigo);
        exit();
        break;

    case 2:
        $codigo_del = isset($_POST['codigo']) ? $_POST['codigo'] : (isset($_GET['codigo']) ? $_GET['codigo'] : "");
        if (empty($codigo_del)) {
            header("Location:../../pages/usuarios/listado_users.php?alert=DataMissing");
            exit();
        }
        $sql = "DELETE FROM Usuarios WHERE Codigo = '$codigo_del'";
        mysqli_query($conn, $sql);
        header("Location:../../pages/usuarios/listado_users.php?alert=2&codigo=".$codigo_del);
        exit();
        break;

    case 3:
        $codigo_toggle = isset($_POST['codigo']) ? $_POST['codigo'] : (isset($_GET['codigo']) ? $_GET['codigo'] : "");
        if (empty($codigo_toggle)) {
            header("Location:../../pages/usuarios/listado_users.php?alert=DataMissing");
            exit();
        }
        $email_active = isset($_POST['email_active']) ? 1 : 0;
        $sql = "UPDATE Usuarios SET email_active = '$email_active', date_update = '$date' WHERE Codigo = '$codigo_toggle'";
        mysqli_query($conn, $sql);
        header("Location:../../pages/usuarios/listado_users.php?alert=3&codigo=".$codigo_toggle);
        exit();
        break;

    default:
        echo "Error: Acción no reconocida.";
        break;
}
?>