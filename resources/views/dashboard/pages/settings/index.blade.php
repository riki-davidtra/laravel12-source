@extends('dashboard.layouts.main')
@push('title', 'Settings')

@section('content')
    <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
        <x-breadcrumb title="Settings" :items="[['name' => 'Dashboard', 'url' => route('dashboard')], ['name' => 'Settings']]" />

        <div class="space-y-5 sm:space-y-6">
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="p-5 border-t border-gray-100 dark:border-gray-800 sm:p-6">
                    <form action="" method="POST" enctype="multipart/form-data">
                        @csrf
                        @foreach ($settings as $setting)
                            <div class="mb-4">
                                <div class="mb-2 font-semibold text-lg text-brand-400">{{ $setting->name }}</div>
                                <div class="flex flex-col space-y-2 px-4">
                                    @foreach ($setting->settingItems as $settingItem)
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                                            <label for="{{ $settingItem->key }}" class="text-base text-gray-700 dark:text-gray-300">{{ $settingItem->name }}</label>
                                            @switch($settingItem->type)
                                                @case('text')
                                                @case('email')

                                                @case('url')
                                                @case('number')

                                                @case('password')
                                                    <x-form.input type="{{ $settingItem->type }}" name="{{ $settingItem->key }}" value="{{ old($settingItem->key, $settingItem->value) }}" class="col-span-2" />
                                                @break

                                                @case('date')
                                                    <x-form.input type="date" name="{{ $settingItem->key }}" value="{{ old($settingItem->key, $settingItem->value) }}" class="col-span-2" />
                                                @break

                                                @case('file')
                                                    <x-form.input type="file" name="{{ $settingItem->key }}" class="col-span-2" />
                                                @break

                                                @case('textarea')
                                                    <x-form.textarea name="{{ $settingItem->key }}" value="{{ old($settingItem->key, $settingItem->value) }}" rows="5" class="col-span-2" />
                                                @break

                                                @default
                                                    {{-- fallback jika type tidak dikenali --}}
                                                    <x-form.input name="{{ $settingItem->key }}" value="{{ old($settingItem->key, $settingItem->value) }}" class="col-span-2" />
                                            @endswitch
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        <div class="mt-8">
                            <x-form.button label="Save Changes" />
                        </div>
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
