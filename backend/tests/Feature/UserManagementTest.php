<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_user(): void
    {
        $admin = $this->createUser('admin', 'admin@counter.test');

        $this->actingAs($admin)
            ->post('/users', [
                'name' => 'Novo contador',
                'email' => 'novo.contador@counter.test',
                'role' => 'counter',
                'password' => 'password',
            ])
            ->assertRedirect('/users');

        $this->assertDatabaseHas('users', [
            'company_id' => $admin->company_id,
            'name' => 'Novo contador',
            'email' => 'novo.contador@counter.test',
            'role' => 'counter',
        ]);
    }

    public function test_admin_can_update_user(): void
    {
        $admin = $this->createUser('admin', 'admin@counter.test');
        $user = $this->createUser('counter', 'contador@counter.test', $admin->company_id);

        $this->actingAs($admin)
            ->put("/users/{$user->id}", [
                'name' => 'Estoquista atualizado',
                'email' => 'estoquista.atualizado@counter.test',
                'role' => 'stockist',
                'password' => null,
            ])
            ->assertRedirect('/users');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Estoquista atualizado',
            'email' => 'estoquista.atualizado@counter.test',
            'role' => 'stockist',
        ]);
    }

    public function test_admin_can_delete_other_user(): void
    {
        $admin = $this->createUser('admin', 'admin@counter.test');
        $user = $this->createUser('counter', 'contador@counter.test', $admin->company_id);

        $this->actingAs($admin)
            ->delete("/users/{$user->id}")
            ->assertRedirect('/users');

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    public function test_admin_cannot_delete_authenticated_user(): void
    {
        $admin = $this->createUser('admin', 'admin@counter.test');

        $this->actingAs($admin)
            ->delete("/users/{$admin->id}")
            ->assertRedirect('/users');

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
        ]);
    }

    public function test_admin_cannot_change_own_role(): void
    {
        $admin = $this->createUser('admin', 'admin@counter.test');

        $this->actingAs($admin)
            ->put("/users/{$admin->id}", [
                'name' => 'Administrador',
                'email' => 'admin@counter.test',
                'role' => 'stockist',
                'password' => null,
            ])
            ->assertRedirect('/users');

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'role' => 'admin',
        ]);
    }

    public function test_user_management_is_restricted_to_admin(): void
    {
        $stockist = $this->createUser('stockist', 'estoquista@counter.test');

        $this->actingAs($stockist)
            ->get('/users')
            ->assertForbidden();
    }

    public function test_admin_cannot_access_user_from_another_company(): void
    {
        $admin = $this->createUser('admin', 'admin@counter.test');
        $otherUser = $this->createUser('counter', 'contador@outra.test');

        $this->actingAs($admin)
            ->get("/users/{$otherUser->id}/edit")
            ->assertNotFound();
    }

    private function createUser(string $role, string $email, ?int $companyId = null): User
    {
        $company = $companyId ? Company::findOrFail($companyId) : Company::create([
            'name' => 'Empresa '.$email,
        ]);

        return User::create([
            'company_id' => $company->id,
            'name' => 'Usuário '.$role,
            'email' => $email,
            'password' => 'password',
            'role' => $role,
        ]);
    }
}
