<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\InventoryCount;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryCountTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_inventory_count_with_selected_products(): void
    {
        [$user, $firstProduct] = $this->createUserAndProduct(5);
        $secondProduct = Product::create([
            'company_id' => $user->company_id,
            'name' => 'Mouse',
            'sku' => 'MOU-001',
            'current_quantity' => 8,
        ]);

        $response = $this->actingAs($user)
            ->post('/inventory-counts', [
                'title' => 'Contagem geral',
                'product_ids' => [$firstProduct->id, $secondProduct->id],
            ]);

        $count = InventoryCount::first();

        $response->assertRedirect("/inventory-counts/{$count->id}");

        $this->assertDatabaseHas('inventory_counts', [
            'company_id' => $user->company_id,
            'created_by' => $user->id,
            'title' => 'Contagem geral',
            'status' => 'open',
        ]);

        $this->assertDatabaseHas('inventory_count_items', [
            'inventory_count_id' => $count->id,
            'product_id' => $firstProduct->id,
            'system_quantity' => 5,
            'difference' => 0,
            'sync_status' => 'pending',
        ]);

        $this->assertDatabaseHas('inventory_count_items', [
            'inventory_count_id' => $count->id,
            'product_id' => $secondProduct->id,
            'system_quantity' => 8,
        ]);
    }

    public function test_user_can_view_inventory_count_list_and_detail(): void
    {
        [$user, $product] = $this->createUserAndProduct(5);

        $count = InventoryCount::create([
            'company_id' => $user->company_id,
            'created_by' => $user->id,
            'title' => 'Contagem semanal',
            'status' => 'open',
            'started_at' => now(),
        ]);

        $count->items()->create([
            'product_id' => $product->id,
            'system_quantity' => 5,
            'difference' => 0,
            'sync_status' => 'pending',
        ]);

        $this->actingAs($user)
            ->get('/inventory-counts')
            ->assertOk()
            ->assertSee('Contagem semanal');

        $this->actingAs($user)
            ->get("/inventory-counts/{$count->id}")
            ->assertOk()
            ->assertSee('Contagem semanal')
            ->assertSee('Notebook');
    }

    public function test_user_cannot_include_product_from_another_company(): void
    {
        [$user] = $this->createUserAndProduct(5);
        [, $otherProduct] = $this->createUserAndProduct(4, 'Outra empresa', 'outro@counter.test');

        $this->actingAs($user)
            ->post('/inventory-counts', [
                'title' => 'Contagem inválida',
                'product_ids' => [$otherProduct->id],
            ])
            ->assertSessionHasErrors('product_ids.0');
    }

    public function test_user_cannot_view_inventory_count_from_another_company(): void
    {
        [$user] = $this->createUserAndProduct(5);
        [$otherUser] = $this->createUserAndProduct(4, 'Outra empresa', 'outro@counter.test');

        $count = InventoryCount::create([
            'company_id' => $otherUser->company_id,
            'created_by' => $otherUser->id,
            'title' => 'Contagem restrita',
            'status' => 'open',
        ]);

        $this->actingAs($user)
            ->get("/inventory-counts/{$count->id}")
            ->assertNotFound();
    }

    public function test_user_can_update_counted_quantities_and_differences(): void
    {
        [$user, $product] = $this->createUserAndProduct(10);

        $count = InventoryCount::create([
            'company_id' => $user->company_id,
            'created_by' => $user->id,
            'title' => 'Contagem semanal',
            'status' => 'open',
            'started_at' => now(),
        ]);

        $item = $count->items()->create([
            'product_id' => $product->id,
            'system_quantity' => 10,
            'difference' => 0,
            'sync_status' => 'pending',
        ]);

        $this->actingAs($user)
            ->post("/inventory-counts/{$count->id}/items", [
                'items' => [
                    [
                        'id' => $item->id,
                        'counted_quantity' => 7,
                    ],
                ],
            ])
            ->assertRedirect("/inventory-counts/{$count->id}");

        $this->assertDatabaseHas('inventory_counts', [
            'id' => $count->id,
            'status' => 'in_progress',
        ]);

        $this->assertDatabaseHas('inventory_count_items', [
            'id' => $item->id,
            'counted_by' => $user->id,
            'counted_quantity' => 7,
            'difference' => -3,
            'sync_status' => 'synced',
        ]);
    }

    public function test_user_can_clear_counted_quantity(): void
    {
        [$user, $product] = $this->createUserAndProduct(10);

        $count = InventoryCount::create([
            'company_id' => $user->company_id,
            'created_by' => $user->id,
            'title' => 'Contagem semanal',
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        $item = $count->items()->create([
            'product_id' => $product->id,
            'counted_by' => $user->id,
            'system_quantity' => 10,
            'counted_quantity' => 7,
            'difference' => -3,
            'sync_status' => 'synced',
            'counted_at' => now(),
        ]);

        $this->actingAs($user)
            ->post("/inventory-counts/{$count->id}/items", [
                'items' => [
                    [
                        'id' => $item->id,
                        'counted_quantity' => null,
                    ],
                ],
            ])
            ->assertRedirect("/inventory-counts/{$count->id}");

        $item->refresh();

        $this->assertNull($item->counted_by);
        $this->assertNull($item->counted_quantity);
        $this->assertNull($item->counted_at);
        $this->assertSame('0.000', $item->difference);
        $this->assertSame('pending', $item->sync_status);
    }

    public function test_user_cannot_update_finished_inventory_count(): void
    {
        [$user, $product] = $this->createUserAndProduct(10);

        $count = InventoryCount::create([
            'company_id' => $user->company_id,
            'created_by' => $user->id,
            'title' => 'Contagem finalizada',
            'status' => 'finished',
            'started_at' => now(),
            'finished_at' => now(),
        ]);

        $item = $count->items()->create([
            'product_id' => $product->id,
            'system_quantity' => 10,
            'difference' => 0,
            'sync_status' => 'pending',
        ]);

        $this->actingAs($user)
            ->post("/inventory-counts/{$count->id}/items", [
                'items' => [
                    [
                        'id' => $item->id,
                        'counted_quantity' => 7,
                    ],
                ],
            ])
            ->assertSessionHasErrors('items');

        $this->assertDatabaseHas('inventory_count_items', [
            'id' => $item->id,
            'counted_quantity' => null,
            'difference' => 0,
            'sync_status' => 'pending',
        ]);
    }

    public function test_user_cannot_finish_inventory_count_with_pending_items(): void
    {
        [$user, $product] = $this->createUserAndProduct(10);

        $count = InventoryCount::create([
            'company_id' => $user->company_id,
            'created_by' => $user->id,
            'title' => 'Contagem semanal',
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        $count->items()->create([
            'product_id' => $product->id,
            'system_quantity' => 10,
            'difference' => 0,
            'sync_status' => 'pending',
        ]);

        $this->actingAs($user)
            ->post("/inventory-counts/{$count->id}/finish")
            ->assertSessionHasErrors('count');

        $this->assertDatabaseHas('inventory_counts', [
            'id' => $count->id,
            'status' => 'in_progress',
            'finished_at' => null,
        ]);
    }

    public function test_user_can_finish_inventory_count_when_all_items_are_counted(): void
    {
        [$user, $product] = $this->createUserAndProduct(10);

        $count = InventoryCount::create([
            'company_id' => $user->company_id,
            'created_by' => $user->id,
            'title' => 'Contagem semanal',
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        $count->items()->create([
            'product_id' => $product->id,
            'counted_by' => $user->id,
            'system_quantity' => 10,
            'counted_quantity' => 7,
            'difference' => -3,
            'sync_status' => 'synced',
            'counted_at' => now(),
        ]);

        $this->actingAs($user)
            ->post("/inventory-counts/{$count->id}/finish")
            ->assertRedirect("/inventory-counts/{$count->id}");

        $this->assertDatabaseHas('inventory_counts', [
            'id' => $count->id,
            'status' => 'finished',
        ]);

        $this->assertNotNull($count->refresh()->finished_at);
    }

    public function test_user_can_approve_finished_inventory_count_adjustments(): void
    {
        [$user, $product] = $this->createUserAndProduct(10);

        $count = InventoryCount::create([
            'company_id' => $user->company_id,
            'created_by' => $user->id,
            'title' => 'Contagem semanal',
            'status' => 'finished',
            'started_at' => now(),
            'finished_at' => now(),
        ]);

        $count->items()->create([
            'product_id' => $product->id,
            'counted_by' => $user->id,
            'system_quantity' => 10,
            'counted_quantity' => 7,
            'difference' => -3,
            'sync_status' => 'synced',
            'counted_at' => now(),
        ]);

        $this->actingAs($user)
            ->post("/inventory-counts/{$count->id}/approve")
            ->assertRedirect("/inventory-counts/{$count->id}");

        $this->assertDatabaseHas('inventory_counts', [
            'id' => $count->id,
            'status' => 'approved',
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'current_quantity' => 7,
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'company_id' => $user->company_id,
            'product_id' => $product->id,
            'user_id' => $user->id,
            'inventory_count_id' => $count->id,
            'type' => 'adjustment',
            'quantity' => 7,
            'quantity_before' => 10,
            'quantity_after' => 7,
            'reason' => 'Ajuste aprovado pela contagem Contagem semanal',
        ]);

        $this->assertSame(1, StockMovement::count());
        $this->assertNotNull($count->refresh()->approved_at);

        $this->actingAs($user)
            ->get("/inventory-counts/{$count->id}")
            ->assertOk()
            ->assertSee('Ajustes aprovados')
            ->assertSee('Notebook');
    }

    public function test_user_cannot_approve_inventory_count_before_finish(): void
    {
        [$user, $product] = $this->createUserAndProduct(10);

        $count = InventoryCount::create([
            'company_id' => $user->company_id,
            'created_by' => $user->id,
            'title' => 'Contagem semanal',
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        $count->items()->create([
            'product_id' => $product->id,
            'counted_by' => $user->id,
            'system_quantity' => 10,
            'counted_quantity' => 7,
            'difference' => -3,
            'sync_status' => 'synced',
            'counted_at' => now(),
        ]);

        $this->actingAs($user)
            ->post("/inventory-counts/{$count->id}/approve")
            ->assertSessionHasErrors('count');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'current_quantity' => 10,
        ]);

        $this->assertSame(0, StockMovement::count());
    }

    private function createUserAndProduct(float $quantity, string $companyName = 'Counter Demo', string $email = 'admin@counter.test'): array
    {
        $company = Company::create([
            'name' => $companyName,
        ]);

        $user = User::create([
            'company_id' => $company->id,
            'name' => 'Administrador',
            'email' => $email,
            'password' => 'password',
            'role' => 'admin',
        ]);

        $product = Product::create([
            'company_id' => $company->id,
            'name' => 'Notebook',
            'sku' => 'NOTE-'.str_replace('@counter.test', '', $email),
            'current_quantity' => $quantity,
        ]);

        return [$user, $product];
    }
}
