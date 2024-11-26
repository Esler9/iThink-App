<?php

$Correo_Consulta = " <!DOCTYPE html>
<html lang='en'>
<head>
<meta charset='UTF-8'>
<meta name='viewport' content='width=device-width, initial-scale=1.0'>
<title>Nueva Liberación</title>
<style>
    body {
        font-family: Arial, sans-serif;
        line-height: 1.6;
        color: #333;
        background-color: #f9f9f9;
        margin: 0;
        padding: 0;
    }
    .container {
        max-width: 600px;
        margin: 20px auto;
        padding: 20px;
        background: #ffffff;
        border: 1px solid #ddd;
        border-radius: 5px;
        box-shadow: 0 2px 3px rgba(0, 0, 0, 0.1);
    }
    .header {
        font-size: 20px;
        font-weight: bold;
        margin-bottom: 20px;
    }
    .content {
        margin-bottom: 20px;
    }
    .content p {
        margin: 5px 0;
    }
    .cta {
        text-align: center;
        margin-top: 20px;
    }
    .cta a {
        display: inline-block;
        padding: 10px 20px;
        color: #fff;
        background-color: #007bff;
        text-decoration: none;
        border-radius: 5px;
        font-size: 16px;
        font-weight: bold;
    }
    .cta a:hover {
        background-color: #0056b3;
    }
    .footer {
        text-align: center;
        font-size: 12px;
        color: #666;
        margin-top: 20px;
    }
</style>
</head>
<body>
<div class='container'>
    <div class='header'>¡Nueva Consulta de Liberación!</div>
    <div class='content'>
        <p><strong>Nombre:</strong> $name</p>
        <p><strong>IMEI:</strong> $imei</p>
        <p><strong>Modelo:</strong> $model</p>
        <p><strong>Celular:</strong> $celular</p>
    </div>
    <div class='cta'>
        <a href='https://app.ithinkguatemala.com/home/pages/tables/buscar.php?search=$imei' target='_blank'>Informar Consulta</a>
    </div>
    <div class='footer'>
        <p>Este es un correo automático, no responder.</p>
        <p>Código: 0</p>
    </div>
</div>
</body>
</html>


"
?>