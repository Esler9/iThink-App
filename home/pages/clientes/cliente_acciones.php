<?php
session_start();
include("../../../conexion.php");

// Verificamos si hay un ID o función especificados en la URL
if (isset($_GET['func']) && isset($_GET['id'])) {
    $func = $_GET['func'];
    $id = $_GET['id'];
    
    // Función Agregar (func = 1)
    if ($func == 1) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nombre = $_POST['nombre'];
            $email = $_POST['email'];
            $telefono = $_POST['telefono'];
            $direccion = $_POST['direccion'];

            // Consulta para agregar cliente
            $sql = "INSERT INTO clientes (nombre, email, telefono, direccion) VALUES ('$nombre', '$email', '$telefono', '$direccion')";
            
            if ($conn->query($sql) === TRUE) {
                header("Location: index.php?msg=Cliente agregado con éxito");
            } else {
                echo "Error al agregar cliente: " . $conn->error;
            }
        }
    }

    // Función Editar (func = 2)
    elseif ($func == 2) {
        // Obtener los datos del cliente a editar
        $sql = "SELECT * FROM clientes WHERE id = $id";
        $resultado = $conn->query($sql);
        if ($resultado->num_rows > 0) {
            $cliente = $resultado->fetch_assoc();
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nombre = $_POST['nombre'];
            $email = $_POST['email'];
            $telefono = $_POST['telefono'];
            $direccion = $_POST['direccion'];

            // Consulta para actualizar el cliente
            $sql = "UPDATE clientes SET nombre='$nombre', email='$email', telefono='$telefono', direccion='$direccion' WHERE id=$id";

            if ($conn->query($sql) === TRUE) {
                header("Location: index.php?msg=Cliente editado con éxito");
            } else {
                echo "Error al editar cliente: " . $conn->error;
            }
        }
    }

    // Función Eliminar (func = 3)
    elseif ($func == 3) {
        // Consulta para eliminar el cliente
        $sql = "DELETE FROM clientes WHERE id=$id";
        if ($conn->query($sql) === TRUE) {
            header("Location: index.php?msg=Cliente eliminado con éxito");
        } else {
            echo "Error al eliminar cliente: " . $conn->error;
        }
    }
} else {
    echo "Parámetros inválidos.";
}
?>
