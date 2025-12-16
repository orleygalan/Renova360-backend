<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../cros.php';
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../config/conexion_db.php';

// try {
$db = new Conexion_db();
$conn = $db->conectar();
//     echo json_encode([
//         "success" => true,
//         "categorias" => $data
//     ]);
// } catch (PDOException $e) {
// echo json_encode([
//     "success" => false,
//     "error" => $e->getMessage()
// ]);
// }