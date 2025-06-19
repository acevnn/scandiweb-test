<?php

namespace App\Config;

use PDO;
use PDOException;

class Database
{
    private PDO $conn;

    public function __construct()
    {
        try {
            $this->conn = new PDO('mysql:host=localhost;dbname=scandiweb_store', 'root', 'AdminRoot123');
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die('DB Connection failed: ' . $e->getMessage());
        }
    }

    public function getConnection(): PDO
    {
        return $this->conn;
    }
}
