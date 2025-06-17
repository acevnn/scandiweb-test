<?php

namespace App\Services;

use PDO;
use App\Factories\CategoryFactory;
use App\Models\Category\Category;

class CategoryService
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    public function getAll(): array
    {
        try {
            $stmt = $this->pdo->query("SELECT id, name FROM categories");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $categories = [];

            foreach ($rows as $row) {
                try {
                    $categories[] = CategoryFactory::create($row['name'], (int) $row['id']);
                } catch (\InvalidArgumentException $e) {
                    error_log("[CategoryService] Skipping unknown category: {$row['name']}");
                }
            }

            return $categories;
        } catch (\Throwable $e) {
            error_log("[CategoryService] Failed to fetch categories: " . $e->getMessage());
            return [];
        }
    }
}
