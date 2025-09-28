<?php
session_start();
if (!isset($_SESSION["username"])) {
    header('location:login.php');
    exit();
}

ini_set('display_errors', 1);
error_reporting(E_ALL);

include("../../../conexion.php");

if (!isset($conn) || !$conn) {
    header("Location:../../pages/usuarios/listado_users.php?alert=DBConn");
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
            header("Location:../../pages/usuarios/listado_users.php?alert=DataMissing");
            exit();
        }

        $stmt = $conn->prepare("SELECT COUNT(*) FROM `Usuarios` WHERE `User` = ?");
        if (!$stmt) { header("Location:../../pages/usuarios/listado_users.php?alert=DBErr"); exit(); }
        $stmt->bind_param("s", $user);
        $stmt->execute();
        $stmt->bind_result($cnt);
        $stmt->fetch();
        $stmt->close();

        if ($cnt > 0) {
            header("Location:../../pages/usuarios/listado_users.php?alert=UserExists");
            exit();
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO `Usuarios` (`User`, `Password`, `Cod_Empleado`, `email`, `id_group_user`, `cod_tienda`, `State`, `email_active`) VALUES (?,?,?,?,?,?,?,?)");
        if (!$stmt) { header("Location:../../pages/usuarios/listado_users.php?alert=DBErr"); exit(); }
        $stmt->bind_param("ssssssii", $user, $hash, $cod_empleado, $email, $id_group_user, $cod_tienda, $state, $email_active);
        $ok = $stmt->execute();
        $stmt->close();

        if ($ok) {
            header("Location:../../pages/usuarios/listado_users.php?alert=0");
            exit();
        } else {
            header("Location:../../pages/usuarios/listado_users.php?alert=InsertError");
            exit();
        }
        break;

    case "1": // Editar usuario
        $codigo = isset($_POST['codigo']) ? (int)$_POST['codigo'] : 0;
        if ($codigo === 0) {
            header("Location:../../pages/usuarios/listado_users.php?alert=NoId");
            exit();
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
            header("Location:../../pages/usuarios/listado_users.php?alert=DataMissing");
            exit();
        }

        $stmt = $conn->prepare("SELECT COUNT(*) FROM `Usuarios` WHERE `User` = ? AND `Codigo` <> ?");
        if (!$stmt) { header("Location:../../pages/usuarios/listado_users.php?alert=DBErr"); exit(); }
        $stmt->bind_param("si", $user, $codigo);
        $stmt->execute();
        $stmt->bind_result($cnt);
        $stmt->fetch();
        $stmt->close();

        if ($cnt > 0) {
            header("Location:../../pages/usuarios/listado_users.php?alert=UserExists");
            exit();
        }

        if (!empty($password)) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE `Usuarios` SET `User`=?, `Password`=?, `Cod_Empleado`=?, `email`=?, `id_group_user`=?, `cod_tienda`=?, `State`=?, `email_active`=? WHERE `Codigo`=?");
            if (!$stmt) { header("Location:../../pages/usuarios/listado_users.php?alert=DBErr"); exit(); }
            $stmt->bind_param("ssssssiii", $user, $hash, $cod_empleado, $email, $id_group_user, $cod_tienda, $state, $email_active, $codigo);
        } else {
            $stmt = $conn->prepare("UPDATE `Usuarios` SET `User`=?, `Cod_Empleado`=?, `email`=?, `id_group_user`=?, `cod_tienda`=?, `State`=?, `email_active`=? WHERE `Codigo`=?");
            if (!$stmt) { header("Location:../../pages/usuarios/listado_users.php?alert=DBErr"); exit(); }
            $stmt->bind_param("sssssiii", $user, $cod_empleado, $email, $id_group_user, $cod_tienda, $state, $email_active, $codigo);
        }

        $ok = $stmt->execute();
        $stmt->close();

        if ($ok) {
            header("Location:../../pages/usuarios/listado_users.php?alert=1");
            exit();
        } else {
            header("Location:../../pages/usuarios/listado_users.php?alert=UpdateError");
            exit();
        }
        break;

    case "2": // Eliminar usuario
        $codigo = isset($_POST['codigo']) ? (int)$_POST['codigo'] : 0;
        if ($codigo === 0) {
            header("Location:../../pages/usuarios/listado_users.php?alert=NoId");
            exit();
        }
        $stmt = $conn->prepare("DELETE FROM `Usuarios` WHERE `Codigo` = ?");
        if (!$stmt) { header("Location:../../pages/usuarios/listado_users.php?alert=DBErr"); exit(); }
        $stmt->bind_param("i", $codigo);
        $ok = $stmt->execute();
        $stmt->close();

        if ($ok) {
            header("Location:../../pages/usuarios/listado_users.php?alert=2");
            exit();
        } else {
            header("Location:../../pages/usuarios/listado_users.php?alert=DeleteError");
            exit();
        }
        break;

    default:
        header("Location:../../pages/usuarios/listado_users.php?alert=NoAction");
        exit();
        break;
}
?>