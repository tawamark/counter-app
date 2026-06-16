<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiResponse;

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::with('company')->where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'As credenciais informadas são inválidas.',
            ]);
        }

        $token = $user->createToken('mobile')->plainTextToken;

        return $this->success([
            'token' => $token,
            'user' => $this->userData($user),
        ], 'Login realizado com sucesso');
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return $this->success(null, 'Logout realizado com sucesso');
    }

    public function me(Request $request): JsonResponse
    {
        return $this->success($this->userData($request->user()->load('company')), 'Usuário autenticado');
    }

    private function userData(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'company' => [
                'id' => $user->company?->id,
                'name' => $user->company?->name,
            ],
        ];
    }

}
