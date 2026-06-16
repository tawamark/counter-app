<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Company;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_product(): void
    {
        $user = $this->createUser();
        $category = Category::create([
            'company_id' => $user->company_id,
            'name' => 'Eletrônicos',
        ]);
        $supplier = Supplier::create([
            'company_id' => $user->company_id,
            'name' => 'Fornecedor Demo',
        ]);

        $this->actingAs($user)
            ->post('/products', [
                'category_id' => $category->id,
                'supplier_id' => $supplier->id,
                'name' => 'Notebook',
                'description' => 'Notebook para escritório',
                'sku' => 'NOTE-001',
                'barcode' => '789000000001',
                'unit' => 'un',
                'cost_price' => 2500,
                'sale_price' => 3200,
                'current_quantity' => 10,
            ])
            ->assertRedirect('/products');

        $this->assertDatabaseHas('products', [
            'company_id' => $user->company_id,
            'category_id' => $category->id,
            'supplier_id' => $supplier->id,
            'name' => 'Notebook',
            'sku' => 'NOTE-001',
        ]);
    }

    public function test_user_can_update_product(): void
    {
        $user = $this->createUser();
        $product = Product::create([
            'company_id' => $user->company_id,
            'name' => 'Produto antigo',
            'sku' => 'OLD-001',
        ]);

        $this->actingAs($user)
            ->put("/products/{$product->id}", [
                'category_id' => null,
                'supplier_id' => null,
                'name' => 'Produto novo',
                'description' => 'Descrição nova',
                'sku' => 'NEW-001',
                'barcode' => '789000000002',
                'unit' => 'cx',
                'cost_price' => 15.5,
                'sale_price' => 20.9,
                'current_quantity' => 3,
            ])
            ->assertRedirect('/products');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Produto novo',
            'sku' => 'NEW-001',
            'unit' => 'cx',
        ]);
    }

    public function test_user_can_delete_product_without_links(): void
    {
        $user = $this->createUser();
        $product = Product::create([
            'company_id' => $user->company_id,
            'name' => 'Produto temporário',
            'sku' => 'TMP-001',
        ]);

        $this->actingAs($user)
            ->delete("/products/{$product->id}")
            ->assertRedirect('/products');

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }

    public function test_user_cannot_delete_product_with_stock_movements(): void
    {
        $user = $this->createUser();
        $product = Product::create([
            'company_id' => $user->company_id,
            'name' => 'Notebook',
            'sku' => 'NOTE-001',
        ]);

        StockMovement::create([
            'company_id' => $user->company_id,
            'product_id' => $product->id,
            'user_id' => $user->id,
            'type' => 'entry',
            'quantity' => 1,
            'quantity_before' => 0,
            'quantity_after' => 1,
        ]);

        $this->actingAs($user)
            ->delete("/products/{$product->id}")
            ->assertRedirect('/products');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
        ]);
    }

    public function test_user_cannot_access_product_from_another_company(): void
    {
        $user = $this->createUser();
        $otherUser = $this->createUser('Outra empresa', 'outro@counter.test');
        $product = Product::create([
            'company_id' => $otherUser->company_id,
            'name' => 'Produto restrito',
            'sku' => 'RES-001',
        ]);

        $this->actingAs($user)
            ->get("/products/{$product->id}/edit")
            ->assertNotFound();
    }

    public function test_user_cannot_repeat_barcode_in_same_company(): void
    {
        $user = $this->createUser();

        Product::create([
            'company_id' => $user->company_id,
            'name' => 'Notebook',
            'sku' => 'NOTE-001',
            'barcode' => '789000000001',
        ]);

        $this->actingAs($user)
            ->post('/products', [
                'category_id' => null,
                'supplier_id' => null,
                'name' => 'Mouse',
                'description' => null,
                'sku' => 'MOU-001',
                'barcode' => '789000000001',
                'unit' => 'un',
                'cost_price' => 45,
                'sale_price' => 79.9,
                'current_quantity' => 10,
            ])
            ->assertSessionHasErrors('barcode');
    }

    private function createUser(string $companyName = 'Counter Demo', string $email = 'admin@counter.test'): User
    {
        $company = Company::create([
            'name' => $companyName,
        ]);

        return User::create([
            'company_id' => $company->id,
            'name' => 'Administrador',
            'email' => $email,
            'password' => 'password',
            'role' => 'admin',
        ]);
    }
}
