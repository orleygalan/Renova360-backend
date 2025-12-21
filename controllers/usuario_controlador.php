<?php
require '../models/usuario_molde.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Usuario_controlador
{
    private $usuario_model;

    function __construct()
    {
        $this->usuario_model = new Usuario_molde();
    }

    public function registrar($nombre, $apellido, $correo, $contrasena)
    {
        $token = bin2hex(random_bytes(32));
        $contrasena = password_hash($contrasena, PASSWORD_DEFAULT);


        $creado = $this->usuario_model->crear_usuario(
            $nombre,
            $apellido,
            $correo,
            $contrasena,
            $token
        );

        if (!$creado) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Error al registrar el usuario.'
            ]);
            return;
        }


        $correoEnviado = $this->enviar_correo_verificacion($correo, $token);

        if (!$correoEnviado) {
            echo json_encode([
                'status' => 'warning',
                'message' => 'Usuario creado, pero no se pudo enviar el correo de verificación.'
            ]);
            return;
        }


        echo json_encode([
            'status' => 'success',
            'message' => 'Registro exitoso. Revisa tu correo para confirmar tu cuenta.'
        ]);
    }


    public function enviar_correo_verificacion($correo, $token)
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $_ENV['SMTP_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['SMTP_USER'];
            $mail->Password = $_ENV['SMTP_PASS'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $_ENV['SMTP_PORT'];

            $mail->setFrom($_ENV['SMTP_FROM'], 'Renova360');
            $mail->addAddress($correo);

            $mail->isHTML(true);
            $mail->Subject = 'Confirma tu registro';
            $mail->Body = "
            <h3>Confirma tu cuenta</h3>
            <p>Haz clic en el botón para activar tu cuenta:</p>
            <a href='https://renova360-backend-production.up.railway.app/usuarios?token=$token'
               style='padding:10px 15px;background:#2563eb;color:#fff;text-decoration:none;border-radius:5px'>
               Confirmar cuenta
            </a>
        ";

            $mail->send();
            return true;

        } catch (Exception $e) {
            error_log('MAIL ERROR: ' . $mail->ErrorInfo);
            return false;
        }
    }



    public function confirmar($token)
    {
        if ($this->usuario_model->confirmacion_registro($token)) {
            echo json_encode([
                "status" => "success",
                "message" => "Cuenta activada correctamente."
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Token inválido o expirado."
            ]);
        }
    }


    public function gestor_inicio_sesion($correo, $contrasena)
    {
        $token = bin2hex(random_bytes(16));
        $usuario = $this->usuario_model->iniciar_sesion($correo, $contrasena, $token);

        if ($usuario) {

            echo json_encode([
                "status" => "ok",
                "mensaje" => "Sesion iniciada correctamente.",
                "token" => $token,
                "usuario" => $usuario
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "mensaje" => "Error al iniciar sesion , vuelva a intentarlo."
            ]);
        }
    }

    public function obtener_perfil_controlador($token)
    {
        if (!$token) {
            return [
                "status" => false,
                "message" => "Token no proporcionado"
            ];
        }

        $perfil = $this->usuario_model->obtener_perfil($token);

        if ($perfil) {

            echo json_encode([
                "status" => "ok",
                "mensaje" => "Datos obtenidos exitosamente",
                "perfil" => $perfil
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "mensaje" => "Error al obtener todos los datos , vuelva a intentarlo."
            ]);
        }
    }
    public function gestor_de_administrador($correo)
    {
        if ($this->usuario_model->agregar_administrador($correo)) {
            echo json_encode([
                "status" => "ok",
                "mensaje" => "Administrador agregado."
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "mensaje" => "Error no se apodido agregar el nuevo administrador."
            ]);
        }
    }

    public function gestor_de_eliminacion_admin($correo)
    {
        if ($this->usuario_model->eliminar_administrador($correo)) {
            echo json_encode([
                "status" => "ok",
                "mensaje" => "Rol de administrador eliminado correctamente."
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "mensaje" => "Error no se pudo eliminar el rol como administrador."
            ]);
        }
    }

    public function gestor_de_eliminacion_usuario($token)
    {
        if ($this->usuario_model->eliminar_usuario($token)) {
            echo json_encode([
                "status" => "ok",
                "mensaje" => "Cuenta de usuario eliminada correctamente."
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "mensaje" => "Error no se pudo eliminar la  cuenta del usuario."
            ]);
        }
    }


}


?>