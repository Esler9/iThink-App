<?php
session_start();
if (!isset($_SESSION["username"])) {
    header('Location: login.php');
    exit();
}
// Redirigir al listado de usuarios
header('Location: listado_users.php');
exit();
?>