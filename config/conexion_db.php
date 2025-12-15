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
        $this->host = $_ENV['MYSQLHOST'];
        $this->usuario = $_ENV['MYSQLUSER'];
        $this->pass = $_ENV['MYSQLPASSWORD'];
        $this->db = $_ENV['MYSQLDATABASE'];
        $this->port = $_ENV['MYSQLPORT'];
    }

    public function conectar()
    {
        return new PDO(
            "mysql:host={$this->host};port={$this->port};dbname={$this->db};charset=utf8",
            $this->usuario,
            $this->pass,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }
}
