<?php
/**
 * Función auxiliar para mapear tiempo de uso
 */
function obtenerTiempoUsaTexto($tiempo) {
    $tiempos = array(
        '0-3' => '0 a 3 meses',
        '3-6' => '3 a 6 meses',
        '6-12' => '6 a 12 meses',
        '12+' => 'Más de 12 meses',
        'nunca' => 'Nunca usado en USA'
    );
    return isset($tiempos[$tiempo]) ? $tiempos[$tiempo] : $tiempo;
}

/**
 * Genera el cuerpo del correo HTML según el tipo de notificación y los datos proporcionados.
 *
 * @param string $tipo   Tipo de correo
 * @param array  $datos  Arreglo asociativo con la información necesaria
 * @return string        Cadena con el contenido HTML del correo
 */
function correo_enviar($tipo, $datos)
{
    // Cabecera HTML y estilos base mejorados
    $html_header = '<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Notificación de Liberación</title>
<style>
    body, p, div { margin: 0; padding: 0; }
    body {
        background-color: #f4f4f4;
        font-family: Helvetica Neue, Helvetica, Arial, sans-serif;
        color: #333;
        line-height: 1.6;
        padding: 20px;
    }
    .container {
        max-width: 700px;
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
        background: #fafafa;
        border-radius: 8px;
        overflow: hidden;
    }
    .details-table tr {
        border-bottom: 1px solid #e0e0e0;
    }
    .details-table tr:last-child {
        border-bottom: none;
    }
    .details-table td {
        padding: 12px 15px;
        vertical-align: top;
    }
    .details-table td:first-child {
        font-weight: bold;
        color: #667eea;
        width: 40%;
        background: #f0f0f0;
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
    .badge-primary {
        background-color: #cce5ff;
        color: #004085;
    }
    .security-checks {
        background: #e8f5e9;
        padding: 15px;
        border-radius: 8px;
        margin: 15px 0;
        border: 2px solid #4caf50;
    }
    .security-item {
        padding: 8px 0;
        display: flex;
        align-items: center;
        font-size: 15px;
    }
    .security-item .check-icon {
        color: #28a745;
        font-size: 24px;
        margin-right: 10px;
        font-weight: bold;
    }
    .info-highlight {
        background: #e3f2fd;
        padding: 15px;
        border-radius: 8px;
        margin: 15px 0;
        border-left: 4px solid #2196f3;
    }
    .summary-box {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        border-radius: 8px;
        margin: 20px 0;
    }
    .summary-box h3 {
        margin: 0 0 15px 0;
        font-size: 20px;
    }
    .summary-item {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid rgba(255,255,255,0.2);
    }
    .summary-item:last-child {
        border-bottom: none;
    }
    .summary-value {
        font-weight: bold;
        font-size: 18px;
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
        display: inline-block;
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
        font-family: Courier New, monospace;
        font-weight: bold;
    }
    .observaciones-box {
        background: #fff3cd;
        border-left: 4px solid #ffc107;
        padding: 15px;
        margin: 15px 0;
        border-radius: 4px;
    }
    .observaciones-box strong {
        color: #856404;
        font-size: 16px;
    }
    @media (max-width: 600px) {
        .container { width: 100%; border-radius: 0; }
        .header h1 { font-size: 22px; }
        .header .icon { font-size: 40px; }
        .cta a { padding: 12px 24px; font-size: 15px; }
        .details-table td { font-size: 14px; padding: 10px; }
        .content { padding: 20px 15px; }
    }
</style>
</head>
<body>
<div class="container">';

    // Pie de página
    $html_footer = '<div class="footer">
    <p>Este es un correo automático, por favor no responder.</p>
    <p><strong>iThink Guatemala</strong> - Sistema de Liberaciones</p>
    <p class="code">Código: {codigo}</p>
    <p style="margin-top: 10px; opacity: 0.8;">Fecha: ' . date('d/m/Y H:i:s') . '</p>
</div>
</div>
</body>
</html>';

    // Definir variables según el tipo de notificación
    $titulo = '';
    $ctaText = '';
    $codigo = '';
    $headerColor = '';
    $icon = '';
    
    switch ($tipo) {
        case "consulta":
            $titulo = "Nueva Consulta de Liberación";
            $ctaText = "Informar Consulta";
            $codigo = "0";
            $headerColor = "linear-gradient(135deg, #0097A7 0%, #00ACC1 100%)";
            $icon = "&#x1F4CB;";
            break;
        case "pendiente":
            $titulo = "Liberación Cambió a Pendiente";
            $ctaText = "Realizar Acción";
            $codigo = "1";
            $headerColor = "linear-gradient(135deg, #D32F2F 0%, #F44336 100%)";
            $icon = "&#x23F3;";
            break;
        case "aprobado":
            $titulo = "Liberación Aprobada por Cliente";
            $ctaText = "Ver Detalles";
            $codigo = "2";
            $headerColor = "linear-gradient(135deg, #388E3C 0%, #4CAF50 100%)";
            $icon = "&#x2705;";
            break;
        case "finalizado":
            $titulo = "Proceso de Liberación Finalizado";
            $ctaText = "Ver Resultados";
            $codigo = "4";
            $headerColor = "linear-gradient(135deg, #F57C00 0%, #FFA726 100%)";
            $icon = "&#x1F3C1;";
            break;
        case "iniciado":
            $titulo = "Inicio de Proceso de Liberación";
            $ctaText = "Ver Progreso";
            $codigo = "3";
            $headerColor = "linear-gradient(135deg, #1976D2 0%, #2196F3 100%)";
            $icon = "&#x1F680;";
            break;
        case "rechazado":
            $titulo = "Liberación Rechazada";
            $ctaText = "Realizar Acción";
            $codigo = "5";
            $headerColor = "linear-gradient(135deg, #C62828 0%, #E53935 100%)";
            $icon = "&#x274C;";
            break;
        case "consultaReiniciada":
            $titulo = "Consulta de Liberación Reiniciada";
            $ctaText = "Realizar Acción";
            $codigo = "2.1";
            $headerColor = "linear-gradient(135deg, #F57C00 0%, #FF9800 100%)";
            $icon = "&#x1F501;";
            break;
        default:
            $titulo = "Notificación de Liberación";
            $ctaText = "Ver Detalles";
            $codigo = "0";
            $headerColor = "linear-gradient(135deg, #667eea 0%, #764ba2 100%)";
            $icon = "&#x1F4E7;";
            break;
    }

    // Construir contenido - HEADER
    $content = '<div class="header" style="background: ' . $headerColor . ';">
    <span class="icon">' . $icon . '</span>
    <h1>' . $titulo . '</h1>
</div>
<div class="content">
    <p style="font-size: 17px;">Se ha registrado una actualización en el sistema de liberaciones. A continuación se presenta la <strong>información completa</strong> del proceso:</p>';

    // RESUMEN DESTACADO
    if (isset($datos['precio']) && $datos['precio'] > 0 && isset($datos['tiempo']) && $datos['tiempo'] > 0) {
        $content .= '<div class="summary-box">
        <h3>📊 Resumen del Servicio</h3>
        <div class="summary-item">
            <span>Precio del Servicio:</span>
            <span class="summary-value">Q ' . number_format($datos['precio'], 2) . '</span>
        </div>
        <div class="summary-item">
            <span>Tiempo Estimado:</span>
            <span class="summary-value">' . htmlspecialchars($datos['tiempo']) . ' días</span>
        </div>';
        
        if (isset($datos['estado'])) {
            $content .= '<div class="summary-item">
            <span>Estado Actual:</span>
            <span class="summary-value">' . htmlspecialchars($datos['estado']) . '</span>
        </div>';
        }
        
        $content .= '</div>';
    }
    
    // SECCIÓN: INFORMACIÓN DEL CLIENTE
    $content .= '<div class="section-title">
        <span class="icon">👤</span> Información del Cliente
    </div>
    <table class="details-table">
        <tr>
            <td>Nombre Completo:</td>
            <td><strong>' . htmlspecialchars($datos['name']) . '</strong></td>
        </tr>
        <tr>
            <td>Número de Celular:</td>
            <td><strong>+502 ' . htmlspecialchars($datos['celular']) . '</strong></td>
        </tr>';
    
    if (isset($datos['tienda']) && $datos['tienda']) {
        $content .= '<tr>
            <td>Tienda:</td>
            <td>' . htmlspecialchars($datos['tienda']) . '</td>
        </tr>';
    }
    
    $content .= '</table>';
    
    // SECCIÓN: INFORMACIÓN DEL EQUIPO
    $content .= '<div class="section-title">
        <span class="icon">📱</span> Información del Equipo
    </div>
    <table class="details-table">
        <tr>
            <td>IMEI Principal:</td>
            <td><strong style="font-size: 16px;">' . htmlspecialchars($datos['imei']) . '</strong></td>
        </tr>';

    // IMEI secundario
    if (isset($datos['imei2']) && $datos['imei2'] !== "" && $datos['imei2'] !== null) {
        $content .= '<tr>
            <td>IMEI Secundario:</td>
            <td><strong style="font-size: 16px;">' . htmlspecialchars($datos['imei2']) . '</strong> <span class="badge badge-info">Dual SIM</span></td>
        </tr>';
    }

    $content .= '<tr>
            <td>Modelo:</td>
            <td><strong>' . htmlspecialchars($datos['modelo']) . '</strong></td>
        </tr>';

    // Tiempo de uso en USA
    if (isset($datos['tiempo_usa']) && $datos['tiempo_usa'] !== "" && $datos['tiempo_usa'] !== null) {
        $tiempoTexto = obtenerTiempoUsaTexto($datos['tiempo_usa']);
        $badgeClass = ($datos['tiempo_usa'] === 'nunca' || $datos['tiempo_usa'] === '0-3') ? 'badge-success' : 'badge-warning';
        $content .= '<tr>
            <td>Tiempo en USA:</td>
            <td>' . htmlspecialchars($tiempoTexto) . ' <span class="badge ' . $badgeClass . '">USA</span></td>
        </tr>';
    }

    $content .= '</table>';

    // VERIFICACIONES DE SEGURIDAD
    if ((isset($datos['no_blacklist']) && $datos['no_blacklist'] == 1) || 
        (isset($datos['no_icloud']) && $datos['no_icloud'] == 1)) {
        
        $content .= '<div class="section-title">
        <span class="icon">🔒</span> Verificaciones de Seguridad
    </div>
    <div class="security-checks">';

        if (isset($datos['no_blacklist']) && $datos['no_blacklist'] == 1) {
            $content .= '<div class="security-item">
            <span class="check-icon">✓</span>
            <span>Equipo <strong>NO</strong> está en Blacklist</span>
        </div>';
        }

        if (isset($datos['no_icloud']) && $datos['no_icloud'] == 1) {
            $content .= '<div class="security-item">
            <span class="check-icon">✓</span>
            <span>Equipo <strong>NO</strong> tiene iCloud activo</span>
        </div>';
        }

        $content .= '</div>';
    }

    // DETALLES DEL SERVICIO
    if ((isset($datos['precio']) && $datos['precio'] > 0) || (isset($datos['tiempo']) && $datos['tiempo'] > 0) || isset($datos['estado'])) {
        $content .= '<div class="section-title">
        <span class="icon">💼</span> Detalles del Servicio
    </div>
    <table class="details-table">';

        if (isset($datos['precio']) && $datos['precio'] > 0) {
            $content .= '<tr>
            <td>Precio:</td>
            <td><strong style="color: #28a745; font-size: 18px;">Q ' . number_format($datos['precio'], 2) . '</strong></td>
        </tr>';
        }

        if (isset($datos['tiempo']) && $datos['tiempo'] > 0) {
            $content .= '<tr>
            <td>Tiempo Estimado:</td>
            <td><strong>' . htmlspecialchars($datos['tiempo']) . ' días hábiles</strong></td>
        </tr>';
        }

        if (isset($datos['estado'])) {
            $content .= '<tr>
            <td>Estado:</td>
            <td><span class="badge badge-primary">' . htmlspecialchars($datos['estado']) . '</span></td>
        </tr>';
        }

        if (isset($datos['fecha_creacion'])) {
            $content .= '<tr>
            <td>Fecha Creación:</td>
            <td>' . date('d/m/Y H:i', strtotime($datos['fecha_creacion'])) . '</td>
        </tr>';
        }

        if (isset($datos['fecha_actualizacion'])) {
            $content .= '<tr>
            <td>Última Actualización:</td>
            <td>' . date('d/m/Y H:i', strtotime($datos['fecha_actualizacion'])) . '</td>
        </tr>';
        }

        $content .= '</table>';
    }

    // OBSERVACIONES
    if (isset($datos['observaciones']) && $datos['observaciones'] !== "" && $datos['observaciones'] !== null) {
        $content .= '<div class="observaciones-box">
        <strong>📝 Observaciones:</strong><br><br>
        <div style="font-size: 15px;">' . nl2br(htmlspecialchars($datos['observaciones'])) . '</div>
    </div>';
    }

    // BOTÓN
    $content .= '<div class="cta">
        <a href="https://app.ithinkguatemala.com/home/pages/liberaciones/buscar.php?search=' . urlencode($datos['imei']) . '" target="_blank">' . htmlspecialchars($ctaText) . '</a>
    </div>
</div>';

    // Reemplazar código
    $html_footer = str_replace('{codigo}', $codigo, $html_footer);

    return $html_header . $content . $html_footer;
}

// Función para obtener datos completos de una liberación
function obtenerDatosLiberacion($conexion, $imei) {
    $sql = "SELECT 
                l.serie,
                l.serie_2,
                l.Nombre_Cliente,
                l.Celular,
                l.modelo,
                l.tiempo_usa,
                l.no_blacklist,
                l.no_icloud,
                l.Observaciones,
                l.Precio,
                l.Tiempo,
                l.cod_estado,
                l.date,
                l.date_update,
                t.nombre as tienda,
                e.estado as nombre_estado
            FROM liberacion l
            LEFT JOIN tiendas t ON l.cod_tienda = t.cod_tienda
            LEFT JOIN estados e ON l.cod_estado = e.cod_estado
            WHERE l.serie = ?";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $imei);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        return array(
            'imei' => $row['serie'],
            'imei2' => $row['serie_2'],
            'name' => $row['Nombre_Cliente'],
            'celular' => $row['Celular'],
            'modelo' => $row['modelo'],
            'tiempo_usa' => $row['tiempo_usa'],
            'no_blacklist' => $row['no_blacklist'],
            'no_icloud' => $row['no_icloud'],
            'observaciones' => $row['Observaciones'],
            'precio' => $row['Precio'],
            'tiempo' => $row['Tiempo'],
            'tienda' => $row['tienda'],
            'estado' => $row['nombre_estado'],
            'fecha_creacion' => $row['date'],
            'fecha_actualizacion' => $row['date_update']
        );
    }
    
    return null;
}

