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
            $this->conn = new PDO(
                'mysql:host=fdb1028.awardspace.net;dbname=4636572_scandi',
                '4636572_scandi',
                '123Database456'
            );
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
