<?php

namespace App\Models\Attribute;

class SizeAttribute extends Attribute
{
    public function getType(): string
    {
        return $this->type;
    }

    public function filterItemsByContext(string $productId): static
    {
        $map = [
            'huarache-x-stussy-le' => 'numeric',
            'jacket-canada-goosee' => 'clothing',
        ];

        $filterType = $map[$productId] ?? null;

        if ($filterType === 'numeric') {
            $this->items = array_filter($this->items, fn($item) => is_numeric($item['value']));
        } elseif ($filterType === 'clothing') {
            $validSizes = ['S', 'M', 'L', 'XL'];
            $this->items = array_filter($this->items, fn($item) => in_array(strtoupper($item['value']), $validSizes));
        }

        return $this;
    }
}
