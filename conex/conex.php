<?php
// $server = "127.0.0.1";
// $username = "root";
// $passw = "";
// $datebase = "ejercicie";

// $sql = mysqli_connect(hostname: $server, username: $username, password: $passw, database: $datebase);

// if (!$sql){
//     echo "Don't Have Conectión To Server";
// }
// else{
    
// }

// PROGRAMACION ORIENTADA A OBJETOS OTRA VEZ

class Database
{
    private $hostname = "localhost";
    private $database = "dead_ahead_zombie";
    private $username = "root";
    private $password = "";
    private $charset = "utf8";

        function conectar()
        {
            try{ $conexion = "mysql:host=" . $this->hostname . "; dbname=" . $this->database . "; charset=" . $this->charset;
            
                $option = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_EMULATE_PREPARES => false
                ];

                $pdo = new PDO($conexion, $this->username,$this->password, $option);

                return $pdo;

            }

            catch(PDOException $e)
            {
                echo "Conection Error: " . $e->getMessage();
                exit;
            }
        }

}
?>