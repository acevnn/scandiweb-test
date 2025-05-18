<?php

namespace App\Models\Product;

abstract class Product
{
    protected string $id;
    protected string $name;
    protected string $description;
    protected bool $inStock;
    protected string $brand;
    protected int $categoryId;
    protected array $gallery;
    protected array $prices;
    protected array $data;
    protected string $category;
    protected array $attributes = [];

    public function __construct(array $data)
    {
        $this->data = $data;
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->description = $data['description'];
        $this->inStock = (bool)$data['in_stock'];
        $this->brand = $data['brand'];
        $this->category = $data['category'] ?? '';
        $this->categoryId = (int)$data['category_id'];
        $this->gallery = isset($data['gallery']) ? json_decode($data['gallery'], true) ?? [] : [];
        $this->prices = isset($data['prices']) ? json_decode($data['prices'], true) ?? [] : [];
    }

    abstract public function getType(): string;

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function isInStock(): bool
    {
        return $this->inStock;
    }

    public function getBrand(): string
    {
        return $this->brand;
    }

    public function getCategoryId(): int
    {
        return $this->categoryId;
    }

    public function setGallery(array $gallery): void
    {
        $this->gallery = $gallery;
    }

    public function getGallery(): array
    {
        return $this->gallery ?? [];
    }

    public function setPrices(array $prices): void
    {
        $this->prices = $prices;
    }

    public function getPrices(): array
    {
        return $this->prices ?? [];
    }

    public function getCategoryName(): string
    {
        return $this->data['category_name'] ?? '';
    }
    public function setCategory(string $category): void
    {
        $this->category = $category;
    }

    public function getCategory(): string
    {
        return $this->category ?? '';
    }

    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
