<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\InventoryCount;
use App\Models\InventoryCountItem;
use App\Repositories\InventoryCountRepository;
use App\Services\InventoryCountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InventoryCountController extends Controller
{
    use ApiResponse;

    public function index(Request $request, InventoryCountRepository $inventoryCounts): JsonResponse
    {
        $data = $request->validate([
            'status' => ['nullable', Rule::in(['open', 'in_progress', 'finished', 'approved'])],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $perPage = $data['per_page'] ?? 15;

        $counts = $inventoryCounts->paginatedForApi($request->user()->company_id, $data, $perPage);

        return $this->paginated($counts, fn (InventoryCount $count) => $this->countData($count), 'Contagens encontradas com sucesso');
    }

    public function show(Request $request, InventoryCount $inventoryCount): JsonResponse
    {
        abort_unless($inventoryCount->company_id === $request->user()->company_id, 404);

        return $this->success($this->countData($inventoryCount->loadCount('items')), 'Contagem encontrada com sucesso');
    }

    public function items(Request $request, InventoryCount $inventoryCount): JsonResponse
    {
        abort_unless($inventoryCount->company_id === $request->user()->company_id, 404);

        $data = $request->validate([
            'sync_status' => ['nullable', Rule::in(['pending', 'synced', 'error'])],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $perPage = $data['per_page'] ?? 50;

        $items = $inventoryCount->items()
            ->with('product')
            ->when($data['sync_status'] ?? null, fn ($query, $status) => $query->where('sync_status', $status))
            ->orderBy('id')
            ->paginate($perPage)
            ->withQueryString();

        return $this->paginated($items, fn (InventoryCountItem $item) => $this->itemData($item), 'Itens encontrados com sucesso');
    }

    public function updateItems(Request $request, InventoryCount $inventoryCount, InventoryCountService $service): JsonResponse
    {
        abort_unless($inventoryCount->company_id === $request->user()->company_id, 404);

        $itemIds = $inventoryCount->items()->pluck('id')->all();

        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'integer', Rule::in($itemIds)],
            'items.*.counted_quantity' => ['nullable', 'numeric', 'min:0', 'max:999999999.999'],
        ]);

        $items = collect($data['items'])->keyBy('id')->all();
        $count = $service->updateItems($request->user(), $inventoryCount, $items);

        return $this->success($this->countData($count->loadCount('items')), 'Itens sincronizados com sucesso');
    }

    private function countData(InventoryCount $count): array
    {
        return [
            'id' => $count->id,
            'title' => $count->title,
            'status' => $count->status,
            'items_count' => $count->items_count,
            'started_at' => $count->started_at?->toISOString(),
            'finished_at' => $count->finished_at?->toISOString(),
            'approved_at' => $count->approved_at?->toISOString(),
        ];
    }

    private function itemData(InventoryCountItem $item): array
    {
        return [
            'id' => $item->id,
            'product' => $item->product ? [
                'id' => $item->product->id,
                'name' => $item->product->name,
                'sku' => $item->product->sku,
                'barcode' => $item->product->barcode,
                'unit' => $item->product->unit,
            ] : null,
            'system_quantity' => (float) $item->system_quantity,
            'counted_quantity' => $item->counted_quantity === null ? null : (float) $item->counted_quantity,
            'difference' => (float) $item->difference,
            'sync_status' => $item->sync_status,
            'counted_at' => $item->counted_at?->toISOString(),
        ];
    }

}
