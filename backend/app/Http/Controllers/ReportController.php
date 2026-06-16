<?php

namespace App\Http\Controllers;

use App\Models\InventoryCountItem;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index()
    {
        $companyId = auth()->user()->company_id;

        $stockSummary = Product::where('company_id', $companyId)
            ->selectRaw('COUNT(*) as products_count')
            ->selectRaw('COALESCE(SUM(current_quantity), 0) as quantity_sum')
            ->selectRaw('COALESCE(SUM(current_quantity * cost_price), 0) as cost_value')
            ->first();

        $movementSummary = StockMovement::where('company_id', $companyId)
            ->selectRaw('COUNT(*) as movements_count')
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'entry' THEN 1 ELSE 0 END), 0) as entries_count")
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'exit' THEN 1 ELSE 0 END), 0) as exits_count")
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'adjustment' THEN 1 ELSE 0 END), 0) as adjustments_count")
            ->first();

        $divergenceSummary = null;

        if (auth()->user()->role === 'admin') {
            $divergenceSummary = InventoryCountItem::query()
                ->whereHas('inventoryCount', fn ($query) => $query->where('company_id', $companyId))
                ->whereNotNull('counted_quantity')
                ->selectRaw('COALESCE(SUM(CASE WHEN difference < 0 THEN 1 ELSE 0 END), 0) as shortages')
                ->selectRaw('COALESCE(SUM(CASE WHEN difference > 0 THEN 1 ELSE 0 END), 0) as surpluses')
                ->selectRaw('COALESCE(SUM(CASE WHEN difference = 0 THEN 1 ELSE 0 END), 0) as matches')
                ->first();
        }

        return view('reports.index', [
            'stockSummary' => $stockSummary,
            'movementSummary' => $movementSummary,
            'divergenceSummary' => $divergenceSummary,
            'products' => Product::where('company_id', $companyId)->orderBy('name')->get(),
            'users' => User::where('company_id', $companyId)->orderBy('name')->get(),
        ]);
    }

    public function stock(): StreamedResponse
    {
        $companyId = auth()->user()->company_id;

        $products = Product::with(['category', 'supplier'])
            ->where('company_id', $companyId)
            ->orderBy('name')
            ->get()
            ->map(fn (Product $product): array => [
                $product->name,
                $product->sku,
                $product->barcode,
                $product->category?->name,
                $product->supplier?->name,
                $product->unit,
                number_format((float) $product->current_quantity, 3, ',', ''),
                number_format((float) $product->cost_price, 2, ',', ''),
                number_format((float) $product->sale_price, 2, ',', ''),
            ]);

        return $this->csv('relatorio-estoque.csv', [
            'Produto',
            'SKU',
            'Código de barras',
            'Categoria',
            'Fornecedor',
            'Unidade',
            'Quantidade atual',
            'Preço de custo',
            'Preço de venda',
        ], $products);
    }

    public function movements(Request $request): StreamedResponse
    {
        $companyId = auth()->user()->company_id;

        $filters = $request->validate([
            'product_id' => ['nullable', Rule::exists('products', 'id')->where('company_id', $companyId)],
            'type' => ['nullable', Rule::in(['entry', 'exit', 'adjustment'])],
            'user_id' => ['nullable', Rule::exists('users', 'id')->where('company_id', $companyId)],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $movements = StockMovement::with(['product', 'user', 'inventoryCount'])
            ->where('company_id', $companyId)
            ->when($filters['product_id'] ?? null, fn ($query, $productId) => $query->where('product_id', $productId))
            ->when($filters['type'] ?? null, fn ($query, $type) => $query->where('type', $type))
            ->when($filters['user_id'] ?? null, fn ($query, $userId) => $query->where('user_id', $userId))
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '<=', $date))
            ->latest()
            ->get()
            ->map(fn (StockMovement $movement): array => [
                $movement->created_at->format('d/m/Y H:i'),
                $movement->product?->name ?? 'Produto removido',
                ['entry' => 'Entrada', 'exit' => 'Saída', 'adjustment' => 'Ajuste'][$movement->type] ?? $movement->type,
                number_format((float) $movement->quantity, 3, ',', ''),
                number_format((float) $movement->quantity_before, 3, ',', ''),
                number_format((float) $movement->quantity_after, 3, ',', ''),
                $movement->user?->name ?? 'Usuário removido',
                $movement->inventoryCount?->title,
                $movement->reason,
            ]);

        return $this->csv('relatorio-movimentacoes.csv', [
            'Data',
            'Produto',
            'Tipo',
            'Quantidade',
            'Saldo anterior',
            'Saldo posterior',
            'Usuário',
            'Contagem vinculada',
            'Motivo',
        ], $movements);
    }

    public function divergences(Request $request): StreamedResponse
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        $filters = $request->validate([
            'type' => ['nullable', Rule::in(['shortage', 'surplus', 'none'])],
        ]);

        $items = InventoryCountItem::with(['inventoryCount', 'product'])
            ->whereHas('inventoryCount', fn ($query) => $query->where('company_id', auth()->user()->company_id))
            ->whereNotNull('counted_quantity')
            ->when(($filters['type'] ?? null) === 'shortage', fn ($query) => $query->where('difference', '<', 0))
            ->when(($filters['type'] ?? null) === 'surplus', fn ($query) => $query->where('difference', '>', 0))
            ->when(($filters['type'] ?? null) === 'none', fn ($query) => $query->where('difference', 0))
            ->latest()
            ->get()
            ->map(fn (InventoryCountItem $item): array => [
                $item->inventoryCount->title,
                $item->product?->name ?? 'Produto removido',
                $item->product?->sku,
                number_format((float) $item->system_quantity, 3, ',', ''),
                number_format((float) $item->counted_quantity, 3, ',', ''),
                number_format((float) $item->difference, 3, ',', ''),
                $this->divergenceType((float) $item->difference),
            ]);

        return $this->csv('relatorio-divergencias.csv', [
            'Contagem',
            'Produto',
            'SKU',
            'Saldo do sistema',
            'Quantidade contada',
            'Diferença',
            'Tipo',
        ], $items);
    }

    private function csv(string $filename, array $header, iterable $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($header, $rows): void {
            $output = fopen('php://output', 'w');

            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, $header, ';');

            foreach ($rows as $row) {
                fputcsv($output, $row, ';');
            }

            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function divergenceType(float $difference): string
    {
        if ($difference < 0) {
            return 'Falta física';
        }

        if ($difference > 0) {
            return 'Sobra física';
        }

        return 'Sem divergência';
    }
}
