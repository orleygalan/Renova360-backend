<?php

// 1️⃣ Cargar variables de entorno
require_once __DIR__ . '/../bootstrap.php';

// 2️⃣ Cargar la clase de conexión
require_once __DIR__ . '/../config/conexion_db.php';

// 3️⃣ Usar la conexión
$db = new Conexion_db();
$conn = $db->conectar();

var_dump($_ENV['MYSQLHOST']);
exit;