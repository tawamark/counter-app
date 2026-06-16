<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Company;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_audit_logs(): void
    {
        $admin = $this->createUser('admin');

        AuditLog::create([
            'company_id' => $admin->company_id,
            'user_id' => $admin->id,
            'module' => 'produtos',
            'action' => 'criou',
            'description' => 'Produto cadastrado: Notebook',
        ]);

        $this->actingAs($admin)
            ->get('/audit-logs')
            ->assertOk()
            ->assertSee('Auditoria')
            ->assertSee('Produto cadastrado: Notebook');
    }

    public function test_audit_logs_can_be_filtered(): void
    {
        $admin = $this->createUser('admin');

        AuditLog::create([
            'company_id' => $admin->company_id,
            'user_id' => $admin->id,
            'module' => 'produtos',
            'action' => 'criou',
            'description' => 'Produto cadastrado: Notebook',
        ]);

        AuditLog::create([
            'company_id' => $admin->company_id,
            'user_id' => $admin->id,
            'module' => 'contagens',
            'action' => 'aprovou',
            'description' => 'Contagem aprovada: Contagem semanal',
        ]);

        $this->actingAs($admin)
            ->get('/audit-logs?module=produtos')
            ->assertOk()
            ->assertSee('Produto cadastrado: Notebook')
            ->assertDontSee('Contagem aprovada: Contagem semanal');
    }

    public function test_product_creation_registers_audit_log(): void
    {
        $admin = $this->createUser('admin');

        $this->actingAs($admin)
            ->post('/products', [
                'name' => 'Notebook',
                'sku' => 'NOTE-001',
                'unit' => 'un',
                'cost_price' => 2500,
                'sale_price' => 3200,
                'current_quantity' => 10,
            ])
            ->assertRedirect('/products');

        $product = Product::first();

        $this->assertDatabaseHas('audit_logs', [
            'company_id' => $admin->company_id,
            'user_id' => $admin->id,
            'module' => 'produtos',
            'action' => 'criou',
            'auditable_type' => Product::class,
            'auditable_id' => $product->id,
            'description' => 'Produto cadastrado: Notebook',
        ]);
    }

    public function test_stock_movement_registers_audit_log(): void
    {
        $admin = $this->createUser('admin');

        $product = Product::create([
            'company_id' => $admin->company_id,
            'name' => 'Notebook',
            'sku' => 'NOTE-001',
            'current_quantity' => 10,
        ]);

        $this->actingAs($admin)
            ->post('/stock-movements', [
                'product_id' => $product->id,
                'type' => 'entry',
                'quantity' => 5,
                'reason' => 'Compra',
            ])
            ->assertRedirect('/stock-movements');

        $this->assertDatabaseHas('audit_logs', [
            'company_id' => $admin->company_id,
            'user_id' => $admin->id,
            'module' => 'movimentacoes',
            'action' => 'registrou',
            'description' => 'Movimentação registrada para Notebook',
        ]);
    }

    public function test_non_admin_cannot_access_audit_logs(): void
    {
        $stockist = $this->createUser('stockist');

        $this->actingAs($stockist)
            ->get('/audit-logs')
            ->assertForbidden();
    }

    private function createUser(string $role): User
    {
        $company = Company::create([
            'name' => 'Counter Demo '.$role,
        ]);

        return User::create([
            'company_id' => $company->id,
            'name' => 'Usuário '.$role,
            'email' => $role.'@counter.test',
            'password' => 'password',
            'role' => $role,
        ]);
    }
}
