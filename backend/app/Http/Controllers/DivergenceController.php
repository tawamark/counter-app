<?php

namespace App\Http\Controllers;

use App\Models\InventoryCountItem;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DivergenceController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'type' => ['nullable', 'in:shortage,surplus,none'],
        ]);

        $items = InventoryCountItem::with(['inventoryCount', 'product'])
            ->whereHas('inventoryCount', fn ($query) => $query->where('company_id', auth()->user()->company_id))
            ->whereNotNull('counted_quantity')
            ->when(($filters['type'] ?? null) === 'shortage', fn ($query) => $query->where('difference', '<', 0))
            ->when(($filters['type'] ?? null) === 'surplus', fn ($query) => $query->where('difference', '>', 0))
            ->when(($filters['type'] ?? null) === 'none', fn ($query) => $query->where('difference', 0))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $summary = InventoryCountItem::query()
            ->whereHas('inventoryCount', fn ($query) => $query->where('company_id', auth()->user()->company_id))
            ->whereNotNull('counted_quantity')
            ->selectRaw('SUM(CASE WHEN difference < 0 THEN 1 ELSE 0 END) as shortages')
            ->selectRaw('SUM(CASE WHEN difference > 0 THEN 1 ELSE 0 END) as surpluses')
            ->selectRaw('SUM(CASE WHEN difference = 0 THEN 1 ELSE 0 END) as matches')
            ->first();

        return view('divergences.index', [
            'items' => $items,
            'filters' => $filters,
            'summary' => $summary,
        ]);
    }
}
