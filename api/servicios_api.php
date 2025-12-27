<?php
require '../cros.php';
require_once '../controllers/servicio_controlador.php';

$servicio_controlador = new Servicio_controlador();

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? null;

switch ($method) {

    case 'POST':
        if ($action === 'agregar_servicio') {

            $data = json_decode($_POST['dataService'], true);

            if ($data && isset($_FILES['imagen'])) {

                require_once '../services/s3service.php';
                $s3 = new S3Service();

                $imagen = $_FILES['imagen'];

                if (!empty($imagen['tmp_name'])) {

                    $url_imagen = $s3->uploadImage($imagen);
                } else {
                    $url_imagen = null;
                }

                $servicio_controlador->crear_servicio_controlador(
                    $data['descripcion_nombre'],
                    $url_imagen,
                    $data['categoria_ID'],
                     $data['nombre_empresa']
                );

            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Faltan datos requeridos para agregar servicio.'
                ]);
            }

        } else {
            echo json_encode([
                'status' => 'error',
                'mensaje' => 'Acción POST no reconocida.'
            ]);
        }
        break;

    case 'GET':
        if ($action === 'obtener_servicios') {
            $servicio_controlador->obtener_servicios_controlador();
        } else {
            echo json_encode(['status' => 'Error', 'message' => 'Acción GET no reconocida']);
        }
        break;

    case 'PUT':
        if ($action === 'editar_servicio') {
            $data = json_decode(file_get_contents('php://input'), true);

            if (isset($data['servicio_ID'])) {
                $servicio_controlador->editar_servicio_controlador($data);
            } else {
                echo json_encode(['status' => 'error', 'mensaje' => 'Debe enviar el servicio_ID']);
            }
        } else {
            echo json_encode(['status' => 'error', 'mensaje' => 'Acción PUT no reconocida.']);
        }
        break;

    case 'DELETE':
        if ($action === 'eliminar_servicio') {
            $data = json_decode(file_get_contents('php://input'), true);

            if (isset($data['servicio_ID'])) {
                $servicio_controlador->eliminar_servicio_controlador($data['servicio_ID']);
            } else {
                echo json_encode(['status' => 'error', 'mensaje' => 'Debe enviar el servicio_ID']);
            }
        }
        break;

    default:
        echo json_encode(['status' => 'error', 'mensaje' => 'Metodo no reconocido.']);
        break;
}
?>