<?php
session_start();

error_log("Contenido de POST: " . print_r($_POST, true)); // Depuración

include("../../conexion.php");

// Verifica si los datos del formulario están presentes
if (!isset($_POST['usuario']) || !isset($_POST['pass']) || !isset($_POST['csrf_token'])) {
    header("Location: ../login.php?error=2"); // Error si faltan datos
    exit();
}

// Verifica el token CSRF para proteger contra ataques CSRF
if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header("Location: ../login.php?error=3"); // Error de token CSRF
    exit();
}

$usuario = mysqli_real_escape_string($conn, $_POST['usuario']);
$pass = mysqli_real_escape_string($conn, $_POST['pass']);

// Consulta para obtener la información del usuario
$sql = "SELECT `Codigo`, `User`, `State`, `cod_tienda`, `Password` 
        FROM Usuarios 
        WHERE User = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 's', $usuario);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_array($resultado)) {
    // Verifica la contraseña (se asume hashing)
    if (password_verify($pass, $row['Password'])) {
        $_SESSION['username'] = $usuario;
        $_SESSION['cod_user'] = $row['Codigo'];
        $_SESSION['state'] = $row['State'];

        // Consulta de tiendas asignadas al usuario
        $sql_tiendas = "SELECT cod_tienda FROM `asignacion_tienda` WHERE cod_user = ?";
        $stmt_tiendas = mysqli_prepare($conn, $sql_tiendas);
        mysqli_stmt_bind_param($stmt_tiendas, 'i', $row['Codigo']);
        mysqli_stmt_execute($stmt_tiendas);
        $resultado_tiendas = mysqli_stmt_get_result($stmt_tiendas);

        $marcas = [];
        while ($fila = mysqli_fetch_array($resultado_tiendas)) {
            $marcas[] = $fila['cod_tienda'];
        }

        if (!empty($marcas)) {
            $_SESSION['tiendas'] = implode(', ', $marcas);
        }

        $_SESSION['tienda_user'] = $row['cod_tienda'];

        // Maneja la opción "Recuérdame"
        if (isset($_POST['remember'])) {
            setcookie('remember_user', $usuario, time() + (86400 * 30), "/");
        } else {
            setcookie('remember_user', '', time() - 3600, "/");
        }

        $linkre = isset($_GET['linkre']) ? $_GET['linkre'] : '../dashboard.php';
        header("Location: $linkre");
        exit();
    } else {
        header("Location: ../login.php?error=1");
        exit();
    }
} else {
    header("Location: ../login.php?error=4");
    exit();
}
?>
