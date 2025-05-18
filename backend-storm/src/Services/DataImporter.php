<?php

namespace App\Services;

use PDO;

class DataImporter
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function import(string $jsonPath): void
    {
        $data = json_decode(file_get_contents($jsonPath), true);

        $this->importCategories($data['data']['categories']);
        $this->importProducts($data['data']['products']);
    }

    private function importCategories(array $categories): void
    {
        foreach ($categories as $category) {
            $stmt = $this->pdo->prepare("INSERT IGNORE INTO categories (name) VALUES (:name)");
            $stmt->execute([':name' => $category['name']]);
        }
    }

    private function importProducts(array $products): void
    {
        foreach ($products as $product) {
            $categoryId = $this->getCategoryIdByName($product['category']);

            $this->insertProduct($product, $categoryId);
            $this->insertImages($product['id'], $product['gallery']);
            $this->insertPrices($product['id'], $product['prices']);
            $this->insertAttributes($product['id'], $product['attributes']);
        }
    }

    private function getCategoryIdByName(string $categoryName): int
    {
        $stmt = $this->pdo->prepare("SELECT id FROM categories WHERE name = :name");
        $stmt->execute([':name' => $categoryName]);
        return (int) $stmt->fetchColumn();
    }

    private function insertProduct(array $product, int $categoryId): void
    {
        $stmt = $this->pdo->prepare(<<<SQL
            INSERT INTO products (id, name, description, in_stock, brand, category_id)
            VALUES (:id, :name, :description, :in_stock, :brand, :category_id)
        SQL);

        $stmt->execute([
            ':id' => $product['id'],
            ':name' => $product['name'],
            ':description' => $product['description'],
            ':in_stock' => $product['inStock'] ? 1 : 0,
            ':brand' => $product['brand'],
            ':category_id' => $categoryId,
        ]);
    }

    private function insertImages(string $productId, array $images): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO product_images (product_id, image_url) VALUES (:product_id, :image_url)"
        );
        foreach ($images as $image) {
            $stmt->execute([':product_id' => $productId, ':image_url' => $image]);
        }
    }

    private function insertPrices(string $productId, array $prices): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO prices (product_id, amount, currency_label, currency_symbol) 
                    VALUES (:product_id, :amount, :label, :symbol)"
        );
        foreach ($prices as $price) {
            $stmt->execute([
                ':product_id' => $productId,
                ':amount' => $price['amount'],
                ':label' => $price['currency']['label'],
                ':symbol' => $price['currency']['symbol']
            ]);
        }
    }

    private function insertAttributes(string $productId, array $attributes): void
    {
        foreach ($attributes as $attribute) {
            $attrId = $this->getOrInsertAttribute($attribute['name'], $attribute['type']);

            $this->insertAttributeItems($attrId, $attribute['items']);
            $this->linkProductAttribute($productId, $attrId);
        }
    }

    private function getOrInsertAttribute(string $name, string $type): int
    {
        $stmt = $this->pdo->prepare("INSERT IGNORE INTO attributes (name, type) VALUES (:name, :type)");
        $stmt->execute([':name' => $name, ':type' => $type]);

        $id = $this->pdo->lastInsertId();
        if (!$id) {
            $stmt = $this->pdo->prepare("SELECT id FROM attributes WHERE name = :name");
            $stmt->execute([':name' => $name]);
            $id = $stmt->fetchColumn();
        }
        return (int) $id;
    }

    private function insertAttributeItems(int $attributeId, array $items): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT IGNORE INTO attribute_items (attribute_id, display_value, value, item_key)
                    VALUES (:attr_id, :display, :value, :item_key)"
        );
        foreach ($items as $item) {
            $stmt->execute([
                ':attr_id' => $attributeId,
                ':display' => $item['displayValue'],
                ':value' => $item['value'],
                ':item_key' => $item['id'],
            ]);
        }
    }

    private function linkProductAttribute(string $productId, int $attributeId): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO product_attribute_sets (product_id, attribute_id)
                    VALUES (:pid, :aid)"
        );
        $stmt->execute([':pid' => $productId, ':aid' => $attributeId]);
    }
}
