<?php
session_start();

$DEBUG_MODE = true; // Cambia a false en producción
$debugMessages = [];

$debugMessages[] = "DEBUG: Inicio de loguear.php";
$debugMessages[] = "DEBUG: Contenido de POST: " . print_r($_POST, true);

include("../../conexion.php");

// Verificar si cada variable se recibió y generar mensajes de depuración
if (!isset($_POST['usuario'])) {
    $msg = "DEBUG: Variable 'usuario' no recibida en POST.";
    error_log($msg);
    $debugMessages[] = $msg;
}
if (!isset($_POST['pass'])) {
    $msg = "DEBUG: Variable 'pass' no recibida en POST.";
    error_log($msg);
    $debugMessages[] = $msg;
}
if (!isset($_POST['csrf_token'])) {
    $msg = "DEBUG: Variable 'csrf_token' no recibida en POST.";
    error_log($msg);
    $debugMessages[] = $msg;
}

// Validar que se hayan recibido todos los campos requeridos
if (!isset($_POST['usuario']) || !isset($_POST['pass']) || !isset($_POST['csrf_token'])) {
    $msg = "DEBUG: Faltan datos en POST. usuario:" . (isset($_POST['usuario']) ? "si" : "no") .
        ", pass:" . (isset($_POST['pass']) ? "si" : "no") .
        ", csrf_token:" . (isset($_POST['csrf_token']) ? "si" : "no");
    error_log($msg);
    $debugMessages[] = $msg;
    if ($DEBUG_MODE) {
        echo "<pre>" . implode("\n", $debugMessages) . "</pre>";
        exit();
    }
    header("Location: ../login.php?error=2"); // Error: faltan datos
    exit();
}

// Verificar que el token CSRF enviado coincida con el almacenado en la sesión
if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $msg = "DEBUG: Token CSRF inválido. POST: " . $_POST['csrf_token'] . " | Sesión: " . (isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : 'no token');
    error_log($msg);
    $debugMessages[] = $msg;
    if ($DEBUG_MODE) {
        echo "<pre>" . implode("\n", $debugMessages) . "</pre>";
        exit();
    }
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
    $msg = "DEBUG: Error en mysqli_prepare: " . mysqli_error($conn);
    error_log($msg);
    $debugMessages[] = $msg;
    if ($DEBUG_MODE) {
        echo "<pre>" . implode("\n", $debugMessages) . "</pre>";
        exit();
    }
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
            $msg = "DEBUG: Error en mysqli_prepare (tiendas): " . mysqli_error($conn);
            error_log($msg);
            $debugMessages[] = $msg;
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
        $msg = "DEBUG: Contraseña incorrecta para usuario: " . $usuario;
        error_log($msg);
        $debugMessages[] = $msg;
        if ($DEBUG_MODE) {
            echo "<pre>" . implode("\n", $debugMessages) . "</pre>";
            exit();
        }
        header("Location: ../login.php?error=1");
        exit();
    }
} else {
    $msg = "DEBUG: Usuario no encontrado: " . $usuario;
    error_log($msg);
    $debugMessages[] = $msg;
    if ($DEBUG_MODE) {
        echo "<pre>" . implode("\n", $debugMessages) . "</pre>";
        exit();
    }
    header("Location: ../login.php?error=4");
    exit();
}
?>
