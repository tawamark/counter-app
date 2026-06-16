<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::where('company_id', auth()->user()->company_id)
            ->orderBy('name')
            ->paginate(10);

        return view('users.index', [
            'users' => $users,
        ]);
    }

    public function create(): View
    {
        return view('users.create');
    }

    public function store(UserRequest $request, AuditLogService $auditLogService): RedirectResponse
    {
        $user = User::create([
            'company_id' => $request->user()->company_id,
            ...$request->validatedData(),
        ]);

        $auditLogService->record(auth()->user(), 'usuarios', 'criou', 'Usuário cadastrado: '.$user->name, $user, [
            'email' => $user->email,
            'role' => $user->role,
        ]);

        return redirect()
            ->route('users.index')
            ->with('status', 'Usuário cadastrado com sucesso.');
    }

    public function edit(User $user): View
    {
        $this->authorizeUser($user);

        return view('users.edit', [
            'user' => $user,
        ]);
    }

    public function update(UserRequest $request, User $user, AuditLogService $auditLogService): RedirectResponse
    {
        $this->authorizeUser($user);

        $data = $request->validatedData();

        if ($user->id === auth()->id() && $data['role'] !== $user->role) {
            return redirect()
                ->route('users.index')
                ->with('error', 'Não é possível alterar o próprio perfil de acesso.');
        }

        $user->update($data);

        $auditLogService->record(auth()->user(), 'usuarios', 'atualizou', 'Usuário atualizado: '.$user->name, $user, [
            'email' => $user->email,
            'role' => $user->role,
        ]);

        return redirect()
            ->route('users.index')
            ->with('status', 'Usuário atualizado com sucesso.');
    }

    public function destroy(User $user, AuditLogService $auditLogService): RedirectResponse
    {
        $this->authorizeUser($user);

        if ($user->id === auth()->id()) {
            return redirect()
                ->route('users.index')
                ->with('error', 'Não é possível excluir o usuário autenticado.');
        }

        $userName = $user->name;
        $userEmail = $user->email;

        $user->delete();

        $auditLogService->record(auth()->user(), 'usuarios', 'excluiu', 'Usuário excluído: '.$userName, null, [
            'email' => $userEmail,
        ]);

        return redirect()
            ->route('users.index')
            ->with('status', 'Usuário excluído com sucesso.');
    }

    private function authorizeUser(User $user): void
    {
        abort_unless($user->company_id === auth()->user()->company_id, 404);
    }
}
