<?php
/**
 * Genera el cuerpo del correo HTML según el tipo de notificación y los datos proporcionados.
 *
 * @param string $tipo   Tipo de correo ("consulta", "pendiente", "aprobado", "rechazado", etc.).
 * @param array  $datos  Arreglo asociativo con la información necesaria. Se espera que incluya al menos:
 *                       - 'name'           => Nombre del cliente
 *                       - 'celular'        => Número de celular
 *                       - 'imei'           => IMEI o serie
 *                       - 'modelo'         => Modelo del dispositivo
 *                       Además, opcionalmente:
 *                       - 'precio'         => Precio
 *                       - 'tiempo'         => Tiempo
 *                       - 'observaciones'  => Observaciones
 *
 * @return string        Cadena con el contenido HTML del correo.
 */
function correo_enviar($tipo, $datos)
{
    // Plantilla base del correo: cabecera con estilos
    $html_header = "<!DOCTYPE html>
<html lang='es'>
<head>
<meta charset='UTF-8'>
<meta name='viewport' content='width=device-width, initial-scale=1.0'>
<title>Notificación de Liberación</title>
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
    .separator {
        border-bottom: 1px dashed #ccc;
        margin: 20px 0;
    }
    .content p {
        margin: 5px 0;
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
<div class='container'>";

    // Pie de página base con un placeholder para el código de notificación
    $html_footer = "<div class='footer'>
        <p>Este es un Correo Automático no responder.</p>
        <p>Código: {codigo}</p>
    </div>
</div>
</body>
</html>";

    // Según el tipo de correo, se definen el título, el texto del botón y el código a mostrar.
    switch ($tipo) {
        case "consulta":
            $titulo  = "¡Nueva Consulta de Liberación!";
            $boton   = "Informar Consulta";
            $codigo  = "0";
            break;
        case "pendiente":
            $titulo  = "¡Liberación Cambio a Pendiente!";
            $boton   = "Realizar Acción";
            $codigo  = "1";
            break;
        case "aprobado":
            $titulo  = "¡Liberación Aprobada Por Cliente!";
            $boton   = "Ver Detalles";
            $codigo  = "2";
            break;
        case "iniciado":
            $titulo  = "¡Inicio de Proceso de Liberación!";
            $boton   = "Ver Progreso";
            $codigo  = "3";
            break;
        case "finalizado":
            $titulo  = "¡Finalizado el Proceso de Liberación!";
            $boton   = "Ver Resultados";
            $codigo  = "4";
            break;
        case "rechazado":
            $titulo  = "¡Liberación Rechazada!";
            $boton   = "Realizar Acción";
            $codigo  = "5";
            break;
        case "consultaReiniciada":
            $titulo  = "¡Nueva Consulta de Liberación: Consulta Reiniciada!";
            $boton   = "Realizar Acción";
            $codigo  = "2.1";
            break;
        // Puedes agregar más casos según los diferentes estados (ej. rechazado desde aprobadas, rechazado desde finalizadas, etc.)
        default:
            $titulo  = "Notificación de Liberación";
            $boton   = "Ver Detalles";
            $codigo  = "0";
            break;
    }

    // Construir la sección del contenido con los datos recibidos.
    $content = "<div class='header'>$titulo</div>
    <div class='separator'></div>
    <div class='content'>
        <p>Se detallan los datos asociados:</p>
        <p><strong>Nombre:</strong> " . $datos['name'] . "</p>
        <p><strong>Celular:</strong> " . $datos['celular'] . "</p>
        <p><strong>IMEI:</strong> " . $datos['imei'] . "</p>
        <p><strong>Modelo:</strong> " . $datos['modelo'] . "</p>";

    // Agregar opcionalmente Precio, Tiempo y Observaciones si están definidos.
    if (isset($datos['precio'])) {
        $content .= "<p><strong>Precio:</strong> " . $datos['precio'] . "</p>";
    }
    if (isset($datos['tiempo'])) {
        $content .= "<p><strong>Tiempo:</strong> " . $datos['tiempo'] . "</p>";
    }
    if (isset($datos['observaciones'])) {
        $content .= "<p><strong>Observaciones:</strong> " . $datos['observaciones'] . "</p>";
    }

    $content .= "</div>
    <div class='separator'></div>
    <div class='content'>
        <p>En el siguiente link puedes realizar la acción necesaria:</p>
        <div class='cta'>
            <a href='https://app.ithinkguatemala.com/home/pages/tables/buscar.php?search=" . $datos['imei'] . "' target='_blank'>$boton</a>
        </div>
    </div>
    <div class='footer'>
        <p>¡Gracias!</p>
    </div>";

    // Reemplazar el placeholder {codigo} en el footer
    $html_footer = str_replace("{codigo}", $codigo, $html_footer);

    // Unir la cabecera, el contenido y el pie para formar el correo completo.
    $correo = $html_header . $content . $html_footer;
    return $correo;
}
?>
