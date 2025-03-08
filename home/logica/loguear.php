<?php
session_start();

error_log("DEBUG: Inicio de loguear.php");
error_log("DEBUG: Contenido de POST: " . print_r($_POST, true));

include("../../conexion.php");

// Verificar si cada variable se recibió y generar mensajes de depuración
if (!isset($_POST['usuario'])) {
    error_log("DEBUG: Variable 'usuario' no recibida en POST.");
}
if (!isset($_POST['pass'])) {
    error_log("DEBUG: Variable 'pass' no recibida en POST.");
}
if (!isset($_POST['csrf_token'])) {
    error_log("DEBUG: Variable 'csrf_token' no recibida en POST.");
}

// Validar que se hayan recibido todos los campos requeridos
if (!isset($_POST['usuario']) || !isset($_POST['pass']) || !isset($_POST['csrf_token'])) {
    error_log("DEBUG: Faltan datos en POST. usuario:" . (isset($_POST['usuario']) ? "si" : "no") .
        ", pass:" . (isset($_POST['pass']) ? "si" : "no") .
        ", csrf_token:" . (isset($_POST['csrf_token']) ? "si" : "no"));
    header("Location: ../login.php?error=2"); // Error: faltan datos
    exit();
}

// Verificar que el token CSRF enviado coincida con el almacenado en la sesión
if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    error_log("DEBUG: Token CSRF inválido. POST: " . $_POST['csrf_token'] . " | Sesión: " . $_SESSION['csrf_token']);
    header("Location: ../login.php?error=3"); // Error: token CSRF inválido
    exit();
}

// Escapar datos para prevenir inyección SQL
$usuario = mysqli_real_escape_string($conn, $_POST['usuario']);
$pass = mysqli_real_escape_string($conn, $_POST['pass']);

// Preparar consulta para obtener la información del usuario
$sql = "SELECT `Codigo`, `User`, `State`, `cod_tienda`, `Password` 
        FROM Usuarios 
        WHERE User = ?";
$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    error_log("DEBUG: Error en mysqli_prepare: " . mysqli_error($conn));
    header("Location: ../login.php?error=5"); // Error: fallo en la preparación de la consulta
    exit();
}
mysqli_stmt_bind_param($stmt, 's', $usuario);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_array($resultado)) {
    // Verificar la contraseña utilizando password_verify
    if (password_verify($pass, $row['Password'])) {
        // Configurar variables de sesión
        $_SESSION['username'] = $usuario;
        $_SESSION['cod_user'] = $row['Codigo'];
        $_SESSION['state'] = $row['State'];

        // Consulta para obtener las tiendas asignadas al usuario
        $sql_tiendas = "SELECT cod_tienda FROM asignacion_tienda WHERE cod_user = ?";
        $stmt_tiendas = mysqli_prepare($conn, $sql_tiendas);
        if ($stmt_tiendas) {
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
            mysqli_stmt_close($stmt_tiendas);
        } else {
            error_log("Error en mysqli_prepare (tiendas): " . mysqli_error($conn));
        }

        $_SESSION['tienda_user'] = $row['cod_tienda'];

        // Manejo de la opción "Recuérdame"
        if (isset($_POST['remember'])) {
            setcookie('remember_user', $usuario, time() + (86400 * 30), "/");
        } else {
            setcookie('remember_user', '', time() - 3600, "/");
        }

        // Redirigir al usuario a la página de destino (dashboard u otra)
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
