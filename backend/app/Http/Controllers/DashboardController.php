<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\InventoryCount;
use App\Models\InventoryCountItem;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $companyId = auth()->user()->company_id;
        $movementSummary = StockMovement::where('company_id', $companyId)
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'entry' THEN 1 ELSE 0 END), 0) as entries")
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'exit' THEN 1 ELSE 0 END), 0) as exits")
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'adjustment' THEN 1 ELSE 0 END), 0) as adjustments")
            ->first();
        $movementChart = collect([
            ['label' => 'Entradas', 'value' => (int) ($movementSummary->entries ?? 0), 'class' => 'bg-counter-primary'],
            ['label' => 'Saídas', 'value' => (int) ($movementSummary->exits ?? 0), 'class' => 'bg-[#323232]'],
            ['label' => 'Ajustes', 'value' => (int) ($movementSummary->adjustments ?? 0), 'class' => 'bg-[#8f8f8f]'],
        ]);
        $movementTotal = max(1, $movementChart->sum('value'));
        $countStatusSummary = InventoryCount::where('company_id', $companyId)
            ->selectRaw("COALESCE(SUM(CASE WHEN status = 'open' THEN 1 ELSE 0 END), 0) as open")
            ->selectRaw("COALESCE(SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END), 0) as in_progress")
            ->selectRaw("COALESCE(SUM(CASE WHEN status = 'finished' THEN 1 ELSE 0 END), 0) as finished")
            ->selectRaw("COALESCE(SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END), 0) as approved")
            ->first();
        $countStatusChart = collect([
            ['label' => 'Abertas', 'value' => (int) ($countStatusSummary->open ?? 0), 'class' => 'bg-[#fbbf24]'],
            ['label' => 'Em andamento', 'value' => (int) ($countStatusSummary->in_progress ?? 0), 'class' => 'bg-counter-primary'],
            ['label' => 'Finalizadas', 'value' => (int) ($countStatusSummary->finished ?? 0), 'class' => 'bg-[#6f6f6f]'],
            ['label' => 'Aprovadas', 'value' => (int) ($countStatusSummary->approved ?? 0), 'class' => 'bg-[#323232]'],
        ]);
        $countStatusTotal = max(1, $countStatusChart->sum('value'));
        $stockByCategory = Product::query()
            ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
            ->where('products.company_id', $companyId)
            ->selectRaw("COALESCE(categories.name, 'Sem categoria') as label")
            ->selectRaw('COALESCE(SUM(products.current_quantity), 0) as value')
            ->groupBy('label')
            ->orderByDesc('value')
            ->limit(5)
            ->get()
            ->map(fn ($item): array => [
                'label' => $item->label,
                'value' => (float) $item->value,
            ]);
        $stockByCategoryMax = max(1, (float) $stockByCategory->max('value'));
        $recentMovementDays = collect(range(6, 0))
            ->map(function (int $days) use ($companyId): array {
                $date = Carbon::today()->subDays($days);
                $count = StockMovement::where('company_id', $companyId)
                    ->whereDate('created_at', $date)
                    ->count();

                return [
                    'label' => $date->format('d/m'),
                    'value' => $count,
                ];
            });
        $recentMovementDaysMax = max(1, (int) $recentMovementDays->max('value'));

        return view('dashboard.index', [
            'totalProducts' => Product::where('company_id', $companyId)->count(),
            'totalCategories' => Category::where('company_id', $companyId)->count(),
            'totalSuppliers' => Supplier::where('company_id', $companyId)->count(),
            'openInventoryCounts' => InventoryCount::where('company_id', $companyId)
                ->whereIn('status', ['open', 'in_progress'])
                ->count(),
            'shortageItems' => InventoryCountItem::whereHas('inventoryCount', fn ($query) => $query->where('company_id', $companyId))
                ->where('difference', '<', 0)
                ->whereNotNull('counted_quantity')
                ->count(),
            'surplusItems' => InventoryCountItem::whereHas('inventoryCount', fn ($query) => $query->where('company_id', $companyId))
                ->where('difference', '>', 0)
                ->whereNotNull('counted_quantity')
                ->count(),
            'recentInventoryCounts' => InventoryCount::with('creator')
                ->where('company_id', $companyId)
                ->latest()
                ->limit(5)
                ->get(),
            'recentMovements' => StockMovement::with(['product', 'user'])
                ->where('company_id', $companyId)
                ->latest()
                ->limit(5)
                ->get(),
            'lowStockProducts' => Product::where('company_id', $companyId)
                ->where('current_quantity', '<=', 5)
                ->orderBy('current_quantity')
                ->orderBy('name')
                ->limit(5)
                ->get(),
            'movementChart' => $movementChart,
            'movementTotal' => $movementTotal,
            'countStatusChart' => $countStatusChart,
            'countStatusTotal' => $countStatusTotal,
            'stockByCategory' => $stockByCategory,
            'stockByCategoryMax' => $stockByCategoryMax,
            'recentMovementDays' => $recentMovementDays,
            'recentMovementDaysMax' => $recentMovementDaysMax,
        ]);
    }
}
