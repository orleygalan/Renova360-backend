<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/conexion_db.php';

try {
    $db = new Conexion_db();
    $pdo = $db->conectar();

    echo json_encode([
        "success" => true,
        "msg" => "Conectado correctamente a Railway"
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
