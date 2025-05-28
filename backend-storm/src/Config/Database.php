<?php

namespace App\Config;

use PDO;
use PDOException;

class Database
{
    private PDO $connection;

    public function __construct()
    {
        $host = getenv('DB_HOST');
        $port = getenv('DB_PORT');
        $db = getenv('DB_NAME');
        $user = getenv('DB_USER');
        $pass = getenv('DB_PASS');

        $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";

        try {
            $this->connection = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode([
                'errors' => [['message' => 'DB Connection failed: ' . $e->getMessage()]]
            ]);
            exit;
        }
        file_put_contents('php://stderr', "DB_HOST=" . getenv('DB_HOST') . PHP_EOL);
        file_put_contents('php://stderr', "DB_USER=" . getenv('DB_USER') . PHP_EOL);
        file_put_contents('php://stderr', "DB_PASS=" . getenv('DB_PASS') . PHP_EOL);
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }
}
//
//
// namespace App\Config;
//
// use PDO;
// use PDOException;
//
// class Database
// {
//     private PDO $conn;
//
//     public function __construct()
//     {
//         try {
//             $this->conn = new PDO('mysql:host=localhost;dbname=scandiweb_store', 'root', 'AdminRoot123');
//             $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//         } catch (PDOException $e) {
//             die('DB Connection failed: ' . $e->getMessage());
//         }
//     }
//
//     public function getConnection(): PDO
//     {
//         return $this->conn;
//     }
// }
