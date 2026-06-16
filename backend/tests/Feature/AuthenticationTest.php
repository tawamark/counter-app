<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_dashboard_to_login(): void
    {
        $this->get('/dashboard')
            ->assertRedirect('/login');
    }

    public function test_user_can_sign_in(): void
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

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);
    }

    public function test_user_can_sign_out(): void
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

        $this->actingAs($user)
            ->post('/logout')
            ->assertRedirect('/login');

        $this->assertGuest();
    }

    public function test_login_error_uses_general_portuguese_message(): void
    {
        Company::create([
            'name' => 'Counter Demo',
        ]);

        $this->from('/login')
            ->post('/login', [
                'email' => 'admin@counter.test',
                'password' => 'senha-incorreta',
            ])
            ->assertRedirect('/login')
            ->assertSessionHasErrors([
                'login' => 'As credenciais informadas não conferem.',
            ]);

        $this->assertGuest();
    }

    public function test_login_validation_messages_are_in_portuguese(): void
    {
        $this->from('/login')
            ->post('/login', [])
            ->assertRedirect('/login')
            ->assertSessionHasErrors([
                'email' => 'Informe o e-mail.',
                'password' => 'Informe a senha.',
            ]);
    }
}
