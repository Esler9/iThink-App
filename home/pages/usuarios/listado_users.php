<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Usuarios</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 900px;
            margin: 40px auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            padding: 32px;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 24px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }
        th, td {
            padding: 12px 10px;
            text-align: left;
        }
        th {
            background: #0078d4;
            color: #fff;
        }
        tr:nth-child(even) {
            background: #f0f4fa;
        }
        tr:hover {
            background: #e6f7ff;
        }
        .btn {
            padding: 6px 16px;
            background: #0078d4;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }
        .btn:hover {
            background: #005fa3;
        }
    </style>
</head>
<body>
    <div class="container">
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
                <tr>
                    <td>1</td>
                    <td>Ana Pérez</td>
                    <td>ana.perez@email.com</td>
                    <td>Administrador</td>
                    <td>
                        <a href="#" class="btn">Editar</a>
                        <a href="#" class="btn">Eliminar</a>
                    </td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Carlos Gómez</td>
                    <td>carlos.gomez@email.com</td>
                    <td>Usuario</td>
                    <td>
                        <a href="#" class="btn">Editar</a>
                        <a href="#" class="btn">Eliminar</a>
                    </td>
                </tr>
                <!-- Más usuarios aquí -->
            </tbody>
        </table>
    </div>
</body>
</html>