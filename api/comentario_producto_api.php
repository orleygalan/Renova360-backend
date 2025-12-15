<?php
// require './cros.php';
require '../controllers/comentario_producto_controlador.php';


$comentario_controlador = new Comentario_producto_controlador();
$data = json_decode(file_get_contents("php://input"), true);
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? null;

switch ($method) {
    case 'POST':
        if ($action === 'agregar_comentario_producto') {
            if (isset($data['usuario_ID'], $data['comentario'], $data['producto_ID'])) {

                $usuario_ID = $data['usuario_ID'];
                $comentario = trim($data['comentario']);
                $producto_ID = $data['producto_ID'];

                $comentario_controlador->agregar_comentario_controlador($usuario_ID, $comentario, $producto_ID);

            } else {
                echo json_encode([
                    'status' => 'error',
                    'mensaje' => 'Faltan datos obligatorios.'
                ]);
            }
        } else {
            echo json_encode(['status' => 'error', 'mensaje' => 'Acción POST no reconocida.']);
        }
        break;

    case 'DELETE':
        if ($action === 'eliminar_comentario_producto') {

            if (isset($data['comentario_ID'], $data['usuario_ID'], $data['rol'])) {

                $comentario_ID = $data['comentario_ID'] ?? null;
                $usuario_ID = $data['usuario_ID'] ?? null;
                $rol = strtolower($data['rol'] ?? 'usuario');
                $es_admin = ($rol === 'administrador');

                $comentario_controlador->eliminar_comentario_controlador($comentario_ID, $usuario_ID, $es_admin);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'mensaje' => 'Faltan datos requeridos.'
                ]);
            }
        }
        break;


    default:
        echo json_encode(['status' => 'error', 'mensaje' => 'Metodo no reconocido.']);
        break;
}

?>