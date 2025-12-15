<?php

ini_set('display_errors', 0);
ini_set('log_errors', 1);

require '../cros.php';
require '../controllers/usuario_controlador.php';
// require '../api/usuarios_api.php';

$usuario_controlador = new Usuario_controlador();
$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true);
$action = $_GET['action'] ?? null;

$headers = getallheaders();
$auth = $headers["Authorization"] ?? "";

switch ($method) {
    case 'POST':

        if (isset($action)) {
            switch ($action) {
                case 'registrar':
                    $usuario_controlador->registrar($data['nombre'], $data['apellido'], $data['correo'], $data['contrasena']);
                    break;
                case 'iniciar_sesion':
                    $usuario_controlador->gestor_inicio_sesion($data['correo'], $data['contrasena']);
                    break;
                case 'agregar_admin':
                    $usuario_controlador->gestor_de_administrador($data['correo']);
                    break;

                case 'obtener_perfil':
                    if (!str_starts_with($auth, "Bearer ")) {
                        echo json_encode(["error" => "Token no enviado"]);
                        exit;
                    }

                    $token = trim(str_replace("Bearer", "", $auth));

                    $usuario_controlador->obtener_perfil_controlador($token);
                    break;
                default:
                    echo json_encode([
                        'status' => 'Error',
                        'message' => 'Accion POST no reconocida'
                    ]);
                    break;
            }
        }

        break;
    case 'GET':
        if (isset($_GET['token'])) {
            $token = $_GET['token'];
            $usuario_controlador->confirmar($token);
        } else {
            echo "Token no válido.";
        }
        break;
    case 'DELETE':
        if ($action) {
            switch ($action) {
                case 'eliminar_usuario':
                    $usuario_controlador->gestor_de_eliminacion_usuario($data['token']);
                    break;
                case 'eliminar_administrador':
                    $usuario_controlador->gestor_de_eliminacion_admin($data['correo']);
                    break;

                default:
                    echo json_encode([
                        'status' => 'Error',
                        'message' => 'Accion DELETE no reconocida'
                    ]);
                    break;
            }
        }
        break;

    default:
        echo json_encode([
            'status' => 'Error',
            'message' => 'Metodos no reconocido'
        ]);
        break;
}


?>