<?php

namespace App\Repositories;

use App\Models\InventoryCount;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class InventoryCountRepository
{
    public function paginateForCompany(int $companyId, int $perPage = 10): LengthAwarePaginator
    {
        return InventoryCount::with('creator')
            ->withCount('items')
            ->where('company_id', $companyId)
            ->latest()
            ->paginate($perPage);
    }

    public function productsForCompany(int $companyId): Collection
    {
        return Product::where('company_id', $companyId)
            ->orderBy('name')
            ->get();
    }

    public function productsForCountCreation(int $companyId, array $productIds): Collection
    {
        return Product::where('company_id', $companyId)
            ->whereIn('id', $productIds)
            ->orderBy('name')
            ->get();
    }

    public function paginatedForApi(int $companyId, array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return InventoryCount::withCount('items')
            ->where('company_id', $companyId)
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when(! ($filters['status'] ?? null), fn ($query) => $query->whereIn('status', ['open', 'in_progress']))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }
}
