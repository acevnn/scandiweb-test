<?php

namespace App\Services;

use PDO;

class DataImporter
{
    private PDO $pdo;
    private array $categoryMap = [];
    private array $attributeMap = [];

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function import(string $jsonPath): void
    {
        $data = json_decode(file_get_contents($jsonPath), true)['data'];

        foreach ($data['products'] as &$product) {
            if ($product['category'] === 'shoes') {
                $product['category'] = 'clothes';
            }
        }
        unset($product);

        $this->clearExistingData();
        $this->importCategories($data['categories'], $data['products']);
        $this->importProducts($data['products']);
    }

    private function clearExistingData(): void
    {
        $this->pdo->exec("DELETE FROM product_attribute_sets");
        $this->pdo->exec("DELETE FROM attribute_items");
        $this->pdo->exec("DELETE FROM attributes");
        $this->pdo->exec("DELETE FROM prices");
        $this->pdo->exec("DELETE FROM product_images");
        $this->pdo->exec("DELETE FROM products");
        $this->pdo->exec("DELETE FROM categories");
    }

    private function importCategories(array $staticCategories, array $products): void
    {
        $unique = [];

        foreach ($staticCategories as $c) {
            $name = $c['name'] === 'shoes' ? 'clothes' : $c['name'];
            $unique[$name] = true;
        }

        foreach ($products as $p) {
            $name = $p['category'];
            $unique[$name] = true;
        }

        foreach (array_keys($unique) as $categoryName) {
            $stmt = $this->pdo->prepare("INSERT IGNORE INTO categories (name) VALUES (:name)");
            $stmt->execute([':name' => $categoryName]);

            $stmt = $this->pdo->prepare("SELECT id FROM categories WHERE name = :name");
            $stmt->execute([':name' => $categoryName]);
            $this->categoryMap[$categoryName] = (int) $stmt->fetchColumn();
        }
    }

    private function importProducts(array $products): void
    {
        foreach ($products as $product) {
            $categoryId = $this->categoryMap[$product['category']] ?? null;

            $this->insertProduct($product, $categoryId);
            $this->insertImages($product['id'], $product['gallery']);
            $this->insertPrices($product['id'], $product['prices']);
            $this->insertAttributes($product['id'], $product['attributes']);
        }
    }

    private function insertProduct(array $product, int $categoryId): void
    {
        $stmt = $this->pdo->prepare("
            INSERT IGNORE INTO products (id, name, description, in_stock, brand, category_id)
            VALUES (:id, :name, :description, :in_stock, :brand, :category_id)
        ");

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
        $stmt = $this->pdo->prepare("
            INSERT IGNORE INTO product_images (product_id, image_url)
            VALUES (:product_id, :image_url)
        ");

        foreach ($images as $image) {
            $stmt->execute([':product_id' => $productId, ':image_url' => $image]);
        }
    }

    private function insertPrices(string $productId, array $prices): void
    {
        $stmt = $this->pdo->prepare("
            INSERT IGNORE INTO prices (product_id, amount, currency_label, currency_symbol)
            VALUES (:product_id, :amount, :label, :symbol)
        ");

        foreach ($prices as $price) {
            $stmt->execute([
                ':product_id' => $productId,
                ':amount' => $price['amount'],
                ':label' => $price['currency']['label'],
                ':symbol' => $price['currency']['symbol'],
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
        if (isset($this->attributeMap[$name])) {
            return $this->attributeMap[$name];
        }

        $stmt = $this->pdo->prepare("INSERT IGNORE INTO attributes (name, type) VALUES (:name, :type)");
        $stmt->execute([':name' => $name, ':type' => $type]);

        $id = $this->pdo->lastInsertId();

        if (!$id) {
            $stmt = $this->pdo->prepare("SELECT id FROM attributes WHERE name = :name");
            $stmt->execute([':name' => $name]);
            $id = $stmt->fetchColumn();
        }

        return $this->attributeMap[$name] = (int) $id;
    }

    private function insertAttributeItems(int $attributeId, array $items): void
    {
        $stmtCheck = $this->pdo->prepare("
            SELECT COUNT(*) FROM attribute_items
            WHERE attribute_id = :attr_id AND item_key = :item_key
        ");

        $stmtInsert = $this->pdo->prepare("
            INSERT INTO attribute_items (attribute_id, display_value, value, item_key)
            VALUES (:attr_id, :display, :value, :item_key)
        ");

        foreach ($items as $item) {
            $stmtCheck->execute([
                ':attr_id' => $attributeId,
                ':item_key' => $item['id'],
            ]);

            if ((int) $stmtCheck->fetchColumn() === 0) {
                $stmtInsert->execute([
                    ':attr_id' => $attributeId,
                    ':display' => $item['displayValue'],
                    ':value' => $item['value'],
                    ':item_key' => $item['id'],
                ]);
            }
        }
    }

    private function linkProductAttribute(string $productId, int $attributeId): void
    {
        $stmt = $this->pdo->prepare("
            INSERT IGNORE INTO product_attribute_sets (product_id, attribute_id)
            VALUES (:pid, :aid)
        ");
        $stmt->execute([':pid' => $productId, ':aid' => $attributeId]);
    }
}
