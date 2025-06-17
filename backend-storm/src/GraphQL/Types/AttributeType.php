<?php

namespace App\GraphQL\Types;

use App\Models\Attribute\Attribute;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class AttributeType
{
    public static function getType(): ObjectType
    {
        return new ObjectType([
            'name' => 'Attribute',
            'fields' => [
                'id' => [
                    'type' => Type::string(),
                    'resolve' => fn(Attribute $attribute) => $attribute->getName(), // or getId() if available
                ],
                'name' => [
                    'type' => Type::string(),
                    'resolve' => fn(Attribute $attribute) => $attribute->getName(),
                ],
                'attrType' => [
                    'type' => Type::string(),
                    'resolve' => fn(Attribute $attribute) => $attribute->getType(),
                ],
                'items' => [
                    'type' => Type::listOf(
                        new ObjectType([
                            'name' => 'AttributeItem',
                            'fields' => [
                                'id' => Type::string(),
                                'displayValue' => Type::string(),
                                'value' => Type::string(),
                            ],
                        ])
                    ),
                    'resolve' => fn(Attribute $attribute) => $attribute->getItems(),
                ],
            ],
        ]);
    }
}
