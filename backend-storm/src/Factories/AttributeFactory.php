<?php

namespace App\Factories;

use App\Models\Attribute\Attribute;
use App\Models\Attribute\ColorAttribute;
use App\Models\Attribute\SizeAttribute;
use App\Models\Attribute\GenericAttribute;

class AttributeFactory
{
    public static function create(string $name, string $type, array $items, string $productId = ''): Attribute
    {
        return match (strtolower($name)) {
            'color' => new ColorAttribute($name, $type, $items),
            'size', 'capacity' => (new SizeAttribute($name, $type, $items))->filterItemsByContext($productId),
            default => new GenericAttribute($name, $type, $items),
        };
    }
}
