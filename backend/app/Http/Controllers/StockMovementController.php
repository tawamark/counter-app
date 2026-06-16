<?php

namespace App\Http\Controllers;

use App\DTOs\StockMovementData;
use App\Http\Requests\StoreStockMovementRequest;
use App\Models\User;
use App\Repositories\ProductRepository;
use App\Repositories\StockMovementRepository;
use App\Services\StockMovementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StockMovementController extends Controller
{
    public function index(Request $request, ProductRepository $products, StockMovementRepository $stockMovements): View
    {
        $companyId = auth()->user()->company_id;

        $filters = $request->validate([
            'product_id' => ['nullable', Rule::exists('products', 'id')->where('company_id', $companyId)],
            'type' => ['nullable', Rule::in(['entry', 'exit', 'adjustment'])],
            'user_id' => ['nullable', Rule::exists('users', 'id')->where('company_id', $companyId)],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        return view('stock-movements.index', [
            'movements' => $stockMovements->paginateForCompany($companyId, $filters),
            'products' => $products->listForCompany($companyId),
            'users' => User::where('company_id', $companyId)->orderBy('name')->get(),
            'filters' => $filters,
        ]);
    }

    public function create(ProductRepository $products): View
    {
        return view('stock-movements.create', [
            'products' => $products->listForCompany(auth()->user()->company_id),
        ]);
    }

    public function store(StoreStockMovementRequest $request, ProductRepository $products, StockMovementService $service): RedirectResponse
    {
        $companyId = $request->user()->company_id;
        $data = StockMovementData::fromArray($request->validated());
        $product = $products->findForCompany($companyId, $data->productId);

        $service->register(
            $request->user(),
            $product,
            $data->type,
            $data->quantity,
            $data->reason
        );

        return redirect()
            ->route('stock-movements.index')
            ->with('status', 'Movimentação registrada com sucesso.');
    }
}
