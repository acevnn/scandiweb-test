<?php

namespace App\Factories;

use App\Models\Category\Category;
use App\Models\Category\AllCategory;
use App\Models\Category\TechCategory;
use App\Models\Category\ClothingCategory;

class CategoryFactory
{
    public static function create(string $name, int $id): Category
    {
        return match (strtolower($name)) {
            'tech' => new TechCategory($id, $name),
            'clothes' => new ClothingCategory($id, $name),
            'all' => new AllCategory($id, $name),
            default => throw new \InvalidArgumentException("[CategoryService] Skipping unknown category: $name"),
        };
    }
}
