<?php

namespace App\Services;

use App\Models\InventoryCount;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockMovementService
{
    public function __construct(private readonly AuditLogService $auditLogService)
    {
    }

    public function register(User $user, Product $product, string $type, float $quantity, ?string $reason = null, ?InventoryCount $inventoryCount = null): StockMovement
    {
        if ($product->company_id !== $user->company_id) {
            abort(404);
        }

        if ($inventoryCount !== null && $inventoryCount->company_id !== $user->company_id) {
            abort(404);
        }

        return DB::transaction(function () use ($user, $product, $type, $quantity, $reason, $inventoryCount): StockMovement {
            $product->refresh();

            $quantityBefore = (float) $product->current_quantity;
            $quantityAfter = $this->calculateQuantityAfter($type, $quantityBefore, $quantity);

            $product->update([
                'current_quantity' => $quantityAfter,
            ]);

            $movement = StockMovement::create([
                'company_id' => $user->company_id,
                'product_id' => $product->id,
                'user_id' => $user->id,
                'inventory_count_id' => $inventoryCount?->id,
                'type' => $type,
                'quantity' => $quantity,
                'quantity_before' => $quantityBefore,
                'quantity_after' => $quantityAfter,
                'reason' => $reason,
            ]);

            $this->auditLogService->record($user, 'movimentacoes', 'registrou', 'Movimentação registrada para '.$product->name, $movement, [
                'type' => $type,
                'quantity' => $quantity,
                'quantity_before' => $quantityBefore,
                'quantity_after' => $quantityAfter,
            ]);

            return $movement;
        });
    }

    private function calculateQuantityAfter(string $type, float $quantityBefore, float $quantity): float
    {
        if ($type === 'entry') {
            return $quantityBefore + $quantity;
        }

        if ($type === 'exit') {
            if ($quantity > $quantityBefore) {
                throw ValidationException::withMessages([
                    'quantity' => 'A quantidade de saída não pode ser maior que o saldo atual.',
                ]);
            }

            return $quantityBefore - $quantity;
        }

        if ($type === 'adjustment') {
            return $quantity;
        }

        throw ValidationException::withMessages([
            'type' => 'Tipo de movimentação inválido.',
        ]);
    }
}
