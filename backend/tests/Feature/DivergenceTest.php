<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\InventoryCount;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DivergenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_divergences(): void
    {
        [$user] = $this->createCountWithItems();

        $this->actingAs($user)
            ->get('/divergences')
            ->assertOk()
            ->assertSee('Falta física')
            ->assertSee('Sobra física')
            ->assertSee('Sem divergência')
            ->assertSee('Notebook')
            ->assertSee('Mouse')
            ->assertSee('Teclado');
    }

    public function test_user_can_filter_shortages(): void
    {
        [$user] = $this->createCountWithItems();

        $this->actingAs($user)
            ->get('/divergences?type=shortage')
            ->assertOk()
            ->assertSee('Notebook')
            ->assertDontSee('<div class="font-medium">Mouse</div>', false)
            ->assertDontSee('<div class="font-medium">Teclado</div>', false);
    }

    public function test_user_only_sees_company_divergences(): void
    {
        [$user] = $this->createCountWithItems();
        $this->createCountWithItems('Outra empresa', 'outro@counter.test', 'Produto restrito');

        $this->actingAs($user)
            ->get('/divergences')
            ->assertOk()
            ->assertSee('Notebook')
            ->assertDontSee('Produto restrito');
    }

    private function createCountWithItems(string $companyName = 'Counter Demo', string $email = 'admin@counter.test', string $firstProductName = 'Notebook'): array
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

        $count = InventoryCount::create([
            'company_id' => $company->id,
            'created_by' => $user->id,
            'title' => 'Contagem semanal',
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        $products = [
            [$firstProductName, 'NOTE', 10, 7, -3],
            ['Mouse', 'MOU', 5, 8, 3],
            ['Teclado', 'TEC', 4, 4, 0],
        ];

        foreach ($products as [$name, $sku, $systemQuantity, $countedQuantity, $difference]) {
            $product = Product::create([
                'company_id' => $company->id,
                'name' => $name,
                'sku' => $sku.'-'.str_replace('@counter.test', '', $email),
                'current_quantity' => $systemQuantity,
            ]);

            $count->items()->create([
                'product_id' => $product->id,
                'counted_by' => $user->id,
                'system_quantity' => $systemQuantity,
                'counted_quantity' => $countedQuantity,
                'difference' => $difference,
                'sync_status' => 'synced',
                'counted_at' => now(),
            ]);
        }

        return [$user, $count];
    }
}
