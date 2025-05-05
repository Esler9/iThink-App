<?php
session_start();

// Cargar configuración (por ejemplo, conexión a BD, constantes, etc.)
require_once __DIR__ . '/../config/config.php';

// Cargar el autoloader de Composer si lo usas, o un autoloader propio
require_once __DIR__ . '/../vendor/autoload.php';

// Incluir el Router base
require_once __DIR__ . '/../app/core/Router.php';

// Puedes definir tus rutas directamente o incluir un archivo de rutas
// Ejemplo de definición directa:
$router = new Router();
$router->addRoute('/liberaciones/consultas', 'LiberacionController@consultas');
$router->addRoute('/liberaciones/actualizar', 'LiberacionController@actualizarEstado');

// O incluir el archivo de rutas
// require_once __DIR__ . '/../config/routes.php';

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestUri = rtrim($requestUri, '/');

// Despacha la solicitud a la ruta correspondiente
$router->dispatch($requestUri);
?>