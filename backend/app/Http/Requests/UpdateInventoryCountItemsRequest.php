<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInventoryCountItemsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $inventoryCount = $this->route('inventoryCount');
        $itemIds = $inventoryCount->items()->pluck('id')->all();

        return [
            'items' => ['required', 'array'],
            'items.*.id' => ['required', 'integer', Rule::in($itemIds)],
            'items.*.counted_quantity' => ['nullable', 'numeric', 'min:0', 'max:999999999.999'],
        ];
    }

    public function itemsById(): array
    {
        return collect($this->validated('items'))->keyBy('id')->all();
    }
}
