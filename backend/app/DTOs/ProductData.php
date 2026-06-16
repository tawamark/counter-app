<?php

namespace App\DTOs;

final readonly class ProductData
{
    public function __construct(
        public ?int $categoryId,
        public ?int $supplierId,
        public string $name,
        public ?string $description,
        public string $sku,
        public ?string $barcode,
        public string $unit,
        public float $costPrice,
        public float $salePrice,
        public float $currentQuantity,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            categoryId: isset($data['category_id']) ? (int) $data['category_id'] : null,
            supplierId: isset($data['supplier_id']) ? (int) $data['supplier_id'] : null,
            name: $data['name'],
            description: $data['description'] ?? null,
            sku: $data['sku'],
            barcode: $data['barcode'] ?? null,
            unit: $data['unit'],
            costPrice: (float) $data['cost_price'],
            salePrice: (float) $data['sale_price'],
            currentQuantity: (float) $data['current_quantity'],
        );
    }

    public function toArray(): array
    {
        return [
            'category_id' => $this->categoryId,
            'supplier_id' => $this->supplierId,
            'name' => $this->name,
            'description' => $this->description,
            'sku' => $this->sku,
            'barcode' => $this->barcode,
            'unit' => $this->unit,
            'cost_price' => $this->costPrice,
            'sale_price' => $this->salePrice,
            'current_quantity' => $this->currentQuantity,
        ];
    }
}
