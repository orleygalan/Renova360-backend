<?php

require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../config/conexion_db.php';

try {
    $db = new Conexion_db();
    $conn = $db->conectar();
    echo "¡Backend funcionando correctamente!";
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
}

?>