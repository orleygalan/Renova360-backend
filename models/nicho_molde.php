<?php

require '../config/conexion_db.php';

class Nicho_molde
{
    private $conn;
    private $tabla = 'categorias';

    public function __construct()
    {
        $db = new Conexion_db();
        $this->conn = $db->conectar();
    }

    public function agregar_nicho($nombre, $tipo, $usuario_ID, $categorySlug, $descripcion, $url_imagen)
    {
        $checkQuery = "SELECT COUNT(*) FROM {$this->tabla} WHERE nombre = :nombre OR categorySlug = :categorySlug";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->bindParam(':nombre', $nombre);
        $checkStmt->bindParam(':categorySlug', $categorySlug);
        $checkStmt->execute();

        $existe = $checkStmt->fetchColumn();

        if ($existe > 0) {
            return [
                "status" => "error",
                "message" => "El nicho ya existe."
            ];
        }

        $query = "INSERT INTO {$this->tabla} 
              (nombre, tipo, usuario_ID, categorySlug, descripcion, imagen)
              VALUES (:nombre, :tipo, :usuario_ID, :categorySlug, :descripcion, :imagen)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':usuario_ID', $usuario_ID);
        $stmt->bindParam(':categorySlug', $categorySlug);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':imagen', $url_imagen);

        if ($stmt->execute()) {
            return [
                "status" => "success",
                "message" => "Nicho agregado correctamente"
            ];
        } else {
            return [
                "status" => "error",
                "message" => "Error insertando el nicho"
            ];
        }
    }


    public function editar_nicho($id, $nombre = null, $categorySlug = null, $tipo = null)
    {
        // Construimos dinámicamente el SQL solo con los campos que llegan
        $campos = [];
        $params = [];

        if ($nombre !== null) {
            $campos[] = "nombre = :nombre";
            $params[':nombre'] = $nombre;
        }

        if ($categorySlug !== null) {
            $campos[] = "categorySlug = :categorySlug";
            $params[':categorySlug'] = $categorySlug;
        }

        if ($tipo !== null) {
            $campos[] = "tipo = :tipo";
            $params[':tipo'] = $tipo;
        }

        // Si no hay campos para actualizar, salimos
        if (empty($campos)) {
            return false;
        }

        // Construir la consulta final
        $query = "UPDATE " . $this->tabla . " SET " . implode(", ", $campos) . " WHERE categoria_ID = :id";
        $stmt = $this->conn->prepare($query);

        // Asignar parámetros dinámicamente
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':id', $id);

        return $stmt->execute() && $stmt->rowCount() > 0;
    }

    public function eliminar_nicho($id)
    {
        $query = "DELETE FROM " . $this->tabla . " WHERE categoria_ID = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function obtener_nichos()
    {
        $query = "SELECT * FROM categorias";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



}


?>