<?php
session_start();
if (!isset($_SESSION["username"])) {
    header('location:../../login.php');
    exit();
}

include("../../../conexion.php");
include('../correo.php');          // mismos archivos pero en ../ (logica)
include('../funcion_logica.php');
include("../diseño_correos.php");

// (Opcional) función para reenviar correos si en algún caso la lógica lo requiere
function reenviarCorreo($listaCorreos, $identificador, $body) {
    foreach ($listaCorreos as $destinatario) {
        enviar($destinatario, $identificador, $body);
    }
}

$accion = $_GET['accion'];
// Para usuarios usaremos 'codigo' como identificador (paralelo a 'imei' en accion.php)
$codigo = isset($_GET['codigo']) ? $_GET['codigo'] : "";
$desc   = isset($_POST['desc']) ? $_POST['desc'] : "";
$date   = date('Y-m-d H:i:s');

// Datos comunes que podrían ser útiles para notificaciones (estructura similar a accion.php)
$datosCommon = [
    'codigo'        => $codigo,
    'observaciones' => $desc
];

switch ($accion) {

    case "0":
        // Creación de usuario
        // Campos esperados en el form: c_user, c_password, c_cod_empleado, c_email, c_id_group, c_cod_tienda, c_state, c_email_active
        if (empty($_POST['c_user']) || empty($_POST['c_password'])) {
            header("location:../../pages/usuarios/crear.php?alert=DataMissing");
            exit();
        }

        $user        = mysqli_real_escape_string($conn, $_POST['c_user']);
        $password    = $_POST['c_password'];
        $cod_empleado = mysqli_real_escape_string($conn, $_POST['c_cod_empleado'] ?? "");
        $email       = mysqli_real_escape_string($conn, $_POST['c_email'] ?? "");
        $id_group    = mysqli_real_escape_string($conn, $_POST['c_id_group'] ?? "");
        $cod_tienda  = mysqli_real_escape_string($conn, $_POST['c_cod_tienda'] ?? "");
        $state       = isset($_POST['c_state']) ? 1 : 0;
        $email_active= isset($_POST['c_email_active']) ? 1 : 0;

        // Evitar duplicados: comprobar si ya existe el user
        $sql_chk = "SELECT COUNT(*) AS contar FROM Usuarios WHERE `User` = '$user'";
        $consulta = mysqli_query($conn, $sql_chk);
        $existe = mysqli_fetch_array($consulta);

        if ($existe["contar"] == 0) {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO Usuarios (`User`,`Cod_Empleado`,`email`,`email_active`,`State`,`cod_tienda`,`id_group_user`,`Password`,`date_update`,`date`)
                    VALUES ('$user', '$cod_empleado', '$email', '$email_active', '$state', '$cod_tienda', '$id_group', '$password_hash', '$date', '$date')";
            mysqli_query($conn, $sql);

            // Si hay notificaciones por correo, se puede usar $datos para generar cuerpo
            // $datos = [ ... ]; $body = correo_enviar("usuario_creado", $datos); reenviarCorreo($correos, $user, $body);

            header("location:../../pages/usuarios/listado_users.php?alert=0&user=".$user);
        } else {
            header("location:../../pages/usuarios/crear.php?alert=33&user=".$user);
        }
        break;

    case "1":
        // Edición de usuario
        // Campos esperados: user, password (opcional), cod_empleado, email, id_group, cod_tienda, state, email_active
        if (empty($codigo)) {
            header("location:../../pages/usuarios/listado_users.php?alert=DataMissing");
            exit();
        }

        $user        = mysqli_real_escape_string($conn, $_POST['user'] ?? "");
        $password    = $_POST['password'] ?? "";
        $cod_empleado = mysqli_real_escape_string($conn, $_POST['cod_empleado'] ?? "");
        $email       = mysqli_real_escape_string($conn, $_POST['email'] ?? "");
        $id_group    = mysqli_real_escape_string($conn, $_POST['id_group'] ?? "");
        $cod_tienda  = mysqli_real_escape_string($conn, $_POST['cod_tienda'] ?? "");
        $state       = isset($_POST['state']) ? 1 : 0;
        $email_active= isset($_POST['email_active']) ? 1 : 0;

        // Evitar duplicar nombre de usuario en otro registro
        if ($user !== "") {
            $sql_chk = "SELECT Codigo FROM Usuarios WHERE `User` = '$user' AND Codigo <> '$codigo'";
            $consulta = mysqli_query($conn, $sql_chk);
            $r = mysqli_fetch_array($consulta);
            if ($r) {
                header("location:../../pages/usuarios/editar.php?alert=DuplicateUser&codigo=".$codigo);
                exit();
            }
        }

        if ($password !== "") {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE Usuarios SET `User` = '$user', `Cod_Empleado` = '$cod_empleado', `email` = '$email', `email_active` = '$email_active',
                    `State` = '$state', `cod_tienda` = '$cod_tienda', `id_group_user` = '$id_group', `Password` = '$password_hash', date_update = '$date'
                    WHERE Codigo = '$codigo'";
        } else {
            $sql = "UPDATE Usuarios SET `User` = '$user', `Cod_Empleado` = '$cod_empleado', `email` = '$email', `email_active` = '$email_active',
                    `State` = '$state', `cod_tienda` = '$cod_tienda', `id_group_user` = '$id_group', date_update = '$date'
                    WHERE Codigo = '$codigo'";
        }

        mysqli_query($conn, $sql);
        header("location:../../pages/usuarios/listado_users.php?alert=1&codigo=".$codigo);
        break;

    case "2":
        // Eliminación de usuario
        // Se acepta codigo por GET o POST (paralelo a accion.php)
        $codigo_del = isset($_POST['codigo']) ? $_POST['codigo'] : (isset($_GET['codigo']) ? $_GET['codigo'] : "");
        if (empty($codigo_del)) {
            header("location:../../pages/usuarios/listado_users.php?alert=DataMissing");
            exit();
        }
        $sql = "DELETE FROM Usuarios WHERE Codigo = '$codigo_del'";
        mysqli_query($conn, $sql);
        header("location:../../pages/usuarios/listado_users.php?alert=2&codigo=".$codigo_del);
        break;

    case "3":
        // Toggle email_active (similar a otros casos: usa checkbox)
        $codigo_toggle = isset($_POST['codigo']) ? $_POST['codigo'] : (isset($_GET['codigo']) ? $_GET['codigo'] : "");
        if (empty($codigo_toggle)) {
            header("location:../../pages/usuarios/listado_users.php?alert=DataMissing");
            exit();
        }
        // checkbox enviado => activar, sino desactivar
        $email_active = isset($_POST['email_active']) ? 1 : 0;
        $sql = "UPDATE Usuarios SET email_active = '$email_active', date_update = '$date' WHERE Codigo = '$codigo_toggle'";
        mysqli_query($conn, $sql);
        header("location:../../pages/usuarios/listado_users.php?alert=3&codigo=".$codigo_toggle);
        break;

    default:
        echo "Error: Acción no reconocida.";
        break;
}
?>