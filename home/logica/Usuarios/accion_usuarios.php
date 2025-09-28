<?php
include("../../../conexion.php");
include('../correo.php');
include('../funcion_logica.php');
include("../diseño_correos.php");

// No redirigir: función de respuesta HTML simple
$no_redirect = true;
function respond($title, $message = '', $post = []) {
    header('Content-Type: text/html; charset=utf-8');
    $post_pretty = $post ? json_encode($post, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '';
    echo '<!doctype html><html><head><meta charset="utf-8"><title>' . htmlspecialchars($title) . '</title></head><body>';
    echo '<h1>' . htmlspecialchars($title) . '</h1>';
    if ($message !== '') echo '<p>' . nl2br(htmlspecialchars($message)) . '</p>';
    if ($post_pretty !== '') {
        echo '<h2>POST recibido</h2><pre>' . htmlspecialchars($post_pretty, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</pre>';
    }
    echo '<p><button onclick="history.back()" type="button">Volver</button></p>';
    echo '</body></html>';
    exit;
}

// Simplificado: si hay POST muestra un HTML con los datos recibidos y no redirige a ningún lado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    respond('Datos recibidos', '', $_POST);
}

// Si no hay POST muestra mensaje mínimo (opcional)
echo '<!doctype html><html><head><meta charset="utf-8"><title>No POST</title></head><body>';
echo '<p>No se ha recibido ningún POST.</p>';
echo '</body></html>';
?>
