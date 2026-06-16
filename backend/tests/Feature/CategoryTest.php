<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Company;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_category(): void
    {
        $user = $this->createUser();

        $this->actingAs($user)
            ->post('/categories', [
                'name' => 'Eletrônicos',
                'description' => 'Produtos eletrônicos',
            ])
            ->assertRedirect('/categories');

        $this->assertDatabaseHas('categories', [
            'company_id' => $user->company_id,
            'name' => 'Eletrônicos',
        ]);
    }

    public function test_user_can_update_category(): void
    {
        $user = $this->createUser();
        $category = Category::create([
            'company_id' => $user->company_id,
            'name' => 'Antiga',
        ]);

        $this->actingAs($user)
            ->put("/categories/{$category->id}", [
                'name' => 'Nova',
                'description' => 'Descrição atualizada',
            ])
            ->assertRedirect('/categories');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Nova',
            'description' => 'Descrição atualizada',
        ]);
    }

    public function test_user_can_delete_category_without_products(): void
    {
        $user = $this->createUser();
        $category = Category::create([
            'company_id' => $user->company_id,
            'name' => 'Temporária',
        ]);

        $this->actingAs($user)
            ->delete("/categories/{$category->id}")
            ->assertRedirect('/categories');

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }

    public function test_user_cannot_delete_category_with_products(): void
    {
        $user = $this->createUser();
        $category = Category::create([
            'company_id' => $user->company_id,
            'name' => 'Com produtos',
        ]);

        Product::create([
            'company_id' => $user->company_id,
            'category_id' => $category->id,
            'name' => 'Notebook',
            'sku' => 'NOTE-001',
        ]);

        $this->actingAs($user)
            ->delete("/categories/{$category->id}")
            ->assertRedirect('/categories');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
        ]);
    }

    public function test_user_cannot_access_category_from_another_company(): void
    {
        $user = $this->createUser();
        $otherUser = $this->createUser('Outra empresa', 'outro@counter.test');
        $category = Category::create([
            'company_id' => $otherUser->company_id,
            'name' => 'Restrita',
        ]);

        $this->actingAs($user)
            ->get("/categories/{$category->id}/edit")
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
