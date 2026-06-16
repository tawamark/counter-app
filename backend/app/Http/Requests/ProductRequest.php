<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $companyId = $this->user()->company_id;
        $product = $this->route('product');

        return [
            'category_id' => ['nullable', Rule::exists('categories', 'id')->where('company_id', $companyId)],
            'supplier_id' => ['nullable', Rule::exists('suppliers', 'id')->where('company_id', $companyId)],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sku' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products')->where('company_id', $companyId)->ignore($product),
            ],
            'barcode' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products')->where('company_id', $companyId)->ignore($product),
            ],
            'unit' => ['required', 'string', 'max:20'],
            'cost_price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'sale_price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'current_quantity' => ['required', 'numeric', 'min:0', 'max:999999999.999'],
        ];
    }
}
