<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInventoryCountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $companyId = $this->user()->company_id;

        return [
            'title' => ['required', 'string', 'max:255'],
            'product_ids' => ['required', 'array', 'min:1'],
            'product_ids.*' => ['integer', Rule::exists('products', 'id')->where('company_id', $companyId)],
        ];
    }
}
