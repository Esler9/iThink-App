<?php
/**
 * Genera el cuerpo del correo HTML según el tipo de notificación y los datos proporcionados.
 *
 * @param string $tipo   Tipo de correo ("consulta", "pendiente", "aprobado", "finalizado", "rechazado", "consultaReiniciada", etc.).
 * @param array  $datos  Arreglo asociativo con la información necesaria. Se espera que incluya:
 *                       - 'name'          => Nombre del cliente
 *                       - 'celular'       => Número de celular
 *                       - 'imei'          => IMEI o serie
 *                       - 'modelo'        => Modelo del dispositivo
 *                       Además, opcionalmente:
 *                       - 'precio'        => Precio
 *                       - 'tiempo'        => Tiempo
 *                       - 'observaciones' => Observaciones
 *
 * @return string        Cadena con el contenido HTML del correo.
 */
function correo_enviar($tipo, $datos)
{
    // Cabecera HTML y estilos base
    $html_header = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Notificación de Liberación</title>
<style>
    /* Reset y estilos base */
    body, p, div { margin: 0; padding: 0; }
    body {
        background-color: #f4f4f4;
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        color: #333;
        line-height: 1.6;
        padding: 20px;
    }
    .container {
        max-width: 600px;
        background: #ffffff;
        margin: 0 auto;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .header {
        text-align: center;
        padding: 20px;
        color: #ffffff;
    }
    .header h1 {
        font-size: 24px;
        margin: 0;
    }
    .content {
        padding: 20px;
    }
    .content p {
        margin-bottom: 15px;
    }
    .details p {
        margin: 8px 0;
    }
    .cta {
        text-align: center;
        margin: 30px 0;
    }
    .cta a {
        background: #28a745;
        color: #ffffff;
        padding: 12px 25px;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
        transition: background 0.3s ease;
    }
    .cta a:hover {
        background: #218838;
    }
    .footer {
        background: #f4f4f4;
        text-align: center;
        padding: 15px;
        font-size: 12px;
        color: #777;
    }
    @media (max-width: 600px) {
        .container { width: 100%; }
        .header h1 { font-size: 20px; }
        .cta a { padding: 10px 20px; font-size: 14px; }
    }
</style>
</head>
<body>
<div class="container">
HTML;

    // Pie de página, con placeholder para el código
    $html_footer = <<<HTML
<div class="footer">
    <p>Este es un correo automático, no responder.</p>
    <p>Código: {codigo}</p>
</div>
</div>
</body>
</html>
HTML;

    // Definir variables según el tipo de notificación
    switch ($tipo) {
        case "consulta":
            $titulo      = "¡Nueva Consulta de Liberación!";
            $ctaText     = "Informar Consulta";
            $codigo      = "0";
            $headerColor = "#0097A7";  // Azul para Consultas
            $icon        = "&#x2139;";  // Ícono de información
            break;
        case "pendiente":
            $titulo      = "¡Liberación Cambio a Pendiente!";
            $ctaText     = "Realizar Acción";
            $codigo      = "1";
            $headerColor = "#D32F2F";  // Rojo para Pendientes
            $icon        = "&#x23F3;";  // Reloj de arena
            break;
        case "aprobado":
            $titulo      = "¡Liberación Aprobada Por Cliente!";
            $ctaText     = "Ver Detalles";
            $codigo      = "2";
            $headerColor = "#388E3C";  // Verde para Aprobadas
            $icon        = "&#x2705;";  // Marca de verificación
            break;
        case "finalizado":
            $titulo      = "¡Finalizado el Proceso de Liberación!";
            $ctaText     = "Ver Resultados";
            $codigo      = "4";
            $headerColor = "#FBC02D";  // Amarillo para Finalizadas
            $icon        = "&#x1F3C1;"; // Bandera a cuadros
            break;
        case "iniciado":
            $titulo      = "¡Inicio de Proceso de Liberación!";
            $ctaText     = "Ver Progreso";
            $codigo      = "3";
            $headerColor = "#17a2b8";  // Se mantiene un color por defecto (teal)
            $icon        = "&#x1F680;"; // Cohete
            break;
        case "rechazado":
            $titulo      = "¡Liberación Rechazada!";
            $ctaText     = "Realizar Acción";
            $codigo      = "5";
            $headerColor = "#dc3545";  // Se mantiene el rojo (o se podría ajustar)
            $icon        = "&#x274C;";  // Cruz
            break;
        case "consultaReiniciada":
            $titulo      = "¡Nueva Consulta de Liberación Reiniciada!";
            $ctaText     = "Realizar Acción";
            $codigo      = "2.1";
            $headerColor = "#fd7e14";  // Naranja, por defecto
            $icon        = "&#x1F501;"; // Símbolo de reinicio
            break;
        default:
            $titulo      = "Notificación de Liberación";
            $ctaText     = "Ver Detalles";
            $codigo      = "0";
            $headerColor = "#0097A7";
            $icon        = "&#x2139;";
            break;
    }

    // Sección de contenido con cabecera personalizada según el tipo
    $content = <<<HTML
<div class="header" style="background: $headerColor;">
    <h1>$icon $titulo</h1>
</div>
<div class="content">
    <p>Se detallan los datos asociados:</p>
    <div class="details">
        <p><strong>Nombre:</strong> {$datos['name']}</p>
        <p><strong>Celular:</strong> {$datos['celular']}</p>
        <p><strong>IMEI:</strong> {$datos['imei']}</p>
        <p><strong>Modelo:</strong> {$datos['modelo']}</p>
HTML;

    // Se agregan opcionalmente Precio, Tiempo y Observaciones si están definidos
    if (isset($datos['precio']) && $datos['precio'] !== "") {
        $content .= "<p><strong>Precio:</strong> {$datos['precio']}</p>";
    }
    if (isset($datos['tiempo']) && $datos['tiempo'] !== "") {
        $content .= "<p><strong>Tiempo:</strong> {$datos['tiempo']}</p>";
    }
    if (isset($datos['observaciones']) && $datos['observaciones'] !== "") {
        $content .= "<p><strong>Observaciones:</strong> {$datos['observaciones']}</p>";
    }
    $content .= <<<HTML
    </div>
    <div class="cta">
        <a href="https://app.ithinkguatemala.com/home/pages/liberaciones/buscar.php?search={$datos['imei']}" target="_blank">$ctaText</a>
    </div>
</div>
HTML;

    // Reemplazar el placeholder {codigo} en el footer
    $html_footer = str_replace("{codigo}", $codigo, $html_footer);

    // Unir todas las partes para formar el correo completo
    $correo = $html_header . $content . $html_footer;
    return $correo;
}
?>
