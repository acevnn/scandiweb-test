<?php

namespace App\Models\Product;

class TechProduct extends Product
{
    public function getType(): string
    {
        return 'tech';
    }
}
