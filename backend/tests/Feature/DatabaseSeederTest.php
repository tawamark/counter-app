<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\InventoryCount;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_creates_demo_data(): void
    {
        $this->seed();
        $this->seed();

        $this->assertDatabaseHas('users', [
            'email' => 'admin@counter.test',
            'role' => 'admin',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'estoquista@counter.test',
            'role' => 'stockist',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'contador@counter.test',
            'role' => 'counter',
        ]);

        $this->assertSame(3, User::count());
        $this->assertSame(6, Category::count());
        $this->assertSame(5, Supplier::count());
        $this->assertSame(20, Product::count());
        $this->assertSame(40, StockMovement::count());
        $this->assertSame(4, InventoryCount::count());
        $this->assertSame(17, InventoryCount::withCount('items')->get()->sum('items_count'));

        $this->assertDatabaseHas('inventory_count_items', [
            'system_quantity' => 10,
            'counted_quantity' => 7,
            'difference' => -3,
            'sync_status' => 'synced',
        ]);

        $this->assertDatabaseHas('inventory_count_items', [
            'system_quantity' => 15,
            'counted_quantity' => 18,
            'difference' => 3,
            'sync_status' => 'synced',
        ]);

        $this->assertDatabaseHas('inventory_counts', [
            'title' => 'Contagem almoxarifado',
            'status' => 'open',
        ]);

        $this->assertDatabaseHas('inventory_counts', [
            'title' => 'Contagem escritório',
            'status' => 'finished',
        ]);

        $this->assertDatabaseHas('inventory_counts', [
            'title' => 'Contagem limpeza aprovada',
            'status' => 'approved',
        ]);
    }
}
