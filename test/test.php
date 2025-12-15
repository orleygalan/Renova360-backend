<?php
require_once "../config/conexion_db.php";

$db = new Conexion_db();
$conn = $db->conectar();

echo "✅ Conectado a Railway correctamente";
