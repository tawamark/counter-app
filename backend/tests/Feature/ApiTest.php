<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\InventoryCount;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_and_access_me_endpoint(): void
    {
        $user = $this->createUser();

        $login = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.email', $user->email);

        $token = $login->json('data.token');

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/me')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.email', $user->email);
    }

    public function test_api_requires_authentication(): void
    {
        $this->getJson('/api/products')
            ->assertUnauthorized()
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Não autenticado');
    }

    public function test_api_validation_errors_follow_contract(): void
    {
        $user = $this->createUser();

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/products?per_page=101')
            ->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Erro de validação')
            ->assertJsonStructure([
                'success',
                'message',
                'errors' => [
                    'per_page',
                ],
            ]);
    }

    public function test_api_forbidden_errors_follow_contract(): void
    {
        $user = $this->createUser(role: 'stockist');

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/mobile/summary')
            ->assertForbidden()
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Acesso não autorizado')
            ->assertJsonPath('errors', []);
    }

    public function test_user_can_list_and_search_products_with_pagination(): void
    {
        $user = $this->createUser();

        Product::create([
            'company_id' => $user->company_id,
            'name' => 'Notebook',
            'sku' => 'NOTE-001',
            'barcode' => '789001',
            'current_quantity' => 5,
        ]);

        Product::create([
            'company_id' => $user->company_id,
            'name' => 'Mouse',
            'sku' => 'MOU-001',
            'barcode' => '789002',
            'current_quantity' => 3,
        ]);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/products?per_page=1')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('meta.total', 2)
            ->assertJsonPath('meta.per_page', 1);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/products/search?q=note')
            ->assertOk()
            ->assertJsonPath('data.0.name', 'Notebook')
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('meta.total', 1);
    }

    public function test_user_can_list_inventory_counts_and_items_with_pagination(): void
    {
        [$user, $count, $item] = $this->createInventoryCount();

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/inventory-counts')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.0.title', 'Contagem semanal')
            ->assertJsonPath('meta.total', 1);

        $this->actingAs($user, 'sanctum')
            ->getJson("/api/inventory-counts/{$count->id}/items")
            ->assertOk()
            ->assertJsonPath('data.0.id', $item->id)
            ->assertJsonPath('data.0.product.name', 'Notebook')
            ->assertJsonPath('meta.total', 1);
    }

    public function test_user_can_filter_inventory_counts_and_items(): void
    {
        [$user, $count, $item] = $this->createInventoryCount();

        InventoryCount::create([
            'company_id' => $user->company_id,
            'created_by' => $user->id,
            'title' => 'Contagem finalizada',
            'status' => 'finished',
            'started_at' => now(),
            'finished_at' => now(),
        ]);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/inventory-counts?status=finished')
            ->assertOk()
            ->assertJsonPath('data.0.title', 'Contagem finalizada')
            ->assertJsonPath('meta.total', 1);

        $this->actingAs($user, 'sanctum')
            ->getJson("/api/inventory-counts/{$count->id}/items?sync_status=pending")
            ->assertOk()
            ->assertJsonPath('data.0.id', $item->id)
            ->assertJsonPath('meta.total', 1);
    }

    public function test_user_can_get_mobile_summary(): void
    {
        [$user] = $this->createInventoryCount();

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/mobile/summary')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.open_counts', 1)
            ->assertJsonPath('data.pending_items', 1)
            ->assertJsonPath('data.synced_items', 0);
    }

    public function test_user_can_sync_inventory_count_items(): void
    {
        [$user, $count, $item] = $this->createInventoryCount();

        $this->actingAs($user, 'sanctum')
            ->postJson("/api/inventory-counts/{$count->id}/sync", [
                'items' => [
                    [
                        'id' => $item->id,
                        'counted_quantity' => 7,
                    ],
                ],
            ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'in_progress');

        $this->assertDatabaseHas('inventory_count_items', [
            'id' => $item->id,
            'counted_by' => $user->id,
            'counted_quantity' => 7,
            'difference' => -3,
            'sync_status' => 'synced',
        ]);
    }

    public function test_user_cannot_access_other_company_api_data(): void
    {
        $user = $this->createUser();
        [$otherUser, $count] = $this->createInventoryCount('Outra empresa', 'outro@counter.test');

        $this->actingAs($user, 'sanctum')
            ->getJson("/api/inventory-counts/{$count->id}")
            ->assertNotFound()
            ->assertJsonPath('message', 'Recurso não encontrado');

        $this->assertNotSame($user->company_id, $otherUser->company_id);
    }

    private function createInventoryCount(string $companyName = 'Counter Demo', string $email = 'admin@counter.test'): array
    {
        $user = $this->createUser($companyName, $email);

        $product = Product::create([
            'company_id' => $user->company_id,
            'name' => 'Notebook',
            'sku' => 'NOTE-'.str_replace('@counter.test', '', $email),
            'current_quantity' => 10,
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
            'system_quantity' => 10,
            'difference' => 0,
            'sync_status' => 'pending',
        ]);

        return [$user, $count, $item];
    }

    private function createUser(string $companyName = 'Counter Demo', string $email = 'admin@counter.test', string $role = 'admin'): User
    {
        $company = Company::create([
            'name' => $companyName,
        ]);

        return User::create([
            'company_id' => $company->id,
            'name' => 'Administrador',
            'email' => $email,
            'password' => 'password',
            'role' => $role,
        ]);
    }
}
