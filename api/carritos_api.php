<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

// Manejar preflight (IMPORTANTE)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require '../controllers/carrito_controlador.php';

$carrito_controlador = new Carrito_controlador();
$data = json_decode(file_get_contents('php://input'), true);
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? null;

switch ($method) {
    case 'POST':
        if ($action === 'agregar_producto_carrito') {
            if (isset($data['usuario_ID'], $data['producto_ID'])) {
                $usuario_ID = $data['usuario_ID'];
                $producto_ID = $data['producto_ID'];

                $carrito_controlador->agregar_producto_carrito_controlador($usuario_ID, $producto_ID);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Faltan datos obligatorios .'
                ]);
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'mensaje' => 'Accion POST no reconocido.'
            ]);
        }
        break;

    case 'DELETE':
        if ($action === 'eliminar_producto_carrito') {
            if (isset($data['usuario_ID'], $data['producto_ID'])) {
                $carrito_controlador->eliminar_producto_carrito_controlador($data['usuario_ID'], $data['producto_ID']);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'mensaje' => 'Faltan datos obligatorios.'
                ]);
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'mensaje' => 'Accion DELETE no reconocido.'
            ]);
        }
        break;


    default:
        echo json_encode(
            [
                'status' => 'error',
                'mensaje' => 'Metodo no reconocido.'
            ]
        );
        break;
}

?>