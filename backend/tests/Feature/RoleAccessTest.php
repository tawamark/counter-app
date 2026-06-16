<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\InventoryCount;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_restricted_web_resources(): void
    {
        $admin = $this->createUser('admin');

        $this->actingAs($admin)
            ->get('/categories')
            ->assertOk();

        $this->actingAs($admin)
            ->get('/divergences')
            ->assertOk();

        $this->actingAs($admin)
            ->get('/products/create')
            ->assertOk();
    }

    public function test_stockist_can_view_products_and_manage_stock_movements_only(): void
    {
        $stockist = $this->createUser('stockist');

        $this->actingAs($stockist)
            ->get('/products')
            ->assertOk();

        $this->actingAs($stockist)
            ->get('/stock-movements')
            ->assertOk();

        $this->actingAs($stockist)
            ->get('/products/create')
            ->assertForbidden();

        $this->actingAs($stockist)
            ->get('/inventory-counts')
            ->assertForbidden();

        $this->actingAs($stockist)
            ->get('/divergences')
            ->assertForbidden();
    }

    public function test_counter_can_access_inventory_counts_only_on_web(): void
    {
        [$counter, $count] = $this->createInventoryCount('counter');

        $this->actingAs($counter)
            ->get('/inventory-counts')
            ->assertOk();

        $this->actingAs($counter)
            ->get("/inventory-counts/{$count->id}")
            ->assertOk()
            ->assertDontSee('Aprovar ajustes');

        $this->actingAs($counter)
            ->get('/products')
            ->assertForbidden();

        $this->actingAs($counter)
            ->get('/stock-movements')
            ->assertForbidden();
    }

    public function test_api_inventory_counts_are_restricted_to_admin_and_counter(): void
    {
        [$stockist, $count] = $this->createInventoryCount('stockist');

        $this->actingAs($stockist, 'sanctum')
            ->getJson('/api/products')
            ->assertOk();

        $this->actingAs($stockist, 'sanctum')
            ->getJson('/api/inventory-counts')
            ->assertForbidden()
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Acesso não autorizado');

        $this->actingAs($stockist, 'sanctum')
            ->postJson("/api/inventory-counts/{$count->id}/sync", [
                'items' => [],
            ])
            ->assertForbidden();
    }

    public function test_counter_can_sync_inventory_count_items_by_api(): void
    {
        [$counter, $count, $item] = $this->createInventoryCount('counter');

        $this->actingAs($counter, 'sanctum')
            ->postJson("/api/inventory-counts/{$count->id}/sync", [
                'items' => [
                    [
                        'id' => $item->id,
                        'counted_quantity' => 4,
                    ],
                ],
            ])
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    private function createInventoryCount(string $role): array
    {
        $user = $this->createUser($role);

        $product = Product::create([
            'company_id' => $user->company_id,
            'name' => 'Notebook',
            'sku' => 'NOTE-'.$role,
            'current_quantity' => 5,
        ]);

        $count = InventoryCount::create([
            'company_id' => $user->company_id,
            'created_by' => $user->id,
            'title' => 'Contagem semanal',
            'status' => 'open',
            'started_at' => now(),
        ]);

        $item = $count->items()->create([
            'product_id' => $product->id,
            'system_quantity' => 5,
            'difference' => 0,
            'sync_status' => 'pending',
        ]);

        return [$user, $count, $item];
    }

    private function createUser(string $role): User
    {
        $company = Company::create([
            'name' => 'Counter Demo '.$role,
        ]);

        return User::create([
            'company_id' => $company->id,
            'name' => 'Usuário '.$role,
            'email' => $role.'@counter.test',
            'password' => 'password',
            'role' => $role,
        ]);
    }
}
