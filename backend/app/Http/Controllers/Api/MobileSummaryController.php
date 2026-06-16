<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\InventoryCount;
use App\Models\InventoryCountItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileSummaryController extends Controller
{
    use ApiResponse;

    public function __invoke(Request $request): JsonResponse
    {
        $companyId = $request->user()->company_id;

        $openCountIds = InventoryCount::where('company_id', $companyId)
            ->whereIn('status', ['open', 'in_progress'])
            ->pluck('id');

        $items = InventoryCountItem::whereIn('inventory_count_id', $openCountIds);

        return $this->success([
            'open_counts' => $openCountIds->count(),
            'pending_items' => (clone $items)->where('sync_status', 'pending')->count(),
            'synced_items' => (clone $items)->where('sync_status', 'synced')->count(),
            'counted_items' => (clone $items)->whereNotNull('counted_quantity')->count(),
            'last_counted_at' => (clone $items)->whereNotNull('counted_at')->max('counted_at'),
        ], 'Resumo mobile encontrado com sucesso');
    }
}
