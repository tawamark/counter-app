<?php

namespace App\Services;

use App\Models\InventoryCount;
use App\Models\User;
use App\Repositories\InventoryCountRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryCountService
{
    public function __construct(
        private readonly AuditLogService $auditLogService,
        private readonly InventoryCountRepository $inventoryCounts,
    ) {
    }

    public function create(User $user, string $title, array $productIds): InventoryCount
    {
        return DB::transaction(function () use ($user, $title, $productIds): InventoryCount {
            $products = $this->inventoryCounts->productsForCountCreation($user->company_id, $productIds);

            $count = InventoryCount::create([
                'company_id' => $user->company_id,
                'created_by' => $user->id,
                'title' => $title,
                'status' => 'open',
                'started_at' => now(),
            ]);

            foreach ($products as $product) {
                $count->items()->create([
                    'product_id' => $product->id,
                    'system_quantity' => $product->current_quantity,
                    'difference' => 0,
                    'sync_status' => 'pending',
                ]);
            }

            $this->auditLogService->record($user, 'contagens', 'criou', 'Contagem criada: '.$count->title, $count, [
                'products_count' => $products->count(),
            ]);

            return $count;
        });
    }

    public function updateItems(User $user, InventoryCount $count, array $items): InventoryCount
    {
        if ($count->company_id !== $user->company_id) {
            abort(404);
        }

        if (in_array($count->status, ['finished', 'approved'], true)) {
            throw ValidationException::withMessages([
                'items' => 'Não é possível alterar uma contagem finalizada ou aprovada.',
            ]);
        }

        return DB::transaction(function () use ($user, $count, $items): InventoryCount {
            $count->load('items');

            foreach ($count->items as $item) {
                if (! array_key_exists($item->id, $items)) {
                    continue;
                }

                $countedQuantity = $items[$item->id]['counted_quantity'];

                if ($countedQuantity === null || $countedQuantity === '') {
                    $item->update([
                        'counted_by' => null,
                        'counted_quantity' => null,
                        'difference' => 0,
                        'sync_status' => 'pending',
                        'counted_at' => null,
                    ]);

                    continue;
                }

                $quantity = (float) $countedQuantity;

                $item->update([
                    'counted_by' => $user->id,
                    'counted_quantity' => $quantity,
                    'difference' => $quantity - (float) $item->system_quantity,
                    'sync_status' => 'synced',
                    'counted_at' => now(),
                ]);
            }

            if ($count->status === 'open') {
                $count->update([
                    'status' => 'in_progress',
                ]);
            }

            $this->auditLogService->record($user, 'contagens', 'alterou', 'Itens da contagem atualizados: '.$count->title, $count);

            return $count->refresh();
        });
    }

    public function finish(User $user, InventoryCount $count): InventoryCount
    {
        if ($count->company_id !== $user->company_id) {
            abort(404);
        }

        if ($count->status === 'approved') {
            throw ValidationException::withMessages([
                'count' => 'Não é possível finalizar uma contagem aprovada.',
            ]);
        }

        $missingItems = $count->items()
            ->whereNull('counted_quantity')
            ->exists();

        if ($missingItems) {
            throw ValidationException::withMessages([
                'count' => 'Informe a quantidade contada de todos os itens antes de finalizar.',
            ]);
        }

        $count->update([
            'status' => 'finished',
            'finished_at' => now(),
        ]);

        $this->auditLogService->record($user, 'contagens', 'finalizou', 'Contagem finalizada: '.$count->title, $count);

        return $count->refresh();
    }

    public function approve(User $user, InventoryCount $count, StockMovementService $stockMovementService): InventoryCount
    {
        if ($count->company_id !== $user->company_id) {
            abort(404);
        }

        if ($count->status !== 'finished') {
            throw ValidationException::withMessages([
                'count' => 'Somente contagens finalizadas podem ser aprovadas.',
            ]);
        }

        return DB::transaction(function () use ($user, $count, $stockMovementService): InventoryCount {
            $count->load('items.product');

            foreach ($count->items as $item) {
                if ($item->counted_quantity === null || (float) $item->difference === 0.0 || $item->product === null) {
                    continue;
                }

                $stockMovementService->register(
                    $user,
                    $item->product,
                    'adjustment',
                    (float) $item->counted_quantity,
                    'Ajuste aprovado pela contagem '.$count->title,
                    $count
                );
            }

            $count->update([
                'status' => 'approved',
                'approved_at' => now(),
            ]);

            $this->auditLogService->record($user, 'contagens', 'aprovou', 'Contagem aprovada: '.$count->title, $count);

            return $count->refresh();
        });
    }
}