// Función para enviar correo con todos los datos
function enviarCorreoLiberacion($tipo, $imei, $conexion) {
    // Obtener datos completos
    $datos = obtenerDatosLiberacion($conexion, $imei);
    
    if (!$datos) {
        error_log("No se encontraron datos para el IMEI: $imei");
        return false;
    }
    
    // Generar cuerpo del correo
    $cuerpoCorreo = correo_enviar($tipo, $datos);
    
    // Configuración del correo
    $para = "destinatario@example.com";
    $asunto = obtenerAsuntoCorreo($tipo, $imei);
    
    // Headers
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: Sistema iThink <sistema@ithinkguatemala.com>\r\n";
    $headers .= "Reply-To: no-reply@ithinkguatemala.com\r\n";
    
    // Enviar
    $resultado = mail($para, $asunto, $cuerpoCorreo, $headers);
    
    if ($resultado) {
        error_log("Correo enviado: $imei - $tipo");
    } else {
        error_log("Error al enviar correo: $imei - $tipo");
    }
    
    return $resultado;
}

// Función para obtener asunto
function obtenerAsuntoCorreo($tipo, $imei) {
    $asuntos = array(
        'consulta' => 'Nueva Consulta de Liberación',
        'pendiente' => 'Liberación Cambió a Pendiente',
        'aprobado' => 'Liberación Aprobada por Cliente',
        'finalizado' => 'Proceso de Liberación Finalizado',
        'iniciado' => 'Inicio de Proceso de Liberación',
        'rechazado' => 'Liberación Rechazada',
        'consultaReiniciada' => 'Consulta de Liberación Reiniciada'
    );
    
    $asunto = isset($asuntos[$tipo]) ? $asuntos[$tipo] : 'Notificación de Liberación';
    return $asunto . ' - IMEI: ' . $imei;
}
?>