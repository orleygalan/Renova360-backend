<?php

require '../config/conexion_db.php';


class Usuario_molde
{

    private $conn;
    private $tabla = 'usuarios';
    public function __construct()
    {
        $db = new Conexion_db();
        $this->conn = $db->conectar();
    }

    public function crear_usuario($nombre, $apellido, $correo, $contrasena, $token)
    {
        $estado = 0;


        $query = ' INSERT INTO ' . $this->tabla . ' (nombre, apellido, correo, contrasena, recordar_token, estado) VALUES (:nombre, :apellido, :correo, :contrasena, :recordar_token, :estado )';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellido', $apellido);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':contrasena', $contrasena);
        $stmt->bindParam(':recordar_token', $token);
        $stmt->bindParam(':estado', $estado);

        return $stmt->execute();
    }
    public function confirmacion_registro($token)
    {
        $query = 'UPDATE ' . $this->tabla . ' SET estado = 1  WHERE recordar_token = :token';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);
        return $stmt->execute();
    }


    public function iniciar_sesion($correo, $contrasena, $token)
    {

        // verificar usuario 
        $query = 'SELECT us.nombre, us.apellido, us.rol, us.correo, us.contrasena FROM ' . $this->tabla . ' AS us WHERE us.correo = :correo ';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':correo', $correo);

        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($contrasena, $usuario['contrasena'])) {
            // actualizar token para cada vez que inicie sesion
            $query = 'UPDATE ' . $this->tabla . ' SET recordar_token = :token WHERE correo = :correo';
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':token', $token);
            $stmt->bindParam(':correo', $correo);
            $stmt->execute();

            // eliminamos la contraseña antes de enviar el json al frontend 
            unset($usuario['contrasena']);
            // devolvemos todos los datos sin la contraseña 
            return $usuario;
        }
        return false;
    }

    public function obtener_perfil($token)
    {
        $query = 'SELECT * FROM ' . $this->tabla . ' WHERE recordar_token = :token';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }



    public function agregar_administrador($correo)
    {
        $query = 'UPDATE ' . $this->tabla . ' SET rol = "administrador" WHERE correo = :correo AND estado = 1';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':correo', $correo);
        return $stmt->execute() && $stmt->rowCount() > 0;
    }

    public function eliminar_usuario($token)
    {
        if (!$token) {
            return false;
        }

        $query = 'DELETE FROM ' . $this->tabla . ' WHERE recordar_token = :token';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);

        return $stmt->execute() && $stmt->rowCount() > 0;
    }

    // Eliminamos al administrador volviendolo un usuario
    public function eliminar_administrador($correo)
    {
        if (!$correo) {
            return false;
        }

        $query = 'UPDATE ' . $this->tabla . ' SET rol = "usuario" WHERE correo = :correo';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':correo', $correo);

        return $stmt->execute() && $stmt->rowCount() > 0;
    }

}


?>