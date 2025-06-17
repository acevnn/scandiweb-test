<?php

namespace App\Services;

use App\Models\Product\ClothesProduct;
use App\Models\Product\Product;
use App\Models\Product\TechProduct;
use PDO;
use PDOException;

class ProductService
{
    private PDO $pdo;
    private AttributeService $attributeService;

    public function __construct(PDO $pdo, AttributeService $attributeService)
    {
        $this->pdo = $pdo;
        $this->attributeService = $attributeService;
    }

    public function getAll(): array
    {
        try {
            $stmt = $this->pdo->query("
    SELECT p.*, c.name AS category_name
    FROM products p
    JOIN categories c ON p.category_id = c.id
");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return array_map(fn(array $row) => $this->hydrateProduct($row), $rows);
        } catch (PDOException $e) {
            error_log("[ProductService] Failed to fetch all products: " . $e->getMessage());
            return [];
        }
    }

    public function getByCategory(string $categoryName): array
    {
        if (strtolower($categoryName) === 'all') {
            return $this->getAll();
        }

        try {
            $stmt = $this->pdo->prepare("
            SELECT p.*, c.name AS category, c.name AS category_name
            FROM products p
            INNER JOIN categories c ON p.category_id = c.id
            WHERE LOWER(c.name) = LOWER(:name)
        ");
            $stmt->execute([':name' => $categoryName]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return array_map(fn(array $row) => $this->hydrateProduct($row), $rows);
        } catch (PDOException $e) {
            error_log("[ProductService] Failed to fetch products by category: " . $e->getMessage());
            return [];
        }
    }



    private function getImagesForProduct(string $productId): array
    {
        try {
            $stmt = $this->pdo->prepare("SELECT image_url FROM product_images WHERE product_id = :productId");
            $stmt->execute(['productId' => $productId]);

            return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'image_url');
        } catch (PDOException $e) {
            error_log("[ProductService] Failed to fetch images for product $productId: " . $e->getMessage());
            return [];
        }
    }

    private function getPricesForProduct(string $productId): array
    {
        try {
            $stmt = $this->pdo->prepare("
            SELECT amount, currency_label AS label, currency_symbol AS symbol
            FROM prices
            WHERE product_id = :productId
        ");
            $stmt->execute(['productId' => $productId]);

            return array_map(fn($row) => [
                'amount' => (float) $row['amount'],
                'currency' => [
                    'label' => $row['label'],
                    'symbol' => $row['symbol'],
                ],
            ], $stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (PDOException $e) {
            error_log("[ProductService] Failed to fetch prices for product $productId: " . $e->getMessage());
            return [];
        }
    }

    public function getById(string $id): ?Product
    {
        try {
            $stmt = $this->pdo->prepare("
            SELECT p.*, c.name AS category
            FROM products p
            INNER JOIN categories c ON p.category_id = c.id
            WHERE p.id = :id
        ");
            $stmt->execute(['id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                error_log("[ProductService] No product found for ID: $id");
                return null;
            }

            return $this->hydrateProduct($row);
        } catch (PDOException $e) {
            error_log("[ProductService] Failed to fetch product by ID: " . $e->getMessage());
            return null;
        }
    }
    private const CATEGORY_MAP = [
        'clothes' => ClothesProduct::class,
        'tech' => TechProduct::class,
    ];

    private function hydrateProduct(array $data): Product
    {
        $categoryName = strtolower($data['category_name'] ?? '');
        $class = self::CATEGORY_MAP[$categoryName] ?? TechProduct::class;
        $product = new $class($data);
        if (method_exists($product, 'setGallery')) {
            $product->setGallery($this->getImagesForProduct($data['id']));
        }

        if (method_exists($product, 'setPrices')) {
            $product->setPrices($this->getPricesForProduct($data['id']));
        }

        if (method_exists($product, 'setCategory') && isset($data['category'])) {
            $product->setCategory($data['category']);
        }

        if (method_exists($product, 'setAttributes')) {
            $product->setAttributes($this->attributeService->getAttributesByProductId($data['id'], $categoryName));
        }

        return $product;
    }
}
