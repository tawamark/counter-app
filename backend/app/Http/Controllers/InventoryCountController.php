<?php

namespace App\Http\Controllers;

use App\DTOs\InventoryCountData;
use App\Http\Requests\StoreInventoryCountRequest;
use App\Http\Requests\UpdateInventoryCountItemsRequest;
use App\Models\InventoryCount;
use App\Repositories\InventoryCountRepository;
use App\Services\InventoryCountService;
use App\Services\StockMovementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InventoryCountController extends Controller
{
    public function index(InventoryCountRepository $inventoryCounts): View
    {
        return view('inventory-counts.index', [
            'counts' => $inventoryCounts->paginateForCompany(auth()->user()->company_id),
        ]);
    }

    public function create(InventoryCountRepository $inventoryCounts): View
    {
        return view('inventory-counts.create', [
            'products' => $inventoryCounts->productsForCompany(auth()->user()->company_id),
        ]);
    }

    public function store(StoreInventoryCountRequest $request, InventoryCountService $service): RedirectResponse
    {
        $data = InventoryCountData::fromArray($request->validated());
        $count = $service->create($request->user(), $data->title, $data->productIds);

        return redirect()
            ->route('inventory-counts.show', $count)
            ->with('status', 'Contagem criada com sucesso.');
    }

    public function show(InventoryCount $inventoryCount): View
    {
        abort_unless($inventoryCount->company_id === auth()->user()->company_id, 404);

        $inventoryCount->load(['creator', 'items.product', 'items.counter', 'adjustmentMovements.product', 'adjustmentMovements.user']);

        return view('inventory-counts.show', [
            'count' => $inventoryCount,
        ]);
    }

    public function updateItems(UpdateInventoryCountItemsRequest $request, InventoryCount $inventoryCount, InventoryCountService $service): RedirectResponse
    {
        abort_unless($inventoryCount->company_id === auth()->user()->company_id, 404);

        $service->updateItems($request->user(), $inventoryCount, $request->itemsById());

        return redirect()
            ->route('inventory-counts.show', $inventoryCount)
            ->with('status', 'Itens da contagem atualizados com sucesso.');
    }

    public function finish(InventoryCount $inventoryCount, InventoryCountService $service): RedirectResponse
    {
        abort_unless($inventoryCount->company_id === auth()->user()->company_id, 404);

        $service->finish(auth()->user(), $inventoryCount);

        return redirect()
            ->route('inventory-counts.show', $inventoryCount)
            ->with('status', 'Contagem finalizada com sucesso.');
    }

    public function approve(InventoryCount $inventoryCount, InventoryCountService $service, StockMovementService $stockMovementService): RedirectResponse
    {
        abort_unless($inventoryCount->company_id === auth()->user()->company_id, 404);

        $service->approve(auth()->user(), $inventoryCount, $stockMovementService);

        return redirect()
            ->route('inventory-counts.show', $inventoryCount)
            ->with('status', 'Ajustes aprovados com sucesso.');
    }
}
