<?php

namespace App\Models\Attribute;

abstract class Attribute
{
    protected string $name;
    protected string $type;
    protected array $items;

    public function __construct(string $name, string $type, array $items = [])
    {
        $this->name = $name;
        $this->type = $type;
        $this->items = $items;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    abstract public function getType(): string;
}
