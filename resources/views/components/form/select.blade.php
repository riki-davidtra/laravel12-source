@props([
    'label' => '',
    'name',
    'options' => [],
    'placeholder' => 'Select an option',
    'selected' => '',
])

@php
    use Illuminate\Support\Str;

    $fieldId = $attributes->get('id') ?? Str::slug($name, '_');
    $hasError = $errors->has($name);

    $isReadOnly = $attributes->get('readonly');
    $isDisabled = $attributes->get('disabled');
    $isNonInteractive = $isReadOnly || $isDisabled;

    // Kelas dasar
    $baseClass = 'shadow-theme-xs h-11 w-full appearance-none rounded-lg px-4 py-2.5 pr-11 text-sm focus:ring-3 focus:outline-hidden';
    // Background tergantung kondisi
    $bgClass = $isNonInteractive ? 'bg-gray-100 dark:bg-gray-800' : 'bg-transparent dark:bg-gray-900';
    // Border tergantung error
    $borderClass = $hasError ? 'border border-error-300 focus:border-error-300 focus:ring-error-500/10 dark:border-error-700 dark:focus:border-error-800' : 'border border-gray-300 focus:border-brand-300 focus:ring-brand-500/10 dark:border-gray-700 dark:focus:border-brand-800';
    // Cursor jika readonly/disabled
    $nonInteractiveClass = $isNonInteractive ? 'cursor-not-allowed text-gray-600 dark:text-gray-400 placeholder:text-gray-600 dark:placeholder:text-white/90' : 'text-gray-800 dark:text-white/90 placeholder:text-gray-400 dark:placeholder:text-white/90';
    // Gabungkan semua class
    $fieldClass = implode(' ', [$baseClass, $bgClass, $borderClass, $nonInteractiveClass]);
@endphp

<div class="space-y-1">
    @if ($label)
        <label for="{{ $fieldId }}" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            {{ $label }}
        </label>
    @endif

    <div x-data="{ isOptionSelected: '{{ $selected }}' !== '' }" class="relative z-20">
        <select id="{{ $fieldId }}" name="{{ $name }}" {{ $attributes->merge(['class' => $fieldClass]) }} :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = $event.target.value !== ''">
            <option value="">{{ $placeholder }}</option>
            @foreach ($options as $key => $value)
                <option value="{{ $key }}" @selected($selected == $key) class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">{{ $value }}</option>
            @endforeach
        </select>

        <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-500 dark:text-gray-400">
            <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </span>
    </div>

    @error($name)
        <span class="text-theme-xs text-error-500">{{ $message }}</span>
    @enderror
</div>
