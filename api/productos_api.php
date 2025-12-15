<?php
// require './cros.php';
require_once '../controllers/producto_controlador.php';

$producto_controlador = new Producto_controlador();
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? null;


function reArrayFiles($file_post)
{
    $files = [];
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);

    for ($i = 0; $i < $file_count; $i++) {
        foreach ($file_keys as $key) {
            $files[$i][$key] = $file_post[$key][$i];
        }
    }
    return $files;
}

switch ($method) {

    case 'POST':

        if ($action === 'agregar_producto') {

            $data = json_decode($_POST['data'], true);

            if ($data && isset($_FILES['imagenes'])) {

                require_once '../services/S3Service.php';
                $s3 = new S3Service();

                
                $imagenes = reArrayFiles($_FILES['imagenes']);

                $url_imagenes = [];

                foreach ($imagenes as $img) {
                    if (!empty($img['tmp_name'])) {
                        
                        $url = $s3->uploadImage($img);
                        $url_imagenes[] = $url;
                    }
                }

                $producto_controlador->crear_producto_controlador(
                    $data['nombre'] ?? '',
                    $data['descripcion'] ?? '',
                    $data['precio'] ?? 0,
                    $data['stock'] ?? 0,
                    $data['categoria_ID'] ?? 0,
                    $data['colores'] ?? [],
                    $data['dimensiones'] ?? [],
                    $url_imagenes
                );

            } else {
                echo json_encode(['status' => 'error', 'message' => 'Faltan datos requeridos para agregar producto.']);
            }

        } else {
            echo json_encode(['status' => 'Error', 'message' => 'Acción POST no reconocida']);
        }

        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);

        if ($action === 'editar_producto') {
            if (isset($data['producto_ID']) && is_array($data['datos'])) {
                $producto_controlador->editar_producto_controlador($data['producto_ID'], $data['datos']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Datos inválidos para editar producto.']);
            }
        } else {
            echo json_encode(['status' => 'Error', 'message' => 'Acción PUT no reconocida']);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents('php://input'), true);

        if ($action === 'eliminar_producto') {
            if (isset($data['producto_ID'])) {
                $resultado = $producto_controlador->eliminar_producto_controlador($data['producto_ID']);
                echo json_encode($resultado);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Falta el campo producto_ID.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Acción DELETE no reconocida']);
        }
        break;

    case 'GET':
        if ($action === 'obtener_productos') {
            $producto_controlador->obtener_productos_controlador();
        } else {
            echo json_encode(['status' => 'Error', 'message' => 'Acción GET no reconocida']);
        }
        break;

    default:
        echo json_encode(['status' => 'Error', 'message' => 'Método no reconocido']);
        break;
}

