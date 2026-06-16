<?php

namespace Tests\Unit;

use App\DTOs\InventoryCountData;
use App\DTOs\ProductData;
use App\DTOs\StockMovementData;
use PHPUnit\Framework\TestCase;

class DtoTest extends TestCase
{
    public function test_product_data_converts_request_values(): void
    {
        $data = ProductData::fromArray([
            'category_id' => '1',
            'supplier_id' => '2',
            'name' => 'Notebook',
            'description' => 'Produto para teste',
            'sku' => 'NOTE-001',
            'barcode' => '789001',
            'unit' => 'un',
            'cost_price' => '2500.50',
            'sale_price' => '3200.90',
            'current_quantity' => '10.5',
        ]);

        $this->assertSame(1, $data->categoryId);
        $this->assertSame(2, $data->supplierId);
        $this->assertSame(2500.50, $data->costPrice);
        $this->assertSame(10.5, $data->currentQuantity);
        $this->assertSame('NOTE-001', $data->toArray()['sku']);
    }

    public function test_stock_movement_data_converts_request_values(): void
    {
        $data = StockMovementData::fromArray([
            'product_id' => '4',
            'type' => 'entry',
            'quantity' => '7.25',
            'reason' => 'Reposição',
        ]);

        $this->assertSame(4, $data->productId);
        $this->assertSame('entry', $data->type);
        $this->assertSame(7.25, $data->quantity);
        $this->assertSame('Reposição', $data->reason);
    }

    public function test_inventory_count_data_converts_request_values(): void
    {
        $data = InventoryCountData::fromArray([
            'title' => 'Contagem semanal',
            'product_ids' => ['1', '2', '3'],
        ]);

        $this->assertSame('Contagem semanal', $data->title);
        $this->assertSame([1, 2, 3], $data->productIds);
    }
}
