<?php

namespace App\Services;

use PDO;
use PDOException;

class OrderService
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function createOrder(string $productId, int $quantity): bool
    {
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO orders (product_id, quantity) VALUES (:product_id, :quantity)"
            );

            return $stmt->execute([
                ':product_id' => $productId,
                ':quantity' => $quantity,
            ]);
        } catch (PDOException $e) {
            error_log("[OrderService] Failed to insert order: " . $e->getMessage());
            return false;
        }
    }
}
