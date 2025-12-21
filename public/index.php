<?php
require '../cros.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Limpia la URL
$uri = rtrim($uri, '/');

$routes = [
    '/nichos'     => '../api/nichos_api.php',
    '/productos'  => '../api/productos_api.php',
    '/servicios'  => '../api/servicios_api.php',
    '/usuarios'   => '../api/usuarios_api.php',
    '/pedido'     => '../api/pedido_compra_api.php',
];

// Ruta no encontrada
if (!isset($routes[$uri])) {
    http_response_code(404);
    echo json_encode([
        "success" => false,
        "error" => "Endpoint no encontrado"
    ]);
    exit;
}

// Redirige internamente
require $routes[$uri];
