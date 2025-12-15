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

require '../controllers/pedido_comprar_controlador.php';

$pedido_controlador = new Pedido_controlador();
$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents("php://input"), true);
$action = $_GET['action'] ?? '';

switch ($method) {

    case 'POST':

        if ($action === 'crear_desde_carrito') {

            echo json_encode($pedido_controlador->crear_desde_carrito($data['usuario_ID']));

        } else if ($action === 'crear_directo') {
            $usuario_ID = $data['usuario_ID'] ?? null;
            $producto_ID = $data['producto_ID'] ?? null;
            $cantidad = $data['cantidad'] ?? 1;

            if ($usuario_ID && $producto_ID) {
                $pedido_controlador->crear_pedido_directo_controlador($usuario_ID, $producto_ID, $cantidad);
            } else {
                echo json_encode(['status' => 'error', 'mensaje' => 'Faltan datos para crear pedido directo.']);
            }
        }
        break;

    case 'GET':
        if ($action === 'consultar_pedido') {
            if (isset($data['usuario_ID'])) {
                $usuario_ID = $data['usuario_ID'] ?? null;

                $pedido_controlador->obtener_pedidos_por_usuario_controlador($usuario_ID);
            } else {
                echo json_encode(['status' => 'error', 'mensaje' => 'Falta el usuario_ID.']);
            }
        } else if ($action === 'consultar_carrito') {
            if (isset($data['usuario_ID'])) {
                $usuario_ID = $data['usuario_ID'] ?? null;

                $pedido_controlador->obtener_carritos_por_usuario_controlador($usuario_ID);
            } else {
                echo json_encode(['status' => 'error', 'mensaje' => 'Falta el usuario_ID.']);
            }
        }
        break;

    default:
        echo json_encode(['status' => 'error', 'mensaje' => 'Método no permitido.']);
}
?>