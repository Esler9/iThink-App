<?php
include("../../../conexion.php");
include('../correo.php');
include('../funcion_logica.php');
include("../diseño_correos.php");

// Bloque de debug simple: muestra alert si recibe POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['__debug_post_check']) || isset($_GET['debug_post']))) {
    $post_pretty = json_encode($_POST, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    $post_js = json_encode($post_pretty);
    echo '<!doctype html><html><head><meta charset="utf-8"><title>Debug POST</title></head><body>';
    echo '<script>';
    echo "alert('POST recibido:\\n\\n' + {$post_js});";
    echo "history.back();";
    echo '</script></body></html>';
    exit;
}
?>