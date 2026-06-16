<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_supplier(): void
    {
        $user = $this->createUser();

        $this->actingAs($user)
            ->post('/suppliers', [
                'name' => 'Fornecedor Demo',
                'cnpj' => '12.345.678/0001-90',
                'phone' => '(49) 99999-0000',
                'email' => 'fornecedor@counter.test',
                'address' => 'Rua Central, 100',
            ])
            ->assertRedirect('/suppliers');

        $this->assertDatabaseHas('suppliers', [
            'company_id' => $user->company_id,
            'name' => 'Fornecedor Demo',
            'email' => 'fornecedor@counter.test',
        ]);
    }

    public function test_user_can_update_supplier(): void
    {
        $user = $this->createUser();
        $supplier = Supplier::create([
            'company_id' => $user->company_id,
            'name' => 'Fornecedor Antigo',
        ]);

        $this->actingAs($user)
            ->put("/suppliers/{$supplier->id}", [
                'name' => 'Fornecedor Novo',
                'cnpj' => '98.765.432/0001-10',
                'phone' => '(49) 98888-0000',
                'email' => 'novo@counter.test',
                'address' => 'Avenida Brasil, 500',
            ])
            ->assertRedirect('/suppliers');

        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'name' => 'Fornecedor Novo',
            'email' => 'novo@counter.test',
        ]);
    }

    public function test_user_can_delete_supplier_without_products(): void
    {
        $user = $this->createUser();
        $supplier = Supplier::create([
            'company_id' => $user->company_id,
            'name' => 'Fornecedor Temporário',
        ]);

        $this->actingAs($user)
            ->delete("/suppliers/{$supplier->id}")
            ->assertRedirect('/suppliers');

        $this->assertDatabaseMissing('suppliers', [
            'id' => $supplier->id,
        ]);
    }

    public function test_user_cannot_delete_supplier_with_products(): void
    {
        $user = $this->createUser();
        $supplier = Supplier::create([
            'company_id' => $user->company_id,
            'name' => 'Fornecedor com produtos',
        ]);

        Product::create([
            'company_id' => $user->company_id,
            'supplier_id' => $supplier->id,
            'name' => 'Notebook',
            'sku' => 'NOTE-001',
        ]);

        $this->actingAs($user)
            ->delete("/suppliers/{$supplier->id}")
            ->assertRedirect('/suppliers');

        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
        ]);
    }

    public function test_user_cannot_access_supplier_from_another_company(): void
    {
        $user = $this->createUser();
        $otherUser = $this->createUser('Outra empresa', 'outro@counter.test');
        $supplier = Supplier::create([
            'company_id' => $otherUser->company_id,
            'name' => 'Fornecedor Restrito',
        ]);

        $this->actingAs($user)
            ->get("/suppliers/{$supplier->id}/edit")
            ->assertNotFound();
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
