<?php
include("../../../conexion.php");
include("../../logica/ac_permiso.php");

// Si conexion.php no define $conn, usar fallback (opcional)
if (!isset($conn)) {
    $host = 'localhost';
    $user = 'root';
    $password = '';
    $dbname = 'nombre_de_tu_base_de_datos';
    $conn = new mysqli($host, $user, $password, $dbname);
}

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Consulta para obtener los usuarios
$sql = "SELECT id, nombre, email FROM usuarios";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Listado de Usuarios</title>
    <style>
        table { border-collapse: collapse; width: 60%; margin: 20px auto; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2 style="text-align:center;">Listado de Usuarios</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Email</th>
        </tr>
        <?php
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['nombre']}</td>
                        <td>{$row['email']}</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No hay usuarios registrados.</td></tr>";
        }
        $conn->close();
        ?>
    </table>
</body>
</html>