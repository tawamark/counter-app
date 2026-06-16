<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user),
            ],
            'role' => ['required', Rule::in(['admin', 'stockist', 'counter'])],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:8', 'max:255'],
        ];
    }

    public function validatedData(): array
    {
        $data = $this->validated();

        if (($data['password'] ?? null) === null || $data['password'] === '') {
            unset($data['password']);
        }

        return $data;
    }
}
