<?php
include("../../conexion.php");

// Verificar conexión
if ($conn->connect_error) {
    die("Error en la conexión: " . $conn->connect_error);
}

// Verificar si se enviaron los datos desde el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir los datos del formulario
    $name = $_POST['name']; // Nuevo campo de nombre
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone = $_POST['phone'];

    // Validar los datos
    if (!empty($name) && !empty($email) && !empty($password) && !empty($phone)) {
        // Insertar los datos en la tabla icloud_accounts
        $sql = "INSERT INTO icloud_accounts (name, email, password, phone_number, created_at) 
                VALUES (?, ?, ?, ?, NOW())";

        // Preparar la sentencia
        if ($stmt = $conn->prepare($sql)) {
            // Encriptar la contraseña antes de guardar
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // Enlazar parámetros y ejecutar la consulta
            $stmt->bind_param("ssss", $name, $email, $hashed_password, $phone);

            // Ejecutar consulta
            if ($stmt->execute()) {
                // Redirigir a la página de impresión con los datos
                header("Location: print_icloud.php?name=".urlencode($name)."&email=".urlencode($email)."&phone=".urlencode($phone)."&password=".urlencode($password));
                exit();
            } else {
                echo "Error: " . $stmt->error;
            }

            // Cerrar statement
            $stmt->close();
        } else {
            echo "Error al preparar la consulta: " . $conn->error;
        }
    } else {
        echo "Por favor, completa todos los campos.";
    }
}

// Cerrar la conexión
$conn->close();
?>
