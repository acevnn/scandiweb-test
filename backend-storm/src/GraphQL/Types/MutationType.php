<?php

namespace App\GraphQL\Types;

use App\Services\OrderService;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use App\GraphQL\Types\OrderItemInputType;

class MutationType extends ObjectType
{
    public function __construct(OrderService $orderService)
    {
        parent::__construct([
            'name' => 'Mutation',
            'fields' => [
                'placeOrder' => [
                    'type' => Type::boolean(),
                    'args' => [
                        'items' => [
                            'type' => Type::nonNull(Type::listOf(Type::nonNull(OrderItemInputType::getType())))
                        ]
                    ],
                    'resolve' => fn($root, $args) => $orderService->createOrder($args['items']),
                ]
            ]
        ]);
    }
}
