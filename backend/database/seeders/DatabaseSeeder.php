<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Company;
use App\Models\InventoryCount;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $company = Company::updateOrCreate([
            'name' => 'Counter Demo',
        ], [
            'document' => '00.000.000/0001-00',
            'phone' => '(11) 4002-8922',
            'email' => 'contato@counter.test',
            'address' => 'Rua Demo, 100',
        ]);

        $admin = $this->user($company, 'Administrador', 'admin@counter.test', 'admin');
        $stockist = $this->user($company, 'Estoquista', 'estoquista@counter.test', 'stockist');
        $counter = $this->user($company, 'Contador', 'contador@counter.test', 'counter');

        $categories = $this->categories($company);
        $suppliers = $this->suppliers($company);
        $products = $this->products($company, $categories, $suppliers);

        $this->movements($company, $stockist, $products);
        $this->inventoryCounts($company, $admin, $counter, $products);
    }

    private function user(Company $company, string $name, string $email, string $role): User
    {
        return User::updateOrCreate([
            'email' => $email,
        ], [
            'company_id' => $company->id,
            'name' => $name,
            'password' => 'password',
            'role' => $role,
        ]);
    }

    private function categories(Company $company): array
    {
        $items = [
            ['key' => 'electronics', 'name' => 'Eletrônicos', 'description' => 'Produtos eletrônicos e periféricos'],
            ['key' => 'office', 'name' => 'Escritório', 'description' => 'Materiais usados em ambiente administrativo'],
            ['key' => 'cleaning', 'name' => 'Limpeza', 'description' => 'Produtos de higiene e limpeza'],
            ['key' => 'tools', 'name' => 'Ferramentas', 'description' => 'Ferramentas manuais e acessórios'],
            ['key' => 'packaging', 'name' => 'Embalagens', 'description' => 'Caixas, fitas e materiais de envio'],
            ['key' => 'food', 'name' => 'Alimentos', 'description' => 'Itens de consumo interno'],
        ];

        $categories = [];

        foreach ($items as $item) {
            $categories[$item['key']] = Category::updateOrCreate([
                'company_id' => $company->id,
                'name' => $item['name'],
            ], [
                'description' => $item['description'],
            ]);
        }

        return $categories;
    }

    private function suppliers(Company $company): array
    {
        $items = [
            ['key' => 'tech', 'name' => 'Tech Prime Distribuidora', 'cnpj' => '12.345.678/0001-90', 'phone' => '(11) 3000-0000', 'email' => 'vendas@techprime.test', 'address' => 'Avenida Estoque, 500'],
            ['key' => 'office', 'name' => 'Office Mais Atacado', 'cnpj' => '23.456.789/0001-10', 'phone' => '(11) 3000-0001', 'email' => 'pedidos@officemais.test', 'address' => 'Rua Papel, 220'],
            ['key' => 'cleaning', 'name' => 'Limpa Tudo Comercial', 'cnpj' => '34.567.890/0001-20', 'phone' => '(11) 3000-0002', 'email' => 'contato@limpatudo.test', 'address' => 'Rua Higiene, 80'],
            ['key' => 'tools', 'name' => 'Ferramentas Norte', 'cnpj' => '45.678.901/0001-30', 'phone' => '(11) 3000-0003', 'email' => 'vendas@ferramentasnorte.test', 'address' => 'Avenida Industrial, 700'],
            ['key' => 'packaging', 'name' => 'Pack Box Soluções', 'cnpj' => '56.789.012/0001-40', 'phone' => '(11) 3000-0004', 'email' => 'comercial@packbox.test', 'address' => 'Rua das Caixas, 45'],
        ];

        $suppliers = [];

        foreach ($items as $item) {
            $suppliers[$item['key']] = Supplier::updateOrCreate([
                'company_id' => $company->id,
                'cnpj' => $item['cnpj'],
            ], [
                'name' => $item['name'],
                'cnpj' => $item['cnpj'],
                'phone' => $item['phone'],
                'email' => $item['email'],
                'address' => $item['address'],
            ]);
        }

        return $suppliers;
    }

    private function products(Company $company, array $categories, array $suppliers): array
    {
        $items = [
            ['key' => 'notebook', 'category' => 'electronics', 'supplier' => 'tech', 'name' => 'Notebook', 'description' => 'Notebook para equipe administrativa', 'sku' => 'NOTE-001', 'barcode' => '789000000001', 'unit' => 'un', 'cost_price' => 2500, 'sale_price' => 3200, 'current_quantity' => 7],
            ['key' => 'mouse', 'category' => 'electronics', 'supplier' => 'tech', 'name' => 'Mouse sem fio', 'description' => 'Mouse óptico sem fio', 'sku' => 'MOU-001', 'barcode' => '789000000002', 'unit' => 'un', 'cost_price' => 45, 'sale_price' => 79.9, 'current_quantity' => 18],
            ['key' => 'keyboard', 'category' => 'electronics', 'supplier' => 'tech', 'name' => 'Teclado mecânico', 'description' => 'Teclado USB com iluminação', 'sku' => 'TEC-001', 'barcode' => '789000000003', 'unit' => 'un', 'cost_price' => 180, 'sale_price' => 249.9, 'current_quantity' => 12],
            ['key' => 'monitor', 'category' => 'electronics', 'supplier' => 'tech', 'name' => 'Monitor 24 polegadas', 'description' => 'Monitor LED Full HD', 'sku' => 'MON-024', 'barcode' => '789000000004', 'unit' => 'un', 'cost_price' => 620, 'sale_price' => 899.9, 'current_quantity' => 6],
            ['key' => 'headset', 'category' => 'electronics', 'supplier' => 'tech', 'name' => 'Headset USB', 'description' => 'Headset para atendimento', 'sku' => 'HED-001', 'barcode' => '789000000005', 'unit' => 'un', 'cost_price' => 95, 'sale_price' => 149.9, 'current_quantity' => 22],
            ['key' => 'paper', 'category' => 'office', 'supplier' => 'office', 'name' => 'Papel A4', 'description' => 'Resma de papel sulfite A4', 'sku' => 'PAP-001', 'barcode' => '789000000006', 'unit' => 'resma', 'cost_price' => 18, 'sale_price' => 29.9, 'current_quantity' => 30],
            ['key' => 'pen', 'category' => 'office', 'supplier' => 'office', 'name' => 'Caneta azul', 'description' => 'Caixa com 50 canetas esferográficas', 'sku' => 'CAN-001', 'barcode' => '789000000007', 'unit' => 'cx', 'cost_price' => 32, 'sale_price' => 49.9, 'current_quantity' => 45],
            ['key' => 'folder', 'category' => 'office', 'supplier' => 'office', 'name' => 'Pasta suspensa', 'description' => 'Pacote com 25 pastas suspensas', 'sku' => 'PAS-001', 'barcode' => '789000000008', 'unit' => 'pct', 'cost_price' => 28, 'sale_price' => 42.9, 'current_quantity' => 16],
            ['key' => 'label', 'category' => 'office', 'supplier' => 'office', 'name' => 'Etiqueta adesiva', 'description' => 'Folhas de etiqueta para impressora', 'sku' => 'ETI-001', 'barcode' => '789000000009', 'unit' => 'pct', 'cost_price' => 21, 'sale_price' => 34.9, 'current_quantity' => 28],
            ['key' => 'detergent', 'category' => 'cleaning', 'supplier' => 'cleaning', 'name' => 'Detergente neutro', 'description' => 'Frasco de detergente neutro 500 ml', 'sku' => 'DET-001', 'barcode' => '789000000010', 'unit' => 'un', 'cost_price' => 2.8, 'sale_price' => 5.5, 'current_quantity' => 80],
            ['key' => 'alcohol', 'category' => 'cleaning', 'supplier' => 'cleaning', 'name' => 'Álcool 70%', 'description' => 'Frasco de álcool líquido 1 litro', 'sku' => 'ALC-001', 'barcode' => '789000000011', 'unit' => 'un', 'cost_price' => 8.5, 'sale_price' => 14.9, 'current_quantity' => 35],
            ['key' => 'paper_towel', 'category' => 'cleaning', 'supplier' => 'cleaning', 'name' => 'Papel toalha', 'description' => 'Fardo com papel toalha interfolhado', 'sku' => 'PTO-001', 'barcode' => '789000000012', 'unit' => 'fardo', 'cost_price' => 42, 'sale_price' => 69.9, 'current_quantity' => 14],
            ['key' => 'glove', 'category' => 'cleaning', 'supplier' => 'cleaning', 'name' => 'Luva descartável', 'description' => 'Caixa com 100 luvas descartáveis', 'sku' => 'LUV-001', 'barcode' => '789000000013', 'unit' => 'cx', 'cost_price' => 24, 'sale_price' => 39.9, 'current_quantity' => 25],
            ['key' => 'hammer', 'category' => 'tools', 'supplier' => 'tools', 'name' => 'Martelo', 'description' => 'Martelo de unha com cabo emborrachado', 'sku' => 'MAR-001', 'barcode' => '789000000014', 'unit' => 'un', 'cost_price' => 28, 'sale_price' => 45.9, 'current_quantity' => 9],
            ['key' => 'screwdriver', 'category' => 'tools', 'supplier' => 'tools', 'name' => 'Chave de fenda', 'description' => 'Chave de fenda média', 'sku' => 'CHF-001', 'barcode' => '789000000015', 'unit' => 'un', 'cost_price' => 12, 'sale_price' => 22.9, 'current_quantity' => 20],
            ['key' => 'drill', 'category' => 'tools', 'supplier' => 'tools', 'name' => 'Furadeira', 'description' => 'Furadeira elétrica 600 W', 'sku' => 'FUR-001', 'barcode' => '789000000016', 'unit' => 'un', 'cost_price' => 180, 'sale_price' => 279.9, 'current_quantity' => 5],
            ['key' => 'box_small', 'category' => 'packaging', 'supplier' => 'packaging', 'name' => 'Caixa pequena', 'description' => 'Caixa de papelão pequena', 'sku' => 'CXP-001', 'barcode' => '789000000017', 'unit' => 'un', 'cost_price' => 1.4, 'sale_price' => 3.5, 'current_quantity' => 120],
            ['key' => 'box_large', 'category' => 'packaging', 'supplier' => 'packaging', 'name' => 'Caixa grande', 'description' => 'Caixa de papelão grande', 'sku' => 'CXG-001', 'barcode' => '789000000018', 'unit' => 'un', 'cost_price' => 4.2, 'sale_price' => 8.9, 'current_quantity' => 75],
            ['key' => 'tape', 'category' => 'packaging', 'supplier' => 'packaging', 'name' => 'Fita adesiva', 'description' => 'Rolo de fita transparente', 'sku' => 'FIT-001', 'barcode' => '789000000019', 'unit' => 'rolo', 'cost_price' => 5.5, 'sale_price' => 11.9, 'current_quantity' => 64],
            ['key' => 'coffee', 'category' => 'food', 'supplier' => 'office', 'name' => 'Café torrado', 'description' => 'Pacote de café 500 g', 'sku' => 'CAF-001', 'barcode' => '789000000020', 'unit' => 'pct', 'cost_price' => 14, 'sale_price' => 24.9, 'current_quantity' => 32],
        ];

        $products = [];

        foreach ($items as $item) {
            $products[$item['key']] = Product::updateOrCreate([
                'company_id' => $company->id,
                'sku' => $item['sku'],
            ], [
                'category_id' => $categories[$item['category']]->id,
                'supplier_id' => $suppliers[$item['supplier']]->id,
                'name' => $item['name'],
                'description' => $item['description'],
                'barcode' => $item['barcode'],
                'unit' => $item['unit'],
                'cost_price' => $item['cost_price'],
                'sale_price' => $item['sale_price'],
                'current_quantity' => $item['current_quantity'],
            ]);
        }

        return $products;
    }

    private function movements(Company $company, User $stockist, array $products): void
    {
        $items = [
            ['notebook', 'entry', 10, 0, 10, 'Compra inicial'],
            ['notebook', 'exit', 3, 10, 7, 'Retirada para uso interno'],
            ['mouse', 'entry', 15, 0, 15, 'Compra inicial'],
            ['mouse', 'adjustment', 18, 15, 18, 'Ajuste aprovado pela contagem piloto'],
            ['keyboard', 'entry', 12, 0, 12, 'Compra inicial'],
            ['monitor', 'entry', 8, 0, 8, 'Compra inicial'],
            ['monitor', 'exit', 2, 8, 6, 'Instalação em sala de reunião'],
            ['headset', 'entry', 25, 0, 25, 'Compra inicial'],
            ['headset', 'exit', 3, 25, 22, 'Entrega para atendimento'],
            ['paper', 'entry', 30, 0, 30, 'Compra inicial'],
            ['pen', 'entry', 50, 0, 50, 'Compra inicial'],
            ['pen', 'exit', 5, 50, 45, 'Retirada administrativa'],
            ['folder', 'entry', 20, 0, 20, 'Compra inicial'],
            ['folder', 'exit', 4, 20, 16, 'Organização de arquivo'],
            ['label', 'entry', 28, 0, 28, 'Compra inicial'],
            ['detergent', 'entry', 90, 0, 90, 'Compra inicial'],
            ['detergent', 'exit', 10, 90, 80, 'Consumo semanal'],
            ['alcohol', 'entry', 40, 0, 40, 'Compra inicial'],
            ['alcohol', 'exit', 5, 40, 35, 'Consumo semanal'],
            ['paper_towel', 'entry', 18, 0, 18, 'Compra inicial'],
            ['paper_towel', 'exit', 4, 18, 14, 'Reposição copa'],
            ['glove', 'entry', 25, 0, 25, 'Compra inicial'],
            ['hammer', 'entry', 10, 0, 10, 'Compra inicial'],
            ['hammer', 'exit', 1, 10, 9, 'Kit manutenção'],
            ['screwdriver', 'entry', 20, 0, 20, 'Compra inicial'],
            ['drill', 'entry', 6, 0, 6, 'Compra inicial'],
            ['drill', 'exit', 1, 6, 5, 'Uso manutenção'],
            ['box_small', 'entry', 150, 0, 150, 'Compra inicial'],
            ['box_small', 'exit', 30, 150, 120, 'Envios do mês'],
            ['box_large', 'entry', 80, 0, 80, 'Compra inicial'],
            ['box_large', 'exit', 5, 80, 75, 'Envios do mês'],
            ['tape', 'entry', 70, 0, 70, 'Compra inicial'],
            ['tape', 'exit', 6, 70, 64, 'Envios do mês'],
            ['coffee', 'entry', 40, 0, 40, 'Compra inicial'],
            ['coffee', 'exit', 8, 40, 32, 'Consumo copa'],
            ['paper', 'adjustment', 30, 32, 30, 'Correção por conferência manual'],
            ['keyboard', 'exit', 2, 14, 12, 'Substituição de equipamentos'],
            ['label', 'exit', 3, 31, 28, 'Etiquetagem de produtos'],
            ['glove', 'adjustment', 25, 22, 25, 'Correção por sobra física'],
            ['coffee', 'adjustment', 32, 30, 32, 'Correção por sobra física'],
        ];

        foreach ($items as [$key, $type, $quantity, $before, $after, $reason]) {
            $this->movement($company, $products[$key], $stockist, $type, $quantity, $before, $after, $reason);
        }
    }

    private function inventoryCounts(Company $company, User $admin, User $counter, array $products): void
    {
        $pilot = $this->count($company, $admin, 'Contagem piloto', 'in_progress', now()->subDays(2), null, null);
        $this->countItem($pilot, $products['notebook'], $counter, 10, 7);
        $this->countItem($pilot, $products['mouse'], $counter, 15, 18);
        $this->countItem($pilot, $products['paper'], $counter, 30, 30);
        $this->countItem($pilot, $products['pen'], $counter, 45, 42);
        $this->countItem($pilot, $products['box_small'], $counter, 120, 126);

        $open = $this->count($company, $admin, 'Contagem almoxarifado', 'open', now()->subHours(6), null, null);
        $this->pendingCountItem($open, $products['keyboard'], 12);
        $this->pendingCountItem($open, $products['monitor'], 6);
        $this->pendingCountItem($open, $products['headset'], 22);
        $this->pendingCountItem($open, $products['tape'], 64);

        $finished = $this->count($company, $admin, 'Contagem escritório', 'finished', now()->subDays(5), now()->subDays(4), null);
        $this->countItem($finished, $products['folder'], $counter, 16, 15);
        $this->countItem($finished, $products['label'], $counter, 28, 31);
        $this->countItem($finished, $products['coffee'], $counter, 32, 32);
        $this->countItem($finished, $products['paper_towel'], $counter, 14, 12);

        $approved = $this->count($company, $admin, 'Contagem limpeza aprovada', 'approved', now()->subDays(12), now()->subDays(11), now()->subDays(10));
        $this->countItem($approved, $products['detergent'], $counter, 82, 80);
        $this->countItem($approved, $products['alcohol'], $counter, 34, 35);
        $this->countItem($approved, $products['glove'], $counter, 25, 25);
        $this->countItem($approved, $products['hammer'], $counter, 9, 8);
    }

    private function count(Company $company, User $admin, string $title, string $status, $startedAt, $finishedAt, $approvedAt): InventoryCount
    {
        return InventoryCount::updateOrCreate([
            'company_id' => $company->id,
            'title' => $title,
        ], [
            'created_by' => $admin->id,
            'status' => $status,
            'started_at' => $startedAt,
            'finished_at' => $finishedAt,
            'approved_at' => $approvedAt,
        ]);
    }

    private function movement(Company $company, Product $product, User $user, string $type, float $quantity, float $before, float $after, string $reason): StockMovement
    {
        return StockMovement::updateOrCreate([
            'company_id' => $company->id,
            'product_id' => $product->id,
            'type' => $type,
            'reason' => $reason,
        ], [
            'user_id' => $user->id,
            'quantity' => $quantity,
            'quantity_before' => $before,
            'quantity_after' => $after,
        ]);
    }

    private function countItem(InventoryCount $count, Product $product, User $counter, float $systemQuantity, float $countedQuantity): void
    {
        $count->items()->updateOrCreate([
            'product_id' => $product->id,
        ], [
            'counted_by' => $counter->id,
            'system_quantity' => $systemQuantity,
            'counted_quantity' => $countedQuantity,
            'difference' => $countedQuantity - $systemQuantity,
            'sync_status' => 'synced',
            'counted_at' => now(),
        ]);
    }

    private function pendingCountItem(InventoryCount $count, Product $product, float $systemQuantity): void
    {
        $count->items()->updateOrCreate([
            'product_id' => $product->id,
        ], [
            'counted_by' => null,
            'system_quantity' => $systemQuantity,
            'counted_quantity' => null,
            'difference' => 0,
            'sync_status' => 'pending',
            'counted_at' => null,
        ]);
    }
}
