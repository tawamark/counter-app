<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStockMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $companyId = $this->user()->company_id;

        return [
            'product_id' => ['required', Rule::exists('products', 'id')->where('company_id', $companyId)],
            'type' => ['required', Rule::in(['entry', 'exit', 'adjustment'])],
            'quantity' => ['required', 'numeric', 'min:0.001', 'max:999999999.999'],
            'reason' => ['nullable', 'string', 'max:255'],
        ];
    }
}
