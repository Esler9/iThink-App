<?php
/**
 * Función auxiliar para mapear tiempo de uso
 */
function obtenerTiempoUsaTexto($tiempo) {
    $tiempos = [
        '0-3' => '0 a 3 meses',
        '3-6' => '3 a 6 meses',
        '6-12' => '6 a 12 meses',
        '12+' => 'Más de 12 meses',
        'nunca' => 'Nunca usado en USA'
    ];
    return isset($tiempos[$tiempo]) ? $tiempos[$tiempo] : $tiempo;
}

/**
 * Genera el cuerpo del correo HTML según el tipo de notificación y los datos proporcionados.
 *
 * @param string $tipo   Tipo de correo ("consulta", "pendiente", "aprobado", "finalizado", "rechazado", "consultaReiniciada", etc.).
 * @param array  $datos  Arreglo asociativo con la información necesaria. Se espera que incluya:
 *                       - 'name'          => Nombre del cliente
 *                       - 'celular'       => Número de celular
 *                       - 'imei'          => IMEI o serie
 *                       - 'imei2'         => IMEI secundario (opcional)
 *                       - 'modelo'        => Modelo del dispositivo
 *                       - 'tiempo_usa'    => Tiempo de uso en USA (opcional)
 *                       - 'no_blacklist'  => Confirmación no blacklist (opcional)
 *                       - 'no_icloud'     => Confirmación no iCloud (opcional)
 *                       Además, opcionalmente:
 *                       - 'precio'        => Precio
 *                       - 'tiempo'        => Tiempo
 *                       - 'observaciones' => Observaciones
 *
 * @return string        Cadena con el contenido HTML del correo.
 */
function correo_enviar($tipo, $datos)
{
    // Cabecera HTML y estilos base mejorados
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
        max-width: 650px;
        background: #ffffff;
        margin: 0 auto;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
    }
    .header {
        text-align: center;
        padding: 30px 20px;
        color: #ffffff;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .header h1 {
        font-size: 26px;
        margin: 0;
        text-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    .header .icon {
        font-size: 48px;
        margin-bottom: 10px;
        display: block;
    }
    .content {
        padding: 30px 25px;
    }
    .content > p {
        margin-bottom: 20px;
        font-size: 16px;
        color: #555;
    }
    .section-title {
        font-size: 18px;
        font-weight: bold;
        color: #333;
        margin: 25px 0 15px 0;
        padding-bottom: 8px;
        border-bottom: 2px solid #667eea;
    }
    .section-title .icon {
        margin-right: 8px;
    }
    .details-table {
        width: 100%;
        border-collapse: collapse;
        margin: 15px 0;
    }
    .details-table tr {
        border-bottom: 1px solid #e0e0e0;
    }
    .details-table tr:last-child {
        border-bottom: none;
    }
    .details-table td {
        padding: 12px 8px;
        vertical-align: top;
    }
    .details-table td:first-child {
        font-weight: bold;
        color: #667eea;
        width: 40%;
    }
    .details-table td:last-child {
        color: #555;
    }
    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: bold;
        margin-left: 8px;
    }
    .badge-success {
        background-color: #d4edda;
        color: #155724;
    }
    .badge-warning {
        background-color: #fff3cd;
        color: #856404;
    }
    .badge-info {
        background-color: #d1ecf1;
        color: #0c5460;
    }
    .security-checks {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin: 15px 0;
    }
    .security-item {
        padding: 8px 0;
        display: flex;
        align-items: center;
    }
    .security-item .check-icon {
        color: #28a745;
        font-size: 20px;
        margin-right: 10px;
    }
    .cta {
        text-align: center;
        margin: 35px 0;
    }
    .cta a {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #ffffff;
        padding: 14px 32px;
        text-decoration: none;
        border-radius: 25px;
        font-weight: bold;
        font-size: 16px;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        transition: all 0.3s ease;
        display: inline-block;
    }
    .cta a:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
    }
    .footer {
        background: #2c3e50;
        text-align: center;
        padding: 20px;
        color: #ecf0f1;
    }
    .footer p {
        margin: 5px 0;
        font-size: 13px;
    }
    .footer .code {
        display: inline-block;
        background: #34495e;
        padding: 5px 15px;
        border-radius: 15px;
        margin-top: 10px;
        font-family: 'Courier New', monospace;
        font-weight: bold;
    }
    .observaciones-box {
        background: #fff3cd;
        border-left: 4px solid #ffc107;
        padding: 12px 15px;
        margin: 15px 0;
        border-radius: 4px;
    }
    .observaciones-box strong {
        color: #856404;
    }
    @media (max-width: 600px) {
        .container { width: 100%; border-radius: 0; }
        .header h1 { font-size: 22px; }
        .header .icon { font-size: 40px; }
        .cta a { padding: 12px 24px; font-size: 15px; }
        .details-table td { font-size: 14px; }
        .content { padding: 20px 15px; }
    }
