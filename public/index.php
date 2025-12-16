<?php
require '../cros.php';
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../config/conexion_db.php';

try {
    $db = new Conexion_db();
    $conn = $db->conectar();

    $stmt = $conn->query("SELECT DATABASE() AS db");
    $data = $stmt->fetch();

    echo json_encode([
        "success" => true,
        "data" => $data
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}