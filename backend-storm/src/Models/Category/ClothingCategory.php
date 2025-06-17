<?php

namespace App\Models\Category;

class ClothingCategory extends Category
{
    public function getType(): string
    {
        return 'clothing';
    }
}
