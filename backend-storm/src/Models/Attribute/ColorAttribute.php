<?php

namespace App\Models\Attribute;

class ColorAttribute extends Attribute
{
    public function getType(): string
    {
        return 'swatch';
    }
}
