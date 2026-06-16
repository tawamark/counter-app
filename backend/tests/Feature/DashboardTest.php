<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Company;
use App\Models\InventoryCount;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_company_totals_recent_counts_divergences_and_movements(): void
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
            'current_quantity' => 5,
        ]);

        Product::create([
            'company_id' => $company->id,
            'name' => 'Mouse',
            'sku' => 'MOU-001',
            'current_quantity' => 12,
        ]);

        $count = InventoryCount::create([
            'company_id' => $company->id,
            'created_by' => $user->id,
            'title' => 'Contagem inicial',
            'status' => 'open',
        ]);

        $count->items()->create([
            'product_id' => $product->id,
            'counted_by' => $user->id,
            'system_quantity' => 5,
            'counted_quantity' => 3,
            'difference' => -2,
            'sync_status' => 'synced',
            'counted_at' => now(),
        ]);

        StockMovement::create([
            'company_id' => $company->id,
            'product_id' => $product->id,
            'user_id' => $user->id,
            'type' => 'entry',
            'quantity' => 5,
            'quantity_before' => 0,
            'quantity_after' => 5,
            'reason' => 'Entrada inicial',
        ]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Produtos')
            ->assertSee('2')
            ->assertSee('Categorias')
            ->assertSee('Fornecedores')
            ->assertSee('Contagens abertas')
            ->assertSee('Divergências')
            ->assertSee('Movimentações por tipo')
            ->assertSee('Status das contagens')
            ->assertSee('Últimos 7 dias')
            ->assertSee('Estoque por categoria')
            ->assertSee('Estoque baixo')
            ->assertSee('Contagens recentes')
            ->assertSee('Contagem inicial')
            ->assertSee('Movimentações recentes')
            ->assertSee('Notebook')
            ->assertSee('Entrada')
            ->assertSee('Administrador');
    }
}
