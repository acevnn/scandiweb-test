<?php

namespace App\Models\Category;

class AllCategory extends Category
{
    public function getName(): string
    {
        return 'all';
    }

    public function getType(): string
    {
        return 'all';
    }
}
