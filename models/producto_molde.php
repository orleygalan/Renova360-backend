<?php

require '../config/conexion_db.php';

class Producto_molde
{
    private $conn;

    public function __construct()
    {
        $db = new Conexion_db();
        $this->conn = $db->conectar();
    }

    public function agregar_producto_completo($nombre, $descripcion, $precio, $stock, $categoria_ID, $colores, $dimensiones, $url_imagenes)
    {
        $this->conn->beginTransaction();

        try {
            // 1. Insertar producto
            $query = "INSERT INTO productos (nombre, descripcion, precio, stock, categoria_ID)
                      VALUES (:nombre, :descripcion, :precio, :stock, :categoria_ID)";

            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':nombre' => $nombre,
                ':descripcion' => $descripcion,
                ':precio' => $precio,
                ':stock' => $stock,
                ':categoria_ID' => $categoria_ID
            ]);

            // 2. ID del producto
            $producto_ID = $this->conn->lastInsertId();

            if (!empty($url_imagenes)) {

                // Asegurar que sea array
                $url_imagenes = is_array($url_imagenes) ? $url_imagenes : [$url_imagenes];

                $imgQuery = 'INSERT INTO imagenes (producto_ID, imagen) VALUES (:producto_ID, :imagen)';
                $imgStmt = $this->conn->prepare($imgQuery);

                foreach ($url_imagenes as $imgUrl) {
                    $imgStmt->execute([
                        ':producto_ID' => $producto_ID,
                        ':imagen' => $imgUrl
                    ]);
                }
            }

            if (!empty($colores)) {

                // Asegurar que sea array
                $colores = is_array($colores) ? $colores : [$colores];

                $colorQuery = "INSERT INTO colores (producto_ID, color)
                               VALUES (:producto_ID, :color)";
                $colorStmt = $this->conn->prepare($colorQuery);

                foreach ($colores as $color) {
                    $colorStmt->execute([
                        ':producto_ID' => $producto_ID,
                        ':color' => $color
                    ]);
                }
            }

            if (!empty($dimensiones) && is_array($dimensiones)) {

                $dimQuery = "INSERT INTO dimensiones (producto_ID, alto, ancho, largo)
                             VALUES (:producto_ID, :alto, :ancho, :largo)";

                $dimStmt = $this->conn->prepare($dimQuery);

                $dimStmt->execute([
                    ':producto_ID' => $producto_ID,
                    ':alto' => $dimensiones['alto'] ?? null,
                    ':ancho' => $dimensiones['ancho'] ?? null,
                    ':largo' => $dimensiones['largo'] ?? null
                ]);
            }

