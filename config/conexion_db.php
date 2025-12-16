<?php

// class Conexion_db
// {
//     private $host = 'crossover.proxy.rlwy.net';
//     private $usuario = 'root';
//     private $pass = 'CmQOkagzRlwwDUURdoihuBXBtYAdFzzZ';
//     private $db = 'railway';
//     private $port = 31271;

//     public $conexion;

//     public function conectar()
//     {
//         $this->conexion = null;

//         try {
//             $this->conexion = new PDO(
//                 "mysql:host={$this->host};port={$this->port};dbname={$this->db};charset=utf8",
//                 $this->usuario,
//                 $this->pass,
//                 [
//                     PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
//                     PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
//                 ]
//             );
//         } catch (PDOException $ex) {
//             die('Error de conexión DB: ' . $ex->getMessage());
//         }

//         return $this->conexion;
//     }
// }

// <?php

class Conexion_db
{
    private $host;
    private $usuario;
    private $pass;
    private $db;
    private $port;

    public function __construct()
    {
        $this->host = getenv('MYSQLHOST');
        $this->usuario = getenv('MYSQLUSER');
        $this->pass = getenv('MYSQLPASSWORD');
        $this->db = getenv('MYSQLDATABASE');
        $this->port = getenv('MYSQLPORT');
    }

    public function conectar()
    {
        try {
        $pdo = new PDO(
            "mysql:host=" . getenv('MYSQLHOST') .
            ";port=" . getenv('MYSQLPORT') .
            ";dbname=" . getenv('MYSQLDATABASE') .
            ";charset=utf8mb4",
            getenv('MYSQLUSER'),
            getenv('MYSQLPASSWORD'),
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );

        return $pdo; 
    } catch (PDOException $e) {
        throw $e;
    }
    }
}
