<?php
/**
 * Genera el cuerpo del correo HTML según el tipo de notificación y los datos proporcionados.
 *
 * @param string $tipo   Tipo de correo ("consulta", "pendiente", "aprobado", "rechazado", etc.).
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
    // Cabecera HTML y estilos
    $html_header = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Notificación de Liberación</title>
<style>
    /* Reset y base */
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
        background: #007bff;
        color: #ffffff;
        text-align: center;
        padding: 20px;
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

    // Pie de página, se utilizará un placeholder para el código
    $html_footer = <<<HTML
<div class="footer">
    <p>Este es un correo automático, no responder.</p>
    <p>Código: {codigo}</p>
</div>
</div>
</body>
</html>
HTML;

    // Según el tipo de correo, definimos título, texto del botón y código
    switch ($tipo) {
        case "consulta":
            $titulo  = "¡Nueva Consulta de Liberación!";
            $ctaText = "Informar Consulta";
            $codigo  = "0";
            break;
        case "pendiente":
            $titulo  = "¡Liberación Cambio a Pendiente!";
            $ctaText = "Realizar Acción";
            $codigo  = "1";
            break;
        case "aprobado":
            $titulo  = "¡Liberación Aprobada Por Cliente!";
            $ctaText = "Ver Detalles";
            $codigo  = "2";
            break;
        case "iniciado":
            $titulo  = "¡Inicio de Proceso de Liberación!";
            $ctaText = "Ver Progreso";
            $codigo  = "3";
            break;
        case "finalizado":
            $titulo  = "¡Finalizado el Proceso de Liberación!";
            $ctaText = "Ver Resultados";
            $codigo  = "4";
            break;
        case "rechazado":
            $titulo  = "¡Liberación Rechazada!";
            $ctaText = "Realizar Acción";
            $codigo  = "5";
            break;
        case "consultaReiniciada":
            $titulo  = "¡Nueva Consulta de Liberación Reiniciada!";
            $ctaText = "Realizar Acción";
            $codigo  = "2.1";
            break;
        default:
            $titulo  = "Notificación de Liberación";
            $ctaText = "Ver Detalles";
            $codigo  = "0";
            break;
    }

    // Sección de contenido (cabecera de sección y detalles)
    $content = <<<HTML
<div class="header">
    <h1>$titulo</h1>
</div>
<div class="content">
    <p>Se detallan los datos asociados:</p>
    <div class="details">
        <p><strong>Nombre:</strong> {$datos['name']}</p>
        <p><strong>Celular:</strong> {$datos['celular']}</p>
        <p><strong>IMEI:</strong> {$datos['imei']}</p>
        <p><strong>Modelo:</strong> {$datos['modelo']}</p>
HTML;
    // Se agregan opcionalmente Precio, Tiempo y Observaciones
    if (isset($datos['precio'])) {
        $content .= "<p><strong>Precio:</strong> {$datos['precio']}</p>";
    }
    if (isset($datos['tiempo'])) {
        $content .= "<p><strong>Tiempo:</strong> {$datos['tiempo']}</p>";
    }
    if (isset($datos['observaciones'])) {
        $content .= "<p><strong>Observaciones:</strong> {$datos['observaciones']}</p>";
    }
    $content .= <<<HTML
    </div>
    <div class="cta">
        <a href="https://app.ithinkguatemala.com/home/pages/tables/buscar.php?search={$datos['imei']}" target="_blank">$ctaText</a>
    </div>
</div>
HTML;

    // Reemplazar placeholder {codigo} en el footer
    $html_footer = str_replace("{codigo}", $codigo, $html_footer);

    // Unir todas las partes para formar el correo completo
    $correo = $html_header . $content . $html_footer;
    return $correo;
}
?>
