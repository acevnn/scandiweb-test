<?php

namespace App\Models\Product;

class ClothesProduct extends Product
{
    public function getType(): string
    {
        return 'clothes';
    }
}
