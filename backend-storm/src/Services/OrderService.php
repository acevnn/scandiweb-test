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

    public function createOrder(array $items): bool
    {
        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare("INSERT INTO orders (created_at) VALUES (NOW())");
            $stmt->execute();
            $orderId = $this->pdo->lastInsertId();

            foreach ($items as $item) {
                $stmt = $this->pdo->prepare("
                    INSERT INTO order_items (order_id, product_id, quantity, selected_attributes)
                    VALUES (:order_id, :product_id, :quantity, :selected_attributes)
                ");

                $stmt->execute([
                    ':order_id' => $orderId,
                    ':product_id' => $item['productId'],
                    ':quantity' => $item['quantity'],
                    ':selected_attributes' => $item['selectedAttributes'],
                ]);
            }

            $this->pdo->commit();
            return true;
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            error_log("[OrderService] Failed to create full order: " . $e->getMessage());
            return false;
        }
    }
}
