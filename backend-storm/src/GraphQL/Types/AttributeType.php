<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class AttributeType
{
    public static function getType(): ObjectType
    {
        return new ObjectType([
            'name' => 'Attribute',
            'fields' => [
                'id' => Type::nonNull(Type::string()),
                'name' => Type::string(),
                'attrType' => [
                    'type' => Type::string(),
                    'resolve' => fn($attribute) => $attribute['type'] ?? null,
                ],
                'items' => [
                    'type' => Type::listOf(new ObjectType([
                        'name' => 'AttributeItem',
                        'fields' => [
                            'id' => Type::string(),
                            'displayValue' => Type::string(),
                            'value' => Type::string(),
                        ],
                    ])),
                    'resolve' => fn($attribute) => $attribute['items'] ?? [],
                ],
            ],
        ]);
    }
}
