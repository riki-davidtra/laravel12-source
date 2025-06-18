@extends('dashboard.layouts.main')
@push('title', 'Add Role')

@section('content')
    <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
        <x-breadcrumb title="Add Role" :items="[['name' => 'Dashboard', 'url' => route('dashboard')], ['name' => 'Roles', 'url' => route('roles.index')], ['name' => 'Add Role']]" />

        <div class="space-y-5 sm:space-y-6">
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="p-5 border-t border-gray-100 dark:border-gray-800 sm:p-6">
                    <form action="{{ route('roles.store') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 spcace-y-4">
                            <x-form.select name="guard_name" label="Guard Name" :options="['web' => 'web', 'api' => 'api']" :selected="old('guard_name', $guard)" onchange="window.location.href='{{ route('roles.create') }}?guard=' + this.value" />
                            <x-form.input name="name" label="Role" placeholder="Enter role" value="{{ old('name') }}" />
                        </div>

                        <div class="space-y-1 my-6">
                            <label for="permissions" class="mb-1.5 block text-lg font-medium text-gray-700 dark:text-gray-400">
                                Permissions
                            </label>

                            @if ($permissions->isEmpty())
                                <div class="text-sm text-red-600 dark:text-red-400">
                                    No permission available. Please request permission first.
                                </div>
                            @else
                                <div class="flex items-center mb-2">
                                    <input type="checkbox" id="select_all" class="cursor-pointer form-checkbox text-indigo-600 rounded">
                                    <label for="select_all" class="cursor-pointer ml-2 text-sm font-semibold">Select all</label>
                                </div>

                                <div class="grid grid-cols-2 gap-2">
                                    @foreach ($permissions as $permission)
                                        <div class="flex items-center">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" id="perm_{{ $permission->id }}" class="cursor-pointer form-checkbox text-indigo-600 rounded permission-item" {{ in_array($permission->name, old('permissions', [])) ? 'checked' : '' }}>
                                            <label for="perm_{{ $permission->id }}" class="cursor-pointer ml-2 text-sm">{{ $permission->name }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('permissions')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            @endif
                        </div>

                        <div class="mt-8">
                            <x-form.button label="Save" />
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('select_all');
            const permissionCheckboxes = document.querySelectorAll('.permission-item');

            selectAll.addEventListener('change', function() {
                permissionCheckboxes.forEach(cb => cb.checked = this.checked);
            });

            permissionCheckboxes.forEach(cb => {
                cb.addEventListener('change', () => {
                    selectAll.checked = [...permissionCheckboxes].every(cb => cb.checked);
                });
            });
        });
    </script>
@endpush
