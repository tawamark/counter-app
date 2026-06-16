<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Company;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConfirmModalTest extends TestCase
{
    use RefreshDatabase;

    public function test_delete_actions_use_global_confirm_modal(): void
    {
        [$user, $category, $product, $supplier, $managedUser] = $this->createScenario();

        $this->actingAs($user)
            ->get('/categories')
            ->assertOk()
            ->assertSee('open-confirm-modal')
            ->assertSee('Excluir categoria')
            ->assertDontSee('return confirm(');

        $this->actingAs($user)
            ->get('/products')
            ->assertOk()
            ->assertSee('Excluir produto')
            ->assertDontSee('return confirm(');

        $this->actingAs($user)
            ->get('/suppliers')
            ->assertOk()
            ->assertSee('Excluir fornecedor')
            ->assertDontSee('return confirm(');

        $this->actingAs($user)
            ->get('/users')
            ->assertOk()
            ->assertSee('Excluir usuário')
            ->assertSee($managedUser->email)
            ->assertDontSee('return confirm(');
    }

    public function test_inventory_count_critical_actions_use_global_confirm_modal(): void
    {
        [$user, $category, $product] = $this->createScenario();

        $count = $user->createdInventoryCounts()->create([
            'company_id' => $user->company_id,
            'title' => 'Contagem semanal',
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        $count->items()->create([
            'product_id' => $product->id,
            'counted_by' => $user->id,
            'system_quantity' => 10,
            'counted_quantity' => 10,
            'difference' => 0,
            'sync_status' => 'synced',
            'counted_at' => now(),
        ]);

        $this->actingAs($user)
            ->get("/inventory-counts/{$count->id}")
            ->assertOk()
            ->assertSee('Finalizar contagem')
            ->assertSee('open-confirm-modal')
            ->assertDontSee('return confirm(');

        $count->update([
            'status' => 'finished',
            'finished_at' => now(),
        ]);

        $this->actingAs($user)
            ->get("/inventory-counts/{$count->id}")
            ->assertOk()
            ->assertSee('Aprovar ajustes')
            ->assertSee('open-confirm-modal')
            ->assertDontSee('return confirm(');
    }

    private function createScenario(): array
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

        $managedUser = User::create([
            'company_id' => $company->id,
            'name' => 'Estoquista',
            'email' => 'estoquista@counter.test',
            'password' => 'password',
            'role' => 'stockist',
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
            'current_quantity' => 10,
        ]);

        return [$user, $category, $product, $supplier, $managedUser];
    }
}
