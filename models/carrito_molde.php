<?php
require '../config/conexion_db.php';

class Carrito_molde
{

    private $conn;

    public function __construct()
    {
        $db = new Conexion_db();
        $this->conn = $db->conectar();
    }

    public function agregar_producto_carrito($usuario_ID, $producto_ID)
    {
        $check = 'SELECT COUNT(*) FROM carritos WHERE usuario_ID = :usuario_ID AND producto_ID = :producto_ID';
        $stmt = $this->conn->prepare($check);
        $stmt->bindParam(':usuario_ID', $usuario_ID);
        $stmt->bindParam(':producto_ID', $producto_ID);

        $stmt->execute();

        if ($stmt->fetchColumn() > 0) {
            return false;
        }

        $query = 'INSERT INTO carritos (usuario_ID, producto_ID) VALUES 
                    (:usuario_ID, :producto_ID)';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_ID', $usuario_ID);
        $stmt->bindParam(':producto_ID', $producto_ID);

        return $stmt->execute() && $stmt->rowCount() > 0;

    }
    public function eliminar_producto_carrito($usuario_ID, $producto_ID)
    {
        $query = 'DELETE FROM carritos WHERE usuario_ID = :usuario_ID AND producto_ID = :producto_ID';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_ID', $usuario_ID);
        $stmt->bindParam(':producto_ID', $producto_ID);

        return $stmt->execute();
    }
}


?>