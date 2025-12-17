<?php

require '../config/conexion_db.php';

class Servicio_molde
{
    private $conn;

    public function __construct()
    {
        $db = new Conexion_db();
        $this->conn = $db->conectar();
    }

    public function crear_servicio($descripcion_nombre, $url_imagen, $categoria_ID)
    {
        $this->conn->beginTransaction();

        try {
            // insertar el servicio principal
            $query = 'INSERT INTO servicios (descripcion_nombre, video_principal, categoria_ID)
                      VALUES (:descripcion_nombre, :video_principal, :categoria_ID)';

            // $imagenes_json = json_encode($url_imagen, JSON_UNESCAPED_SLASHES);

            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':descripcion_nombre' => $descripcion_nombre,
                ':video_principal' => $url_imagen,
                ':categoria_ID' => $categoria_ID
            ]);

            // obtener el ID del nuevo servicio
            // $servicio_ID = $this->conn->lastInsertId();

            // if (!empty($multimedia)) {
            //     $multiQuery = 'INSERT INTO multimedia (multimedia, tipo_multimedia, usuario_ID, servicio_ID)
            //                    VALUES (:multimedia, :tipo_multimedia, :usuario_ID, :servicio_ID)';

            //     $stmt = $this->conn->prepare($multiQuery);

            //     foreach ($multimedia as $item) {
            //         $stmt->execute([
            //             ':servicio_ID' => $servicio_ID,
            //             ':usuario_ID' => $item['usuario_ID'],
            //             ':multimedia' => $item['multimedia'],
            //             ':tipo_multimedia' => $item['tipo_multimedia']
            //         ]);
            //     }
            // }

            // Confirmar transacción
            $this->conn->commit();
            return true;

        } catch (PDOException $ex) {
            // si hay error, revertimos todo
            $this->conn->rollBack();
            error_log("Error al crear servicio: " . $ex->getMessage());
            return false;
        }
    }

    public function obtener_servicios()
    {
        $query = 'SELECT s.servicio_ID, s.descripcion_nombre, s.video_principal, s.categoria_ID, c.nombre AS categoria_nombre
                  FROM servicios AS s
                  LEFT JOIN categorias c ON s.categoria_ID = c.categoria_ID';

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Agregar multimedia y comentarios a cada servicio

        foreach ($servicios as &$servicio) {
            //  Multimedia
            $multiQuery = 'SELECT multimedia, tipo_multimedia
                           FROM multimedia
                           WHERE servicio_ID = :servicio_ID';
            $stmtMulti = $this->conn->prepare($multiQuery);
            $stmtMulti->execute([':servicio_ID' => $servicio['servicio_ID']]);
            $servicio['multimedia'] = $stmtMulti->fetchAll(PDO::FETCH_ASSOC);

            //  Comentarios de los clientes
            $comentariosQuery = 'SELECT cs.comentario_ID, cs.usuario_ID, u.nombre, u.apellido, cs.comentario
                                 FROM comentarios_servicios AS cs
                                 LEFT JOIN usuarios AS u ON cs.usuario_ID = u.usuario_ID
                                 WHERE cs.servicio_ID = :servicio_ID';
            $stmtComentarios = $this->conn->prepare($comentariosQuery);
            $stmtComentarios->execute([':servicio_ID' => $servicio['servicio_ID']]);
            $servicio['comentarios'] = $stmtComentarios->fetchAll(PDO::FETCH_ASSOC);
        }

        return $servicios;
    }

    public function editar_servicio($data)
    {
        try {
            $campos = [];
            $params = [':servicio_ID' => $data['servicio_ID']];

            if (isset($data['descripcion_nombre'])) {
                $campos[] = "descripcion_nombre = :descripcion_nombre";
                $params[':descripcion_nombre'] = $data['descripcion_nombre'];
            }
            if (isset($data['video_principal'])) {
                $campos[] = "video_principal = :video_principal";
                $params[':video_principal'] = $data['video_principal'];
            }
            if (isset($data['categoria_ID'])) {
                $campos[] = "categoria_ID = :categoria_ID";
                $params[':categoria_ID'] = $data['categoria_ID'];
            }

            if (empty($campos))
                return false;

            $query = "UPDATE servicios SET " . implode(', ', $campos) . " WHERE servicio_ID = :servicio_ID";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Error al editar servicio: " . $e->getMessage());
            return false;
        }
    }

    public function eliminar_servicio($servicio_ID)
    {
        try {
            $this->conn->beginTransaction();

            // Eliminar multimedia asociada
            $this->conn->prepare("DELETE FROM multimedia WHERE servicio_ID = :id")
                ->execute([':id' => $servicio_ID]);

            // Eliminar servicio
            $stmt = $this->conn->prepare("DELETE FROM servicios WHERE servicio_ID = :id");
            $stmt->execute([':id' => $servicio_ID]);

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error al eliminar servicio: " . $e->getMessage());
            return false;
        }
    }
}

?>