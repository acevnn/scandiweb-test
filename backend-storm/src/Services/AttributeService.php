<?php

namespace App\Services;

use PDO;

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
            $attributes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($attributes as &$attribute) {
                $items = $this->getAttributeItems($attribute['id']);

                $sizeFilterMap = [
                    'huarache-x-stussy-le' => 'numeric',
                    'jacket-canada-goosee' => 'clothing',
                ];

                $filterType = $sizeFilterMap[$productId] ?? 'clothing';

                if ($filterType === 'numeric') {
                    $items = array_filter($items, fn($item) => is_numeric($item['value']));
                } elseif ($filterType === 'clothing') {
                    $validSizes = ['S', 'M', 'L', 'XL'];
                    $items = array_filter($items, fn($item) => in_array(strtoupper($item['value']), $validSizes));
                }

                $attribute['items'] = array_values($items);
            }

            return $attributes;
        } catch (\Throwable $e) {
            error_log("AttributeService error: " . $e->getMessage());
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
            error_log("AttributeService::getAttributeItems error: " . $e->getMessage());
            return [];
        }
    }
}
