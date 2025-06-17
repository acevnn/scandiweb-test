<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;

class OrderItemInputType
{
    public static function getType(): InputObjectType
    {
        return new InputObjectType([
            'name' => 'OrderItemInput',
            'fields' => [
                'productId' => Type::nonNull(Type::id()),
                'quantity' => Type::nonNull(Type::int()),
                'selectedAttributes' => Type::nonNull(Type::string()),
            ]
        ]);
    }
}
