<?php

//Cargar variables de entorno
require_once __DIR__ . '/../bootstrap.php';

// Cargar la clase de conexión
require_once __DIR__ . '/../config/conexion_db.php';

// Usar la conexión
$db = new Conexion_db();
$conn = $db->conectar();

?>