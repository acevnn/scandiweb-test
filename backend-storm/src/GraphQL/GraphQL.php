<?php

namespace App\GraphQL;

use App\Config\Database;
use App\GraphQL\Types\CategoryType;
use App\GraphQL\Types\ProductType;
use App\Services\AttributeService;
use App\Services\CategoryService;
use App\Services\OrderService;
use App\Services\ProductService;
use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use RuntimeException;
use Throwable;

class GraphQL
{
    public static function handle(): string
    {
        try {
            $pdo = (new Database())->getConnection();
            $categoryService = new CategoryService($pdo);
            $attributeService = new AttributeService($pdo);
            $productService = new ProductService($pdo);
            $orderService = new OrderService($pdo);

            $productType = ProductType::getType($attributeService);

            $queryType = new ObjectType([
                'name' => 'Query',
                'fields' => function () use ($categoryService, $productService, $productType) {
                    return [
                        'ping' => [
                            'type' => Type::string(),
                            'resolve' => function () {
                                return 'pong';
                            },
                        ],
                        'categories' => [
                            'type' => Type::listOf(CategoryType::getType()),
                            'resolve' => function () use ($categoryService) {
                                return $categoryService->getAll();
                            },
                        ],
                        'product' => [
                            'type' => $productType,
                            'args' => [
                                'id' => Type::nonNull(Type::string()),
                            ],
                            'resolve' => function ($root, $args) use ($productService) {
                                return $productService->getById($args['id']);
                            },
                        ],
                        'productsByCategory' => [
                            'type' => Type::listOf($productType),
                            'args' => [
                                'name' => [
                                    'type' => Type::nonNull(Type::string()),
                                ],
                            ],
                            'resolve' => function ($root, $args) use ($productService) {
                                return $productService->getByCategory($args['name']);
                            },
                        ],
                        'products' => [
                            'type' => Type::listOf($productType),
                            'resolve' => function () use ($productService) {
                                return $productService->getAll();
                            },
                        ],
                    ];
                },
            ]);

            $mutationType = new ObjectType([
                'name' => 'Mutation',
                'fields' => [
                    'createOrder' => [
                        'type' => Type::boolean(),
                        'args' => [
                            'productId' => Type::nonNull(Type::string()),
                            'quantity' => Type::nonNull(Type::int()),
                        ],
                        'resolve' => function ($root, $args) use ($orderService) {
                            return $orderService->createOrder($args['productId'], $args['quantity']);
                        },
                    ],
                ],
            ]);

            $schema = new Schema([
                'query' => $queryType,
                'mutation' => $mutationType,
            ]);

            $rawInput = file_get_contents('php://input');
            if ($rawInput === false) {
                throw new RuntimeException('Failed to get php://input');
            }

            $input = json_decode($rawInput, true);
            if (!isset($input['query'])) {
                throw new RuntimeException('No query found in request body');
            }

            $query = $input['query'];
            $variableValues = $input['variables'] ?? null;

            $result = GraphQLBase::executeQuery($schema, $query, null, null, $variableValues);
            $output = $result->toArray();
        } catch (Throwable $e) {
            $output = [
                'errors' => [
                    ['message' => $e->getMessage()],
                ],
            ];
        }

        header('Content-Type: application/json; charset=UTF-8');
        return json_encode($output);
    }
}
