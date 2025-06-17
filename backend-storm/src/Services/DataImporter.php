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

    public function import(string $filePath): void
    {
        $data = json_decode(file_get_contents($filePath), true);

        foreach ($data['categories'] as $category) {
            $categoryId = $this->insertCategory($category['name']);
            foreach ($category['products'] as $product) {
                $productId = $this->insertProduct($product, $categoryId, $category['name']);
                foreach ($product['attributes'] as $attribute) {
                    $attributeId = $this->insertAttribute($attribute);
                    $this->linkProductAttribute($productId, $attributeId);
                }

                foreach ($product['gallery'] as $imageUrl) {
                    $this->insertImage($productId, $imageUrl);
                }

                foreach ($product['prices'] as $price) {
                    $this->insertPrice($productId, $price);
                }
            }
        }
    }

    private function insertCategory(string $name): int
    {
        $stmt = $this->pdo->prepare("SELECT id FROM categories WHERE name = :name");
        $stmt->execute(['name' => $name]);
        $existing = $stmt->fetchColumn();

        if ($existing) {
            return (int) $existing;
        }

        $stmt = $this->pdo->prepare("INSERT INTO categories (name) VALUES (:name)");
        $stmt->execute(['name' => $name]);
        return (int) $this->pdo->lastInsertId();
    }

    private function insertProduct(array $product, int $categoryId, string $categoryName): int
    {
        $stmt = $this->pdo->prepare("SELECT id FROM products WHERE id = :id");
        $stmt->execute(['id' => $product['id']]);
        if ($stmt->fetchColumn()) {
            return $product['id'];
        }

        $stmt = $this->pdo->prepare("
            INSERT INTO products (id, name, in_stock, description, brand, category_id)
            VALUES (:id, :name, :in_stock, :description, :brand, :category_id)
        ");

        $stmt->execute([
            'id' => $product['id'],
            'name' => $product['name'],
            'in_stock' => $product['inStock'],
            'description' => $product['description'],
            'brand' => $product['brand'],
            'category_id' => $categoryId,
        ]);

        return $product['id'];
    }

    private function insertAttribute(array $attribute): int
    {
        $stmt = $this->pdo->prepare("SELECT id FROM attributes WHERE name = :name");
        $stmt->execute(['name' => $attribute['name']]);
        $existing = $stmt->fetchColumn();

        if ($existing) {
            return (int) $existing;
        }

        $stmt = $this->pdo->prepare("
            INSERT INTO attributes (name, type)
            VALUES (:name, :type)
        ");

        $stmt->execute([
            'name' => $attribute['name'],
            'type' => $attribute['type'],
        ]);

        $attributeId = (int) $this->pdo->lastInsertId();

        foreach ($attribute['items'] as $item) {
            $this->insertAttributeItem($attributeId, $item);
        }

        return $attributeId;
    }

    private function insertAttributeItem(int $attributeId, array $item): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO attribute_items (id, attribute_id, display_value, value)
            VALUES (:id, :attribute_id, :display_value, :value)
        ");

        $stmt->execute([
            'id' => $item['id'],
            'attribute_id' => $attributeId,
            'display_value' => $item['displayValue'],
            'value' => $item['value'],
        ]);
    }

    private function linkProductAttribute(int $productId, int $attributeId): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO product_attribute_sets (product_id, attribute_id)
            VALUES (:product_id, :attribute_id)
        ");

        $stmt->execute([
            'product_id' => $productId,
            'attribute_id' => $attributeId,
        ]);
    }

    private function insertImage(int $productId, string $imageUrl): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO product_images (product_id, image_url)
            VALUES (:product_id, :image_url)
        ");

        $stmt->execute([
            'product_id' => $productId,
            'image_url' => $imageUrl,
        ]);
    }

    private function insertPrice(int $productId, array $price): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO prices (product_id, amount, currency_label, currency_symbol)
            VALUES (:product_id, :amount, :label, :symbol)
        ");

        $stmt->execute([
            'product_id' => $productId,
            'amount' => $price['amount'],
            'label' => $price['currency']['label'],
            'symbol' => $price['currency']['symbol'],
        ]);
    }
}
