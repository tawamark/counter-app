<?php

namespace App\Repositories;

use App\Models\StockMovement;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class StockMovementRepository
{
    public function paginateForCompany(int $companyId, array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return StockMovement::with(['product', 'user'])
            ->where('company_id', $companyId)
            ->when($filters['product_id'] ?? null, fn ($query, $productId) => $query->where('product_id', $productId))
            ->when($filters['type'] ?? null, fn ($query, $type) => $query->where('type', $type))
            ->when($filters['user_id'] ?? null, fn ($query, $userId) => $query->where('user_id', $userId))
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '<=', $date))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }
}
