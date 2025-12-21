<?php
require '../cros.php';
require '../controllers/nicho_controlador.php';
// require '../api/nichos_api.php';

$nicho_controlador = new Nicho_controlador();
$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true);
$action = $_GET['action'] ?? null;

$files = $_FILES;


switch ($method) {
    case 'POST':

        if ($action === 'agregar_nicho') {

            $data = json_decode($_POST['data'], true);
            $imagen = $_FILES['imagen'] ?? null;

            if ($data && $imagen) {

                require_once __DIR__ . '/../services/s3service.php';
                $s3 = new S3Service();
                $url_imagen = $s3->uploadImage($imagen);

                $nicho_controlador->crear_nicho_controlador(
                    $data['nombre'],
                    $data['tipo'],
                    $data['usuario_ID'],
                    $data['categorySlug'],
                    $data['descripcion'],
                    $url_imagen
                );

            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Faltan datos o imagen.'
                ]);
            }
        }


        break;
    case 'PUT':
        if ($action === 'editar_nicho') {
            if (isset($data['id'])) {
                $id = $data['id'];
                $nombre = $data['nombre'] ?? null;
                $categorySlug = $data['categorySlug'] ?? null;
                $tipo = $data['tipo'] ?? null;
                $nicho_controlador->editar_nicho_controlador($id, $nombre, $categorySlug, $tipo);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Falta el ID del nicho.']);
            }
        }
        break;

    case 'DELETE':
        if ($action === 'eliminar_nicho') {
            if (isset($data['id'])) {
                $nicho_controlador->eliminar_nicho_controlador($data['id']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Falta el ID del nicho.']);
            }
        }
        break;

    case 'GET':
        if ($action === 'obtener_nichos') {
            $nicho_controlador->obtener_nichos_controlador();
        } else {
            echo json_encode(['status' => 'Error', 'message' => 'Acción GET no reconocida']);
        }
        break;

    default:
        echo json_encode([
            'status' => 'Desconocido',
            'message' => 'Metodo no reconocido'
        ]);
        break;
}

?>