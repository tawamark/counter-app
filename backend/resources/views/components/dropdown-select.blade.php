@props([
    'name',
    'id' => null,
    'label' => null,
    'options' => [],
    'selected' => '',
    'class' => '',
])

@php
    $fieldId = $id ?? $name;
    $selectedValue = (string) $selected;
    $normalizedOptions = collect($options)->map(fn ($optionLabel, $optionValue) => [
        'value' => (string) $optionValue,
        'label' => (string) $optionLabel,
    ])->values();
    $selectedOption = $normalizedOptions->firstWhere('value', $selectedValue);
    $selectedLabel = $selectedOption['label'] ?? ($normalizedOptions->first()['label'] ?? 'Selecione');
@endphp

<div class="{{ $class }}">
    @if ($label)
        <label for="{{ $fieldId }}_button" class="mb-1 block text-sm font-medium">{{ $label }}</label>
    @endif

    <div x-data="{ open: false, value: @js($selectedValue), label: @js($selectedLabel), choose(value, label) { this.value = value; this.label = label; this.open = false; this.$nextTick(() => this.$refs.button.focus()); } }" x-on:keydown.escape.window="open = false" class="relative">
        <input id="{{ $fieldId }}" name="{{ $name }}" type="hidden" x-bind:value="value">

        <button id="{{ $fieldId }}_button" type="button" x-ref="button" x-on:click="open = ! open" x-bind:aria-expanded="open.toString()" class="counter-option-trigger" aria-haspopup="listbox">
            <span class="truncate" x-text="label"></span>
            <i data-lucide="chevron-down" class="size-4 shrink-0 text-[#6f6f6f] transition" x-bind:class="open ? 'rotate-180' : ''"></i>
        </button>

        <div x-cloak x-show="open" x-on:click.outside="open = false" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="-translate-y-1 opacity-0" x-transition:enter-end="translate-y-0 opacity-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="translate-y-0 opacity-100" x-transition:leave-end="-translate-y-1 opacity-0" class="counter-option-menu" role="listbox" aria-labelledby="{{ $fieldId }}_button">
            @foreach ($normalizedOptions as $option)
                <button type="button" x-on:click="choose(@js($option['value']), @js($option['label']))" x-bind:class="value === @js($option['value']) ? 'counter-option-item-selected' : ''" class="counter-option-item" role="option" x-bind:aria-selected="(value === @js($option['value'])).toString()">
                    <span class="truncate">{{ $option['label'] }}</span>
                    <i data-lucide="check" x-show="value === @js($option['value'])" class="size-4 shrink-0"></i>
                </button>
            @endforeach
        </div>
    </div>
</div>
