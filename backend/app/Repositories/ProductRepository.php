<?php

namespace App\Repositories;

use App\DTOs\ProductData;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository
{
    public function paginateForCompany(int $companyId, int $perPage = 10): LengthAwarePaginator
    {
        return Product::with(['category', 'supplier'])
            ->where('company_id', $companyId)
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function listForCompany(int $companyId): Collection
    {
        return Product::where('company_id', $companyId)
            ->orderBy('name')
            ->get();
    }

    public function findForCompany(int $companyId, int $productId): Product
    {
        return Product::where('company_id', $companyId)->findOrFail($productId);
    }

    public function createForCompany(int $companyId, ProductData $data): Product
    {
        return Product::create([
            'company_id' => $companyId,
            ...$data->toArray(),
        ]);
    }

    public function update(Product $product, ProductData $data): bool
    {
        return $product->update($data->toArray());
    }

    public function paginatedSearchForCompany(int $companyId, string $term, int $perPage): LengthAwarePaginator
    {
        return Product::with(['category', 'supplier'])
            ->where('company_id', $companyId)
            ->when($term !== '', fn ($query) => $query->where(fn ($query) => $query
                ->where('name', 'like', "%{$term}%")
                ->orWhere('sku', 'like', "%{$term}%")
                ->orWhere('barcode', 'like', "%{$term}%")))
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();
    }
}
