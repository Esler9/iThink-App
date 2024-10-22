<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imprimir Cuenta iCloud</title>
    <style>
        /* Estilos para la impresión */
        @media print {
            @page {
                size: 80mm 297mm landscape; /* Formato horizontal */
                margin: 5mm; /* Margen pequeño */
            }
            body {
                margin: 0;
                padding: 0;
                font-family: Arial, sans-serif;
                font-size: 18px; /* Texto más grande */
                background-color: #fff;
                color: #000;
            }
            .content {
                width: 100%;
                padding: 10px; /* Margen pequeño */
                box-sizing: border-box;
            }
            h2 {
                font-size: 22px; /* Título más grande */
                margin: 0;
                padding: 5px 0;
                text-align: center;
                border-bottom: 2px solid #007bff; /* Línea elegante debajo del título */
                margin-bottom: 10px;
                color: #007bff; /* Color azul para resaltar el título */
            }
            p {
                font-size: 18px; /* Texto de párrafo más grande */
                margin: 8px 0;
                padding: 0;
                line-height: 1.6;
                border-bottom: 1px solid #ccc; /* Separador entre líneas */
                padding-bottom: 5px;
                display: flex;
                align-items: center;
            }
            .icon {
                font-size: 22px; /* Tamaño del ícono más grande */
                margin-right: 10px;
                color: #007bff; /* Color de los íconos */
            }
            .footer {
                margin-top: 15px;
                text-align: center;
                font-size: 14px;
                color: #666;
            }
            .btn {
                display: none !important; /* Asegurar que los botones no se muestren */
            }
        }
        
        /* Estilos para la vista normal */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f4f8;
            color: #333;
        }
        .container {
            width: 100%;
            max-width: 80mm;
            margin: 0 auto;
            padding: 15px;
            background-color: #fff;
            box-sizing: border-box;
            border-radius: 6px; /* Bordes más suaves */
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05); /* Sombra suave */
        }
        h2 {
            text-align: center;
            font-size: 22px; /* Título más grande */
            border-bottom: 2px solid #007bff;
            padding-bottom: 8px;
            margin-bottom: 15px;
            color: #007bff;
        }
        p {
            font-size: 18px; /* Texto más grande */
            margin: 12px 0;
            line-height: 1.6;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            display: flex;
            align-items: center; /* Alineación de íconos */
        }
        .icon {
            font-size: 22px; /* Tamaño del ícono más grande */
            margin-right: 10px;
            color: #007bff; /* Color de los íconos */
        }
        .btn {
            margin-top: 15px;
            width: calc(100% - 10px); /* Ajusta el ancho del botón para que encaje en el margen */
            padding: 10px;
            background-color: #007bff;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            display: block;
            font-size: 16px;
            cursor: pointer;
            margin-left: 5px; /* Añadimos un margen para centrar */
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 14px;
            color: #666;
        }
    </style>
    <!-- Cargar íconos de Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container">
        <h2>Detalles de la Cuenta iCloud</h2>
        <div class="content">
            <p><i class="fas fa-user icon"></i> <?php echo htmlspecialchars($_GET['name']); ?></p> <!-- Campo para el nombre -->
            <p><i class="fas fa-envelope icon"></i> <?php echo htmlspecialchars($_GET['email']); ?></p>
            <p><i class="fas fa-lock icon"></i> <?php echo htmlspecialchars($_GET['password']); ?></p>
            <p><i class="fas fa-phone icon"></i> <?php echo htmlspecialchars($_GET['phone']); ?></p>
        </div>
        <button class="btn" onclick="window.print()">Imprimir</button>
        <a href="/home/dashboard.php" class="btn">Regresar</a>
        <div class="footer">
            <p>Gracias por utilizar nuestro servicio.</p>
        </div>
    </div>
</body>
</html>
