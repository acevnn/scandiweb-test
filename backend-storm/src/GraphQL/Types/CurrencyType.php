<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class CurrencyType
{
    public static function getType(): ObjectType
    {
        return new ObjectType([
            'name' => 'Currency',
            'fields' => [
                'symbol' => Type::string(),
                'label' => Type::string(),
            ],
        ]);
    }
}
