<?php

namespace App\DTOs;

final readonly class InventoryCountData
{
    public function __construct(
        public string $title,
        public array $productIds,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'],
            productIds: array_map('intval', $data['product_ids']),
        );
    }
}
