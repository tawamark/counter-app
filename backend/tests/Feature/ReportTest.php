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

class ReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_reports_dashboard(): void
    {
        [$admin] = $this->createScenario('admin');

        $this->actingAs($admin)
            ->get('/reports')
            ->assertOk()
            ->assertSee('Relatórios')
            ->assertSee('Exportar estoque')
            ->assertSee('Exportar divergências');
    }

    public function test_stock_report_can_be_exported(): void
    {
        [$admin] = $this->createScenario('admin');

        $response = $this->actingAs($admin)
            ->get('/reports/stock.csv');

        $response->assertOk();
        $response->assertDownload('relatorio-estoque.csv');

        $content = $response->streamedContent();

        $this->assertStringContainsString('Produto;SKU;"Código de barras";Categoria;Fornecedor;Unidade;"Quantidade atual";"Preço de custo";"Preço de venda"', $content);
        $this->assertStringContainsString('Notebook;NOTE-admin', $content);
    }

    public function test_movements_report_can_be_exported_with_filters(): void
    {
        [$admin, $product] = $this->createScenario('admin');

        $response = $this->actingAs($admin)
            ->get("/reports/movements.csv?product_id={$product->id}&type=entry");

        $response->assertOk();
        $response->assertDownload('relatorio-movimentacoes.csv');

        $content = $response->streamedContent();

        $this->assertStringContainsString('Data;Produto;Tipo;Quantidade;"Saldo anterior";"Saldo posterior";Usuário;"Contagem vinculada";Motivo', $content);
        $this->assertStringContainsString('Notebook;Entrada', $content);
        $this->assertStringNotContainsString('Saída', $content);
    }

    public function test_admin_can_export_divergences_report(): void
    {
        [$admin] = $this->createScenario('admin');

        $response = $this->actingAs($admin)
            ->get('/reports/divergences.csv?type=shortage');

        $response->assertOk();
        $response->assertDownload('relatorio-divergencias.csv');

        $content = $response->streamedContent();

        $this->assertStringContainsString('Contagem;Produto;SKU;"Saldo do sistema";"Quantidade contada";Diferença;Tipo', $content);
        $this->assertStringContainsString('"Falta física"', $content);
    }

    public function test_stockist_can_export_stock_and_movements_but_not_divergences(): void
    {
        [$stockist] = $this->createScenario('stockist');

        $this->actingAs($stockist)
            ->get('/reports')
            ->assertOk()
            ->assertSee('Exportar estoque')
            ->assertDontSee('Exportar divergências');

        $this->actingAs($stockist)
            ->get('/reports/stock.csv')
            ->assertOk();

        $this->actingAs($stockist)
            ->get('/reports/movements.csv')
            ->assertOk();

        $this->actingAs($stockist)
            ->get('/reports/divergences.csv')
            ->assertForbidden();
    }

    public function test_counter_cannot_access_reports(): void
    {
        [$counter] = $this->createScenario('counter');

        $this->actingAs($counter)
            ->get('/reports')
            ->assertForbidden();

        $this->actingAs($counter)
            ->get('/reports/stock.csv')
            ->assertForbidden();
    }

    private function createScenario(string $role): array
    {
        $company = Company::create([
            'name' => 'Counter Demo '.$role,
        ]);

        $user = User::create([
            'company_id' => $company->id,
            'name' => 'Usuário '.$role,
            'email' => $role.'@counter.test',
            'password' => 'password',
            'role' => $role,
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
            'sku' => 'NOTE-'.$role,
            'barcode' => '789000000001',
            'unit' => 'un',
            'cost_price' => 2500,
            'sale_price' => 3200,
            'current_quantity' => 8,
        ]);

        StockMovement::create([
            'company_id' => $company->id,
            'product_id' => $product->id,
            'user_id' => $user->id,
            'type' => 'entry',
            'quantity' => 10,
            'quantity_before' => 0,
            'quantity_after' => 10,
            'reason' => 'Carga inicial',
        ]);

        StockMovement::create([
            'company_id' => $company->id,
            'product_id' => $product->id,
            'user_id' => $user->id,
            'type' => 'exit',
            'quantity' => 2,
            'quantity_before' => 10,
            'quantity_after' => 8,
            'reason' => 'Venda',
        ]);

        $count = InventoryCount::create([
            'company_id' => $company->id,
            'created_by' => $user->id,
            'title' => 'Contagem semanal',
            'status' => 'finished',
            'started_at' => now(),
            'finished_at' => now(),
        ]);

        $count->items()->create([
            'product_id' => $product->id,
            'counted_by' => $user->id,
            'system_quantity' => 8,
            'counted_quantity' => 6,
            'difference' => -2,
            'sync_status' => 'synced',
            'counted_at' => now(),
        ]);

        return [$user, $product];
    }
}
