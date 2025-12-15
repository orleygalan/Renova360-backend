<?php

require '../config/conexion_db.php';

class Comentario_producto_molde
{

    private $conn;
    private $lastError = null;

    public function __construct()
    {
        $db = new Conexion_db();
        $this->conn = $db->conectar();
    }

    public function getLastError()
    {
        return $this->lastError;
    }

    public function agregar_comentario($usuario_ID, $comentario, $producto_ID)
    {
        try {
            $query = 'INSERT INTO comentarios_productos 
                                (usuario_ID, comentario, producto_ID) 
                    VALUES (:usuario_ID, :comentario, :producto_ID)';
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':usuario_ID', $usuario_ID);
            $stmt->bindParam(':comentario', $comentario);
            $stmt->bindParam(':producto_ID', $producto_ID);


            if ($stmt->execute() && $stmt->rowCount() > 0) {
                $this->lastError = null;
                return true;
            }

            $this->lastError = 'no_rows';
            return false;

        } catch (PDOException $ex) {
            $sqlErrNum = $ex->errorInfo[1] ?? null;
            if ($sqlErrNum == 1062) {
                $this->lastError = 'duplicate';
            } else {
                $this->lastError = $ex->getMessage();
                error_log("Error al agregar comentario: " . $ex->getMessage());
            }
            return false;
        }
    }

    public function eliminar_comentario($comentario_ID, $usuario_ID, $es_admin = false)
    {
        try {
            if ($es_admin) {
                //  El administrador puede eliminar cualquier comentario
                $query = 'DELETE FROM comentarios_productos WHERE comentario_ID = :comentario_ID';
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':comentario_ID', $comentario_ID);
            } else {
                //  El usuario solo puede eliminar sus propios comentarios
                $query = 'DELETE FROM comentarios_productos 
                      WHERE comentario_ID = :comentario_ID 
                      AND usuario_ID = :usuario_ID';
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':comentario_ID', $comentario_ID);
                $stmt->bindParam(':usuario_ID', $usuario_ID);
            }

            if ($stmt->execute() && $stmt->rowCount() > 0) {
                $this->lastError = null;
                return true;
            }

            $this->lastError = 'no_rows';
            return false;

        } catch (PDOException $ex) {
            $this->lastError = $ex->getMessage();
            error_log("Error al eliminar comentario: " . $ex->getMessage());
            return false;
        }
    }


}


?>