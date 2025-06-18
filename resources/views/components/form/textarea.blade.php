@props([
    'label' => '',
    'name',
    'placeholder' => '',
    'value' => '',
])

@php
    use Illuminate\Support\Str;

    $fieldId = $attributes->get('id') ?? Str::slug($name, '_');
    $hasError = $errors->has($name);

    $isReadOnly = $attributes->get('readonly');
    $isDisabled = $attributes->get('disabled');
    $isNonInteractive = $isReadOnly || $isDisabled;

    // Kelas dasar
    $baseClass = 'shadow-theme-xs w-full rounded-lg px-4 py-2.5 text-sm text-gray-800 dark:text-white/90 focus:ring-3 focus:outline-hidden';
    // Background tergantung kondisi
    $bgClass = $isNonInteractive ? 'bg-gray-100 dark:bg-gray-800' : 'bg-transparent dark:bg-gray-900';
    // Border tergantung error
    $borderClass = $hasError ? 'border border-error-300 focus:border-error-300 focus:ring-error-500/10 dark:border-error-700 dark:focus:border-error-800' : 'border border-gray-300 focus:border-brand-300 focus:ring-brand-500/10 dark:border-gray-700 dark:focus:border-brand-800';
    // Cursor jika readonly/disabled
    $nonInteractiveClass = $isNonInteractive ? 'cursor-not-allowed text-gray-600 dark:text-gray-400 placeholder:text-gray-600 dark:placeholder:text-white/90' : 'text-gray-800 dark:text-white/90 placeholder:text-gray-400 dark:placeholder:text-white/90';
    // Gabungkan semua
    $fieldClass = implode(' ', [$baseClass, $bgClass, $borderClass, $nonInteractiveClass]);
@endphp

@if ($label)
    <div class="space-y-1">
        <label for="{{ $fieldId }}" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            {{ $label }}
        </label>
@endif

<textarea id="{{ $fieldId }}" name="{{ $name }}" placeholder="{{ $placeholder }}" {{ $attributes->merge(['class' => $fieldClass]) }}>{!! old($name, $value) !!}</textarea>

@error($name)
    <span class="text-theme-xs text-error-500">
        {{ $message }}
    </span>
@enderror

@if ($label)
    </div>
@endif
