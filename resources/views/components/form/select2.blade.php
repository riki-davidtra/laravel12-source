@props([
    'label' => '',
    'name',
    'options' => [],
    'selected' => [],
    'multiple' => false,
])

@php
    $fieldId = $attributes->get('id') ?? Str::slug($name, '_');
    $hasError = $errors->has($name);

    $baseClass = 'dark:bg-dark-900 shadow-theme-xs h-11 w-full appearance-none rounded-lg bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30';
    $defaultBorderClass = 'border border-gray-300 focus:border-brand-300 focus:ring-brand-500/10 dark:border-gray-700 dark:focus:border-brand-800';
    $errorBorderClass = 'border border-error-300 focus:border-error-300 focus:ring-error-500/10 dark:border-error-700 dark:focus:border-error-800';
    $fieldClass = $baseClass . ' ' . ($hasError ? $errorBorderClass : $defaultBorderClass);
    if ($attributes->get('readonly') || $attributes->get('disabled')) {
        $fieldClass .= ' cursor-not-allowed';
    }

    $fieldName = $multiple ? $name . '[]' : $name;
@endphp

<div class="space-y-1">
    <label for="{{ $fieldId }}" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
        {{ $label }}
    </label>

    <div class="relative z-20">
        <select id="{{ $fieldId }}" name="{{ $fieldName }}" {{ $attributes->merge(['class' => 'select2 ' . $fieldClass]) }} @if ($multiple) multiple @endif>
            @foreach ($options as $key => $value)
                <option value="{{ $key }}" @selected(is_array($selected) ? in_array($key, $selected) : $selected == $key)>
                    {{ $value }}
                </option>
            @endforeach
        </select>
    </div>

    @error($name)
        <div class="mt-1 text-sm text-red-600 dark:text-red-400">
            {{ $message }}
        </div>
    @enderror
</div>
