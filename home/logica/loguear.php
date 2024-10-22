<?php
session_start();
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
    // Verifica la contraseña (asume que las contraseñas están almacenadas como hashes)
    if (password_verify($pass, $row['Password'])) {
        // Usuario autenticado correctamente
        
        $_SESSION['username'] = $usuario;
        $_SESSION['cod_user'] = $row['Codigo'];
        $_SESSION['state'] = $row['State'];

        // Consulta las tiendas asignadas al usuario
        $sql_tiendas = "SELECT cod_tienda FROM `asignacion_tienda` WHERE cod_user = ?";
        $stmt_tiendas = mysqli_prepare($conn, $sql_tiendas);
        mysqli_stmt_bind_param($stmt_tiendas, 'i', $cod_user);
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
            setcookie('remember_user', $usuario, time() + (86400 * 30), "/"); // 30 días
        } else {
            setcookie('remember_user', '', time() - 3600, "/"); // Eliminar cookie si "Recuérdame" no está seleccionado
        }

        // Redirecciona al usuario a la página correspondiente
        $linkre = isset($_GET['linkre']) ? $_GET['linkre'] : '../dashboard.php';
        header("Location: $linkre");
        exit();
    } else {
        // Contraseña incorrecta
        header("Location: ../login.php?error=1");
        exit();
    }
} else {
    // Usuario no encontrado
    header("Location: ../login.php?error=4");
    exit();
}
?>
