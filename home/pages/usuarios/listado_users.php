<?php
session_start();
include("../../../conexion.php");
include("../../logica/ac_permiso.php");

// Si no existe la conexión, evitar errores
if (!isset($conn)) {
    die("Conexión a la base de datos no disponible.");
}

// Consulta de usuarios
$sql = "SELECT id, nombre, email, IFNULL(rol,'Usuario') as rol FROM usuarios";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Usuarios</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f6f8; margin: 0; padding: 0; }
        .page { display: flex; gap: 24px; max-width: 1100px; margin: 40px auto; padding: 0 16px; }
        aside.sidebar { width: 260px; background: #fff; border-radius: 8px; padding: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.07); }
        .container { flex: 1; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.07); padding: 32px; }
        h1 { text-align: center; color: #333; margin-bottom: 24px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { padding: 12px 10px; text-align: left; }
        th { background: #0078d4; color: #fff; }
        tr:nth-child(even) { background: #f0f4fa; }
        tr:hover { background: #e6f7ff; }
        .btn { padding: 6px 12px; background: #0078d4; color: #fff; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; font-size: 14px; margin-right: 6px; }
        .btn:hover { background: #005fa3; }
    </style>
</head>
<body>
    <div class="page">
        <aside class="sidebar">
            <?php
            // Incluye el sidebar (ruta relativa desde este archivo)
            // Si sidebar.php genera variables o HTML, se mostrará aquí.
            $sidebarPath = __DIR__ . "/../sidebar.php";
            if (file_exists($sidebarPath)) {
                include $sidebarPath;
            } else {
                echo "<p>Sidebar no disponible (archivo no encontrado).</p>";
            }
            ?>
        </aside>

        <main class="container">
            <h1>Listado de Usuarios</h1>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $id = htmlspecialchars($row['id']);
                        $nombre = htmlspecialchars($row['nombre']);
                        $email = htmlspecialchars($row['email']);
                        $rol = htmlspecialchars($row['rol']);
                        echo "<tr>
                                <td>{$id}</td>
                                <td>{$nombre}</td>
                                <td>{$email}</td>
                                <td>{$rol}</td>
                                <td>
                                    <a href=\"editar_usuario.php?id={$id}\" class=\"btn\">Editar</a>
                                    <a href=\"eliminar_usuario.php?id={$id}\" class=\"btn\" onclick=\"return confirm('Eliminar usuario?');\">Eliminar</a>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No hay usuarios registrados.</td></tr>";
                }
                $conn->close();
                ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>