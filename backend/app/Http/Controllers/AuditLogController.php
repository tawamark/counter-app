<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $companyId = auth()->user()->company_id;

        $filters = $request->validate([
            'module' => ['nullable', Rule::in(['categorias', 'fornecedores', 'produtos', 'usuarios', 'movimentacoes', 'contagens'])],
            'action' => ['nullable', Rule::in(['criou', 'atualizou', 'excluiu', 'registrou', 'finalizou', 'aprovou', 'alterou'])],
            'user_id' => ['nullable', Rule::exists('users', 'id')->where('company_id', $companyId)],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $logs = AuditLog::with('user')
            ->where('company_id', $companyId)
            ->when($filters['module'] ?? null, fn ($query, $module) => $query->where('module', $module))
            ->when($filters['action'] ?? null, fn ($query, $action) => $query->where('action', $action))
            ->when($filters['user_id'] ?? null, fn ($query, $userId) => $query->where('user_id', $userId))
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '<=', $date))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('audit-logs.index', [
            'logs' => $logs,
            'filters' => $filters,
            'users' => User::where('company_id', $companyId)->orderBy('name')->get(),
        ]);
    }
}
