<?php

namespace App\Models\Attribute;

class BooleanAttribute extends Attribute
{
    public function getType(): string
    {
        return 'boolean';
    }
}
