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
                'fields' => fn(): array => [
                    'id' => ['type' => Type::nonNull(Type::int())],
                    'name' => ['type' => Type::nonNull(Type::string())],
                ],
            ]);
        }

        return self::$instance;
    }
}
