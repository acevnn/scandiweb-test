<?php

namespace App\Models\Attribute;

class GenericAttribute extends Attribute
{
    public function getType(): string
    {
        return $this->type;
    }
}
