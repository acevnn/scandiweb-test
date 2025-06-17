<?php

namespace App\Models\Category;

class TechCategory extends Category
{
    public function getType(): string
    {
        return 'tech';
    }
}