</style>
</head>
<body>
<div class="container">
HTML;

    // Pie de página mejorado
    $html_footer = <<<HTML
<div class="footer">
    <p>Este es un correo automático, por favor no responder.</p>
    <p><strong>iThink Guatemala</strong> - Sistema de Liberaciones</p>
    <p class="code">Código: {codigo}</p>
</div>
</div>
</body>
</html>
HTML;

    // Definir variables según el tipo de notificación
    switch ($tipo) {
        case "consulta":
            $titulo      = "Nueva Consulta de Liberación";
            $ctaText     = "📋 Informar Consulta";
            $codigo      = "0";
            $headerColor = "linear-gradient(135deg, #0097A7 0%, #00ACC1 100%)";
            $icon        = "&#x1F4CB;";  // 📋 Clipboard
            break;
        case "pendiente":
            $titulo      = "Liberación Cambió a Pendiente";
            $ctaText     = "⚡ Realizar Acción";
            $codigo      = "1";
            $headerColor = "linear-gradient(135deg, #D32F2F 0%, #F44336 100%)";
            $icon        = "&#x23F3;";  // ⏳ Reloj de arena
            break;
        case "aprobado":
            $titulo      = "Liberación Aprobada por Cliente";
            $ctaText     = "✅ Ver Detalles";
            $codigo      = "2";
            $headerColor = "linear-gradient(135deg, #388E3C 0%, #4CAF50 100%)";
            $icon        = "&#x2705;";  // ✅ Marca de verificación
            break;
        case "finalizado":
            $titulo      = "Proceso de Liberación Finalizado";
            $ctaText     = "🎉 Ver Resultados";
            $codigo      = "4";
            $headerColor = "linear-gradient(135deg, #F57C00 0%, #FFA726 100%)";
            $icon        = "&#x1F3C1;"; // 🏁 Bandera a cuadros
            break;
        case "iniciado":
            $titulo      = "Inicio de Proceso de Liberación";
            $ctaText     = "🚀 Ver Progreso";
            $codigo      = "3";
            $headerColor = "linear-gradient(135deg, #1976D2 0%, #2196F3 100%)";
            $icon        = "&#x1F680;"; // 🚀 Cohete
            break;
        case "rechazado":
            $titulo      = "Liberación Rechazada";
            $ctaText     = "❌ Realizar Acción";
            $codigo      = "5";
            $headerColor = "linear-gradient(135deg, #C62828 0%, #E53935 100%)";
            $icon        = "&#x274C;";  // ❌ Cruz
            break;
        case "consultaReiniciada":
            $titulo      = "Consulta de Liberación Reiniciada";
            $ctaText     = "🔄 Realizar Acción";
            $codigo      = "2.1";
            $headerColor = "linear-gradient(135deg, #F57C00 0%, #FF9800 100%)";
            $icon        = "&#x1F501;"; // 🔁 Símbolo de reinicio
            break;
        default:
            $titulo      = "Notificación de Liberación";
            $ctaText     = "👁️ Ver Detalles";
            $codigo      = "0";
            $headerColor = "linear-gradient(135deg, #667eea 0%, #764ba2 100%)";
            $icon        = "&#x1F4E7;"; // 📧 Correo
            break;
    }

    // Sección de contenido con diseño mejorado
    $content = <<<HTML
<div class="header" style="background: $headerColor;">
    <span class="icon">$icon</span>
    <h1>$titulo</h1>
