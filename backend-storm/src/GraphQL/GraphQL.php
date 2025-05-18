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
            $pdo = new Database()->getConnection();
            $categoryService = new CategoryService($pdo);
            $attributeService = new AttributeService($pdo);
            $productService = new ProductService($pdo);
            $orderService = new OrderService($pdo);

            $productType = ProductType::getType($attributeService);

            $queryType = new ObjectType([
                'name' => 'Query',
                'fields' => fn(): array => [
                    'ping' => [
                        'type' => Type::string(),
                        'resolve' => fn() => 'pong',
                    ],
                    'categories' => [
                        'type' => Type::listOf(CategoryType::getType()),
                        'resolve' => fn() => $categoryService->getAll(),
                    ],
                    'product' => [
                        'type' => $productType,
                        'args' => [
                            'id' => Type::nonNull(Type::string()),
                        ],
                        'resolve' => fn($root, $args) => $productService->getById($args['id']),
                    ],
                    'productsByCategory' => [
                        'type' => Type::listOf($productType),
                        'args' => [
                            'name' => ['type' => Type::nonNull(Type::string())],
                        ],
                        'resolve' => fn($root, $args) => $productService->getByCategory($args['name']),
                    ],
                    'products' => [
                        'type' => Type::listOf($productType),
                        'resolve' => fn() => $productService->getAll(),
                    ],
                ],
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
                        'resolve' => fn($root, $args) =>
                        $orderService->createOrder($args['productId'], $args['quantity']),
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
