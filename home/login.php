<?php
session_start();

// Incluir archivos necesarios
include('Funcion.php');
include('../Setting.php');

// Verificar si el sistema está en mantenimiento
if ($mantenimiento === true) {
    header('Location: ../mantenimiento.php');
    exit;
}

// Generar token CSRF si no existe
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Recuperar el usuario recordado en la cookie (si existe)
$rememberedUser = isset($_COOKIE['remember_user']) ? $_COOKIE['remember_user'] : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>iThink | Log in</title>
    <link rel="icon" href="/home/dist/img/logo_ithink.png" type="image/x-icon">

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-logo">
            <img src="/home/dist/img/logo_ithink.png" alt="logo iThink" width="50">
            <a href="https://www.ithinkguatemala.com"><b>iThink</b> Web</a>
        </div>
        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Inicia Sesión</p>

                <!-- Mostrar mensajes de error según parámetro -->
                <?php
                if (isset($_GET['error'])):
                    $errorCode = $_GET['error'];
                    switch ($errorCode) {
                        case 1:
                            $mensaje = "Contraseña incorrecta.";
                            break;
                        case 2:
                            $mensaje = "Faltan datos.";
                            break;
                        case 3:
                            $mensaje = "Token CSRF inválido.";
                            break;
                        case 4:
                            $mensaje = "Usuario no encontrado.";
                            break;
                        default:
                            $mensaje = "Inicio de Sesión Inválido.";
                    }
                ?>
                    <div class="alert alert-danger text-white" role="alert" style="background-color: #dc3545;">
                        <i class="fas fa-exclamation-triangle"></i> <?php echo $mensaje; ?>
                    </div>
                <?php endif; ?>

                <!-- Formulario de Login -->
                <form action="logica/loguear.php" method="POST">
                    <!-- Token CSRF -->
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                    <div class="input-group mb-3">
                        <input type="text" name="usuario" class="form-control" placeholder="Usuario"
                            value="<?php echo htmlspecialchars($rememberedUser); ?>" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                    </div>

                    <div class="input-group mb-3">
                        <input type="password" name="pass" id="password" class="form-control" placeholder="Contraseña" required autocomplete="off">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-secondary" onclick="togglePassword()">
                                <i id="toggleIcon" class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-8">
                            <div class="icheck-primary">
                                <input type="checkbox" id="remember" name="remember" <?php echo isset($_COOKIE['remember_user']) ? 'checked' : ''; ?>>
                                <label for="remember">Recuérdame</label>
                            </div>
                        </div>
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block">Login</button>
                        </div>
                    </div>
                </form>
            </div>
            <!-- /.login-card-body -->
        </div>
    </div>

    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.min.js"></script>

    <!-- Script para mostrar/ocultar contraseña -->
    <script>
        function togglePassword() {