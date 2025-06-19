<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class CategoryType
{
    private static ?ObjectType $instance = null;

    public static function getType(): ObjectType
    {
        if (self::$instance === null) {
            self::$instance = new ObjectType([
                'name' => 'Category',
                'fields' => [
                    'id' => [
                        'type' => Type::nonNull(Type::int()),
                        'resolve' => fn($category) => $category->getId(),
                    ],
                    'name' => [
                        'type' => Type::nonNull(Type::string()),
                        'resolve' => fn($category) => $category->getName(),
                    ],
                ],
            ]);
        }

        return self::$instance;
    }
}
