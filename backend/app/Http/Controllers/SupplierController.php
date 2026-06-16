<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(): View
    {
        $suppliers = Supplier::where('company_id', auth()->user()->company_id)
            ->withCount('products')
            ->orderBy('name')
            ->paginate(10);

        return view('suppliers.index', [
            'suppliers' => $suppliers,
        ]);
    }

    public function create(): View
    {
        return view('suppliers.create');
    }

    public function store(Request $request, AuditLogService $auditLogService): RedirectResponse
    {
        $data = $this->validateSupplier($request);

        $supplier = Supplier::create([
            'company_id' => auth()->user()->company_id,
            ...$data,
        ]);

        $auditLogService->record(auth()->user(), 'fornecedores', 'criou', 'Fornecedor cadastrado: '.$supplier->name, $supplier);

        return redirect()
            ->route('suppliers.index')
            ->with('status', 'Fornecedor cadastrado com sucesso.');
    }

    public function edit(Supplier $supplier): View
    {
        $this->authorizeSupplier($supplier);

        return view('suppliers.edit', [
            'supplier' => $supplier,
        ]);
    }

    public function update(Request $request, Supplier $supplier, AuditLogService $auditLogService): RedirectResponse
    {
        $this->authorizeSupplier($supplier);

        $supplier->update($this->validateSupplier($request, $supplier));

        $auditLogService->record(auth()->user(), 'fornecedores', 'atualizou', 'Fornecedor atualizado: '.$supplier->name, $supplier);

        return redirect()
            ->route('suppliers.index')
            ->with('status', 'Fornecedor atualizado com sucesso.');
    }

    public function destroy(Supplier $supplier, AuditLogService $auditLogService): RedirectResponse
    {
        $this->authorizeSupplier($supplier);

        if ($supplier->products()->exists()) {
            return redirect()
                ->route('suppliers.index')
                ->with('error', 'Não é possível excluir um fornecedor com produtos vinculados.');
        }

        $supplierName = $supplier->name;

        $supplier->delete();

        $auditLogService->record(auth()->user(), 'fornecedores', 'excluiu', 'Fornecedor excluído: '.$supplierName);

        return redirect()
            ->route('suppliers.index')
            ->with('status', 'Fornecedor excluído com sucesso.');
    }

    private function validateSupplier(Request $request, ?Supplier $supplier = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'cnpj' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('suppliers')->where('company_id', auth()->user()->company_id)->ignore($supplier),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);
    }

    private function authorizeSupplier(Supplier $supplier): void
    {
        abort_unless($supplier->company_id === auth()->user()->company_id, 404);
    }
}
