<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Company;
use App\Models\InventoryCount;
use App\Models\InventoryCountItem;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventorySchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_inventory_models_can_be_related(): void
    {
        $company = Company::create([
            'name' => 'Counter Demo',
        ]);

        $user = User::create([
            'company_id' => $company->id,
            'name' => 'Administrador',
            'email' => 'admin@counter.test',
            'password' => 'password',
            'role' => 'admin',
        ]);

        $category = Category::create([
            'company_id' => $company->id,
            'name' => 'Eletrônicos',
        ]);

        $supplier = Supplier::create([
            'company_id' => $company->id,
            'name' => 'Fornecedor Demo',
        ]);

        $product = Product::create([
            'company_id' => $company->id,
            'category_id' => $category->id,
            'supplier_id' => $supplier->id,
            'name' => 'Notebook',
            'sku' => 'NOTE-001',
            'barcode' => '789000000001',
            'unit' => 'un',
            'cost_price' => 2500,
            'sale_price' => 3200,
            'current_quantity' => 10,
        ]);

        $movement = StockMovement::create([
            'company_id' => $company->id,
            'product_id' => $product->id,
            'user_id' => $user->id,
            'type' => 'entry',
            'quantity' => 10,
            'quantity_before' => 0,
            'quantity_after' => 10,
            'reason' => 'Carga inicial',
        ]);

        $inventoryCount = InventoryCount::create([
            'company_id' => $company->id,
            'created_by' => $user->id,
            'title' => 'Contagem inicial',
            'status' => 'open',
        ]);

        $item = InventoryCountItem::create([
            'inventory_count_id' => $inventoryCount->id,
            'product_id' => $product->id,
            'counted_by' => $user->id,
            'system_quantity' => 10,
            'counted_quantity' => 9,
            'difference' => -1,
            'sync_status' => 'synced',
        ]);

        $this->assertTrue($company->products()->whereKey($product->id)->exists());
        $this->assertTrue($category->products()->whereKey($product->id)->exists());
        $this->assertTrue($supplier->products()->whereKey($product->id)->exists());
        $this->assertSame($product->id, $movement->product->id);
        $this->assertSame($inventoryCount->id, $item->inventoryCount->id);
        $this->assertSame($user->id, $item->counter->id);
    }
}
