<?php
require '../config/conexion_db.php';

class Pedido_molde
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


    //  Crear pedido a partir de un carrito (varios productos)

    public function crear_desde_carrito($usuario_ID)
    {
        try {
            $this->conn->beginTransaction();

            // Verificar si el carrito del usuario existe
            $stmt = $this->conn->prepare("SELECT carrito_ID FROM carritos WHERE usuario_ID = :usuario_ID");
            $stmt->bindParam(':usuario_ID', $usuario_ID);
            $stmt->execute();
            $carrito = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$carrito) {
                throw new Exception("No se encontró carrito para este usuario.");
            }

            $carrito_ID = $carrito['carrito_ID'];

            // Crear pedido principal
            $stmtInsert = $this->conn->prepare("INSERT INTO pedidos (carrito_ID) VALUES (:carrito_ID)");
            $stmtInsert->bindValue(':carrito_ID', $carrito_ID);
            $stmtInsert->execute();


            $pedido_ID = $this->conn->lastInsertId();

            // Obtener los productos del carrito
            $stmt = $this->conn->prepare(" SELECT producto_ID, cantidad
                FROM carritos
                WHERE usuario_ID = :usuario_ID
            ");
            $stmt->bindParam(':usuario_ID', $usuario_ID);
            $stmt->execute();
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($productos)) {
                throw new Exception("El carrito está vacío.");
            }

            //  Insertar los detalles del pedido
            $stmt = $this->conn->prepare(" INSERT INTO detalles_pedidos (pedido_ID, producto_ID, cantidad)
                VALUES (:pedido_ID, :producto_ID, :cantidad)
            ");

            foreach ($productos as $producto) {
                $stmt->bindValue(':pedido_ID', $pedido_ID);
                $stmt->bindValue(':producto_ID', $producto['producto_ID']);
                $stmt->bindValue(':cantidad', $producto['cantidad']);

                $stmt->execute();
            }

            $this->conn->commit();

            return [
                'status' => 'success',
                'mensaje' => 'Pedido creado con éxito',
                'pedido_ID' => $pedido_ID
            ];

        } catch (Exception $e) {
            $this->conn->rollBack();
            return [
                'status' => 'error',
                'mensaje' => 'Error al crear pedido: ' . $e->getMessage()
            ];
        }
    }


    //   Crear pedido directo (sin carrito)
  
    public function crear_pedido_directo($usuario_ID, $producto_ID, $cantidad = 1)
    {
        try {
            $this->conn->beginTransaction();

            // Crear pedido
            $queryPedido = "INSERT INTO pedidos ( estado) 
                            VALUES ('pendiente')";
            $stmtPedido = $this->conn->prepare($queryPedido);
            $stmtPedido->execute();
            $pedido_ID = $this->conn->lastInsertId();

            // Agregar detalle
            $queryDetalle = "INSERT INTO detalles_pedidos (pedido_ID, producto_ID, cantidad)
                             VALUES (:pedido_ID, :producto_ID, :cantidad)";
            $stmtDetalle = $this->conn->prepare($queryDetalle);
            $stmtDetalle->execute([
                ':pedido_ID' => $pedido_ID,
                ':producto_ID' => $producto_ID,
                ':cantidad' => $cantidad
            ]);

            $this->conn->commit();
            return $pedido_ID;

        } catch (PDOException $ex) {
            $this->conn->rollBack();
            $this->lastError = $ex->getMessage();
            return false;
        }
    }


    //  Obtener pedidos por usuario (para listar historial)

    public function obtener_pedidos_por_usuario($usuario_ID)
    {
        try {
            $query = "SELECT * FROM pedidos WHERE usuario_ID = :usuario_ID";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':usuario_ID' => $usuario_ID]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            $this->lastError = $ex->getMessage();
            return [];
        }
    }

     public function obtener_carritos_por_usuario($usuario_ID)
    {
        try {
            $query = "SELECT * FROM carritos WHERE usuario_ID = :usuario_ID";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':usuario_ID' => $usuario_ID]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            $this->lastError = $ex->getMessage();
            return [];
        }
    }
}
?>