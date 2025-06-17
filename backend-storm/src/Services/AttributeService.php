<?php

namespace App\Services;

use PDO;
use App\Factories\AttributeFactory;
use App\Models\Attribute\Attribute;

class AttributeService
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAttributesByProductId(string $productId, ?string $category = null): array
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT a.id, a.name, a.type
                FROM attributes a
                INNER JOIN product_attribute_sets pas ON a.id = pas.attribute_id
                WHERE pas.product_id = :productId
            ");
            $stmt->execute(['productId' => $productId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $attributes = [];

            foreach ($rows as $row) {
                $items = $this->getAttributeItems($row['id']);

                try {
                    $attributes[] = AttributeFactory::create(
                        $row['name'],
                        $row['type'],
                        $items,
                        $productId,
                    );
                } catch (\Throwable $e) {
                    error_log("[AttributeService] Invalid attribute: {$row['name']}, {$row['type']}");
                }
            }

            return $attributes;
        } catch (\Throwable $e) {
            error_log("[AttributeService] Failed to load attributes for product {$productId}: " . $e->getMessage());
            return [];
        }
    }

    private function getAttributeItems(string $attributeId): array
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, display_value AS displayValue, value
                FROM attribute_items
                WHERE attribute_id = :attributeId
            ");
            $stmt->execute(['attributeId' => $attributeId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            error_log("[AttributeService] getAttributeItems error: " . $e->getMessage());
            return [];
        }
    }
}