            $this->conn->commit();
            return true;

        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error al crear producto: " . $e->getMessage());
            return false;
        }
    }

    public function editar_producto_completo($producto_ID, $datos)
    {
        $this->conn->beginTransaction();

        try {
            // 1. Verificar que exista
            $stmt = $this->conn->prepare("SELECT * FROM productos WHERE producto_ID = :id");
            $stmt->execute([':id' => $producto_ID]);

            if (!$stmt->fetch()) {
                throw new Exception("Producto no encontrado");
            }

            $campos = [];
            $params = [':producto_ID' => $producto_ID];

            if (isset($datos['nombre'])) {
                $campos[] = "nombre = :nombre";
                $params[':nombre'] = $datos['nombre'];
            }

            if (isset($datos['descripcion'])) {
                $campos[] = "descripcion = :descripcion";
                $params[':descripcion'] = $datos['descripcion'];
            }

            if (isset($datos['precio'])) {
                $campos[] = "precio = :precio";
                $params[':precio'] = $datos['precio'];
            }

            if (isset($datos['stock'])) {
                $campos[] = "stock = :stock";
                $params[':stock'] = $datos['stock'];
            }

            if (!empty($campos)) {
                $query = "UPDATE productos SET " . implode(", ", $campos) . " WHERE producto_ID = :producto_ID";
                $stmt = $this->conn->prepare($query);
                $stmt->execute($params);
            }

            if (isset($datos['imagenes']) && is_array($datos['imagenes'])) {

                // Eliminar antiguas
                $this->conn->prepare("DELETE FROM imagenes WHERE producto_ID = :id")
                           ->execute([':id' => $producto_ID]);

                // Insertar nuevas
                $imgQuery = "INSERT INTO imagenes (producto_ID, imagen) VALUES (:producto_ID, :imagen)";
                $imgStmt = $this->conn->prepare($imgQuery);

                foreach ($datos['imagenes'] as $img) {
                    $imgStmt->execute([
                        ':producto_ID' => $producto_ID,
                        ':imagen' => $img
                    ]);
                }
            }

            if (isset($datos['colores']) && is_array($datos['colores'])) {

                $this->conn->prepare("DELETE FROM colores WHERE producto_ID = :id")
                           ->execute([':id' => $producto_ID]);

                $colorQuery = "INSERT INTO colores (producto_ID, color) VALUES (:producto_ID, :color)";
                $colorStmt = $this->conn->prepare($colorQuery);

                foreach ($datos['colores'] as $color) {
                    $colorStmt->execute([
                        ':producto_ID' => $producto_ID,
                        ':color' => $color
                    ]);
                }
            }

            if (isset($datos['dimensiones']) && is_array($datos['dimensiones'])) {

                $this->conn->prepare("DELETE FROM dimensiones WHERE producto_ID = :id")
                           ->execute([':id' => $producto_ID]);

                $dimQuery = "INSERT INTO dimensiones (producto_ID, alto, ancho, largo)
                             VALUES (:producto_ID, :alto, :ancho, :largo)";
                $dimStmt = $this->conn->prepare($dimQuery);

                $dimStmt->execute([
                    ':producto_ID' => $producto_ID,
                    ':alto' => $datos['dimensiones']['alto'] ?? null,
                    ':ancho' => $datos['dimensiones']['ancho'] ?? null,
                    ':largo' => $datos['dimensiones']['largo'] ?? null
                ]);
            }

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Error al editar producto: " . $e->getMessage());
            return false;
        }
    }

    public function eliminar_producto($producto_id)
    {
        $query = "DELETE FROM productos WHERE producto_ID = :producto_ID";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':producto_ID', $producto_id);
        return $stmt->execute();
    }

    public function obtener_productos_completos()
    {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM productos");
            $stmt->execute();
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($productos as &$producto) {
                $id = $producto['producto_ID'];

                // Imágenes
                $imgQuery = "SELECT imagen FROM imagenes WHERE producto_ID = :id";
                $imgStmt = $this->conn->prepare($imgQuery);
                $imgStmt->execute([':id' => $id]);
                $producto['imagenes'] = array_column($imgStmt->fetchAll(PDO::FETCH_ASSOC), 'imagen');

                // Colores
                $colorQuery = "SELECT color FROM colores WHERE producto_ID = :id";
                $colorStmt = $this->conn->prepare($colorQuery);
                $colorStmt->execute([':id' => $id]);
                $producto['colores'] = array_column($colorStmt->fetchAll(PDO::FETCH_ASSOC), 'color');

                // Dimensiones
                $dimQuery = "SELECT alto, ancho, largo FROM dimensiones WHERE producto_ID = :id";
                $dimStmt = $this->conn->prepare($dimQuery);
                $dimStmt->execute([':id' => $id]);
                $producto['dimensiones'] = $dimStmt->fetch(PDO::FETCH_ASSOC);

                // Comentarios
                $comQuery = "SELECT 
                                c.comentario_ID,
                                c.comentario,
                                u.nombre AS usuario
                             FROM comentarios_productos c
                             JOIN usuarios u ON c.usuario_ID = u.usuario_ID
                             WHERE c.producto_ID = :id
                             ORDER BY c.comentario_ID DESC";

                $comStmt = $this->conn->prepare($comQuery);
                $comStmt->execute([':id' => $id]);
                $producto['comentarios'] = $comStmt->fetchAll(PDO::FETCH_ASSOC);
            }

            return $productos;

        } catch (PDOException $e) {
            error_log("Error al obtener productos: " . $e->getMessage());
            return [];
        }
    }

}

?>
