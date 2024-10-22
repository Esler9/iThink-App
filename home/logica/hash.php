<?php 
session_start();

// Verifica si la sesión está iniciada
if (!isset($_SESSION['username'])) {
    header('Location: ../login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generar y Copiar Hash de Contraseña</title>
    <style>
        /* Estilos básicos para el formulario y el botón */
        form {
            margin-bottom: 20px;
        }
        #hashOutput {
            border: 1px solid #ccc;
            padding: 10px;
            background-color: #f9f9f9;
            margin-top: 10px;
            position: relative;
        }
        .copy-button {
            margin-top: 10px;
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h1>Generar y Copiar Hash de Contraseña</h1>

    <!-- Formulario para ingresar la contraseña -->
    <form method="post">
        <label for="password">Contraseña:</label>
        <input type="text" id="password" name="password" required>
        <button type="submit">Generar Hash</button>
    </form>

    <?php
    // Verifica si se envió el formulario
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Obtiene la contraseña del formulario
        $password = $_POST['password'];

        // Genera el hash de la contraseña usando bcrypt
        $hash = password_hash($password, PASSWORD_BCRYPT);

        // Muestra el hash con un campo para copiar
        echo '<h2>Hash de la Contraseña:</h2>';
        echo '<div id="hashOutput">';
        echo '<p id="hashText">' . htmlspecialchars($hash) . '</p>';
        echo '<button class="copy-button" onclick="copyToClipboard()">Copiar Hash</button>';
        echo '</div>';
    }
    ?>

    <script>
    // Función para copiar el contenido al portapapeles
    function copyToClipboard() {
        var hashText = document.getElementById('hashText').innerText;
        var tempInput = document.createElement('input');
        document.body.appendChild(tempInput);
        tempInput.value = hashText;
        tempInput.select();
        document.execCommand('copy');
        document.body.removeChild(tempInput);
        alert('Hash copiado al portapapeles!');
    }
    </script>
</body>
</html>
