<?php

namespace App\GraphQL\Types;

use App\Services\AttributeService;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class ProductType
{
    public static function getType(AttributeService $attributeService): ObjectType
    {
        return new ObjectType([
            'name' => 'Product',
            'fields' => fn(): array => [
                'id' => [
                    'type' => Type::nonNull(Type::string()),
                    'resolve' => fn($product) => $product->getId(),
                ],
                'name' => [
                    'type' => Type::string(),
                    'resolve' => fn($product) => $product->getName(),
                ],
                'inStock' => [
                    'type' => Type::boolean(),
                    'resolve' => fn($product) => $product->isInStock(),
                ],
                'brand' => [
                    'type' => Type::string(),
                    'resolve' => fn($product) => $product->getBrand(),
                ],
                'gallery' => [
                    'type' => Type::listOf(Type::string()),
                    'resolve' => fn($product) => $product->getGallery(),
                ],
                'description' => [
                    'type' => Type::string(),
                    'resolve' => fn($product) => $product->getDescription(),
                ],
                'category' => [
                    'type' => Type::string(),
                    'resolve' => fn($product) => $product->getCategoryName(),
                ],
                'attributes' => [
                    'type' => Type::listOf(AttributeType::getType()),
                    'resolve' => fn($product) => $attributeService->getAttributesByProductId(
                        $product->getId(),
                        $product->getCategory()
                    ),
                ],
                'prices' => [
                    'type' => Type::listOf(PriceType::getType()),
                    'resolve' => fn($product) => $product->getPrices(),
                ],

                'categoryId' => [
                    'type' => Type::int(),
                    'resolve' => fn($product) => $product->getCategoryId(),
                ],
            ],
        ]);
    }
}
