<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockMovementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_entry(): void
    {
        [$user, $product] = $this->createUserAndProduct(5);

        $this->actingAs($user)
            ->post('/stock-movements', [
                'product_id' => $product->id,
                'type' => 'entry',
                'quantity' => 3,
                'reason' => 'Compra',
            ])
            ->assertRedirect('/stock-movements');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'current_quantity' => 8,
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'entry',
            'quantity_before' => 5,
            'quantity_after' => 8,
        ]);
    }

    public function test_user_can_register_exit(): void
    {
        [$user, $product] = $this->createUserAndProduct(5);

        $this->actingAs($user)
            ->post('/stock-movements', [
                'product_id' => $product->id,
                'type' => 'exit',
                'quantity' => 2,
                'reason' => 'Venda',
            ])
            ->assertRedirect('/stock-movements');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'current_quantity' => 3,
        ]);
    }

    public function test_exit_cannot_be_greater_than_current_quantity(): void
    {
        [$user, $product] = $this->createUserAndProduct(5);

        $this->actingAs($user)
            ->post('/stock-movements', [
                'product_id' => $product->id,
                'type' => 'exit',
                'quantity' => 6,
                'reason' => 'Venda',
            ])
            ->assertSessionHasErrors('quantity');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'current_quantity' => 5,
        ]);
    }

    public function test_user_can_register_adjustment(): void
    {
        [$user, $product] = $this->createUserAndProduct(5);

        $this->actingAs($user)
            ->post('/stock-movements', [
                'product_id' => $product->id,
                'type' => 'adjustment',
                'quantity' => 12,
                'reason' => 'Correção',
            ])
            ->assertRedirect('/stock-movements');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'current_quantity' => 12,
        ]);
    }

    public function test_user_cannot_move_product_from_another_company(): void
    {
        [$user] = $this->createUserAndProduct(5);
        [, $otherProduct] = $this->createUserAndProduct(4, 'Outra empresa', 'outro@counter.test');

        $this->actingAs($user)
            ->post('/stock-movements', [
                'product_id' => $otherProduct->id,
                'type' => 'entry',
                'quantity' => 1,
            ])
            ->assertSessionHasErrors('product_id');
    }

    public function test_user_can_filter_stock_movement_history(): void
    {
        [$user, $product] = $this->createUserAndProduct(5);
        $otherProduct = Product::create([
            'company_id' => $user->company_id,
            'name' => 'Mouse',
            'sku' => 'MOU-001',
            'current_quantity' => 2,
        ]);

        StockMovement::create([
            'company_id' => $user->company_id,
            'product_id' => $product->id,
            'user_id' => $user->id,
            'type' => 'entry',
            'quantity' => 1,
            'quantity_before' => 5,
            'quantity_after' => 6,
        ]);

        StockMovement::create([
            'company_id' => $user->company_id,
            'product_id' => $otherProduct->id,
            'user_id' => $user->id,
            'type' => 'exit',
            'quantity' => 1,
            'quantity_before' => 2,
            'quantity_after' => 1,
        ]);

        $this->actingAs($user)
            ->get("/stock-movements?product_id={$product->id}&type=entry&user_id={$user->id}")
            ->assertOk()
            ->assertSee('Notebook')
            ->assertDontSee('<td class="px-4 py-3 font-medium">Mouse</td>', false);
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
