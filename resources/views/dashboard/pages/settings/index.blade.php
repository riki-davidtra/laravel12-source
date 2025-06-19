@extends('dashboard.layouts.main')
@push('title', 'Settings')

@section('content')
    <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
        <x-breadcrumb title="Settings" :items="[['name' => 'Dashboard', 'url' => route('dashboard')], ['name' => 'Settings']]" />

        <div class="space-y-5 sm:space-y-6">
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="p-5 border-t border-gray-100 dark:border-gray-800 sm:p-6">
                    <form action="{{ route('settings.update_all') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        @foreach ($settings as $setting)
                            <div class="mb-4">
                                <div class="mb-2 font-semibold text-lg text-brand-400">{{ $setting->name }}</div>
                                <div class="flex flex-col space-y-2 px-4">
                                    @foreach ($setting->settingItems as $settingItem)
                                        <div class="flex flex-col md:flex-row md:items-start">
                                            <label for="{{ $settingItem->key }}" class="mb-1.5 md:w-1/5 text-base text-gray-700 dark:text-gray-300">{{ $settingItem->name }}</label>
                                            <div class="md:flex-1">
                                                @switch($settingItem->type)
                                                    @case('text')
                                                    @case('email')

                                                    @case('url')
                                                    @case('number')

                                                    @case('password')
                                                        <x-form.input type="{{ $settingItem->type }}" name="{{ $settingItem->key }}" value="{{ old($settingItem->key, $settingItem->value) }}" />
                                                    @break

                                                    @case('date')
                                                        <x-form.input type="date" name="{{ $settingItem->key }}" value="{{ old($settingItem->key, $settingItem->value) }}" />
                                                    @break

                                                    @case('file')
                                                        <div class="flex items-center gap-1">
                                                            <x-form.input type="file" name="{{ $settingItem->key }}" />
                                                            @if ($settingItem->value && Storage::disk('public')->exists($settingItem->value))
                                                                <a href="{{ Storage::url($settingItem->value) }}" target="_blank" class="px-4 py-3 text-sm text-white font-medium text-nowrap bg-blue-light-500 hover:bg-blue-light-600 focus:outline-none transition-colors rounded-lg">View file</a>
                                                            @endif
                                                        </div>
                                                    @break

                                                    @case('textarea')
                                                        <x-form.textarea name="{{ $settingItem->key }}" value="{{ old($settingItem->key, $settingItem->value) }}" rows="5" />
                                                    @break

                                                    @case('select')
                                                        @php
                                                            $itemList = [
                                                                1 => 'Option 1',
                                                                2 => 'Option 2',
                                                                3 => 'Option 3',
                                                            ];

                                                            $isSelect2 = str($settingItem->type)->contains('select2');
                                                            $isMultiple = str($settingItem->type)->contains('multiple');
                                                        @endphp

                                                        <x-form.select name="{{ $settingItem->key }}" :options="$itemList" :selected="old($settingItem->key, $settingItem->value)" :select2="$isSelect2" :multiple="$isMultiple" />
                                                    @break

                                                    @default
                                                        <x-form.input name="{{ $settingItem->key }}" value="{{ old($settingItem->key, $settingItem->value) }}" />
                                                @endswitch
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        @can('settings edit')
                            <div class="mt-8">
                                <x-form.button label="Save Changes" />
                            </div>
                        @endcan
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
@endpush

@push('scripts')
@endpush