</div>
<div class="content">
    <p>Se ha registrado una actualización en el sistema de liberaciones. A continuación, se detallan los datos asociados:</p>
    
    <div class="section-title">
        <span class="icon">👤</span> Información del Cliente
    </div>
    <table class="details-table">
        <tr>
            <td>Nombre:</td>
            <td>{$datos['name']}</td>
        </tr>
        <tr>
            <td>Celular:</td>
            <td>+502 {$datos['celular']}</td>
        </tr>
    </table>
    
    <div class="section-title">
        <span class="icon">📱</span> Información del Equipo
    </div>
    <table class="details-table">
        <tr>
            <td>IMEI Principal:</td>
            <td><strong>{$datos['imei']}</strong></td>
        </tr>
HTML;

    // IMEI secundario si existe
    if (isset($datos['imei2']) && $datos['imei2'] !== "" && $datos['imei2'] !== null) {
        $content .= <<<HTML
        <tr>
            <td>IMEI Secundario:</td>
            <td><strong>{$datos['imei2']}</strong> <span class="badge badge-info">Dual SIM</span></td>
        </tr>
HTML;
    }

    $content .= <<<HTML
        <tr>
            <td>Modelo:</td>
            <td>{$datos['modelo']}</td>
        </tr>
HTML;

    // Tiempo de uso en USA
    if (isset($datos['tiempo_usa']) && $datos['tiempo_usa'] !== "" && $datos['tiempo_usa'] !== null) {
        $tiempoTexto = obtenerTiempoUsaTexto($datos['tiempo_usa']);
        $badgeClass = ($datos['tiempo_usa'] === 'nunca' || $datos['tiempo_usa'] === '0-3') ? 'badge-success' : 'badge-warning';
        $content .= <<<HTML
        <tr>
            <td>Tiempo en USA:</td>
            <td>$tiempoTexto <span class="badge $badgeClass">USA</span></td>
        </tr>
HTML;
    }

    $content .= "</table>";

    // Verificaciones de Seguridad
    if ((isset($datos['no_blacklist']) && $datos['no_blacklist'] == 1) || 
        (isset($datos['no_icloud']) && $datos['no_icloud'] == 1)) {
        
        $content .= <<<HTML
    <div class="section-title">
        <span class="icon">🔒</span> Verificaciones de Seguridad
    </div>
    <div class="security-checks">
HTML;

        if (isset($datos['no_blacklist']) && $datos['no_blacklist'] == 1) {
            $content .= <<<HTML
        <div class="security-item">
            <span class="check-icon">✓</span>
            <span>Equipo <strong>NO</strong> está en Blacklist (sin reporte de pérdida/robo)</span>
        </div>
HTML;
        }

        if (isset($datos['no_icloud']) && $datos['no_icloud'] == 1) {
            $content .= <<<HTML
        <div class="security-item">
            <span class="check-icon">✓</span>
            <span>Equipo <strong>NO</strong> tiene cuenta iCloud activa</span>
        </div>
HTML;
        }

        $content .= "</div>";
    }

    // Información de Precio y Tiempo si están disponibles
    if ((isset($datos['precio']) && $datos['precio'] !== "" && $datos['precio'] > 0) || 
        (isset($datos['tiempo']) && $datos['tiempo'] !== "" && $datos['tiempo'] > 0)) {
        
        $content .= <<<HTML
    <div class="section-title">
        <span class="icon">💰</span> Detalles de Servicio
    </div>
    <table class="details-table">
HTML;

        if (isset($datos['precio']) && $datos['precio'] !== "" && $datos['precio'] > 0) {
            $content .= <<<HTML
        <tr>
            <td>Precio:</td>
            <td><strong>Q {$datos['precio']}</strong></td>
        </tr>
HTML;
        }

        if (isset($datos['tiempo']) && $datos['tiempo'] !== "" && $datos['tiempo'] > 0) {
            $content .= <<<HTML
        <tr>
            <td>Tiempo Estimado:</td>
            <td>{$datos['tiempo']} días hábiles</td>
        </tr>
HTML;
        }

        $content .= "</table>";
    }

    // Observaciones si existen
    if (isset($datos['observaciones']) && $datos['observaciones'] !== "" && $datos['observaciones'] !== null) {
        $content .= <<<HTML
    <div class="observaciones-box">
        <strong>📝 Observaciones:</strong><br>
        {$datos['observaciones']}
    </div>
HTML;
    }

    $content .= <<<HTML
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
