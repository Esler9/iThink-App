<?php
include("../../../conexion.php");

// Simplificado: si hay POST muestra un HTML con los datos recibidos y no redirige a ningún lado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: text/html; charset=utf-8');
    $post_pretty = json_encode($_POST, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    // escapamos para mostrar de forma segura en HTML
    $post_html = htmlspecialchars($post_pretty, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

    echo '<!doctype html><html><head><meta charset="utf-8"><title>POST recibido</title></head><body>';
    echo '<h1>POST recibido</h1>';
    echo '<p>Contenido de $_POST:</p>';
    echo '<pre>' . $post_html . '</pre>';
    echo '</body></html>';
    exit;
}

// Si no hay POST muestra mensaje mínimo (opcional)
echo '<!doctype html><html><head><meta charset="utf-8"><title>No POST</title></head><body>';
echo '<p>No se ha recibido ningún POST.</p>';
echo '</body></html>';
?>
