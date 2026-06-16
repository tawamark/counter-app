<?php

namespace App\DTOs;

final readonly class StockMovementData
{
    public function __construct(
        public int $productId,
        public string $type,
        public float $quantity,
        public ?string $reason,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            productId: (int) $data['product_id'],
            type: $data['type'],
            quantity: (float) $data['quantity'],
            reason: $data['reason'] ?? null,
        );
    }
}
