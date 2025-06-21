@extends('dashboard.layouts.main')
@push('title', 'Users')

@section('content')
    <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
        <x-breadcrumb title="Users" :items="[['name' => 'Dashboard', 'url' => route('dashboard')], ['name' => 'Users']]" />

        <div class="space-y-5 sm:space-y-6">
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="px-5 py-4 sm:px-6 sm:py-5 flex items-center justify-end gap-1">
                    @can('users delete')
                        <x-table.buttons.bulk-delete :url="route('users.bulk_delete')" />
                    @endcan
                    @can('users create')
                        <x-table.buttons.add :url="route('users.create')" />
                    @endcan
                    <x-table.buttons.columns :columns="[['index' => 6, 'label' => 'Created At'], ['index' => 7, 'label' => 'Updated At']]" />
                </div>
                <div class="p-5 border-t border-gray-100 dark:border-gray-800 sm:p-6">
                    <div class="overflow-x-auto">
                        <x-table.datatable :columns="[['text' => view('components.table.checkbox', ['isAll' => true])->render(), 'class' => 'w-0 text-center'], ['text' => 'No.', 'class' => 'w-0'], ['text' => 'Avatar'], ['text' => 'Name'], ['text' => 'Username'], ['text' => 'Email'], ['text' => 'Roles'], ['text' => 'Created At'], ['text' => 'Updated At'], ['text' => 'Actions', 'class' => 'text-center w-0']]" />
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
@endpush

@push('scripts')
    <script>
        $(function() {
            $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('users.index') }}",
                order: [],
                columns: [{
                    data: 'checkbox',
                    class: 'text-center',
                    orderable: false,
                    searchable: false
                }, {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    class: 'whitespace-nowrap',
                    orderable: false,
                    searchable: false
                }, {
                    data: 'avatar_url',
                    name: 'avatar_url',
                    class: 'text-center',
                    orderable: false,
                    searchable: false
                }, {
                    data: 'name',
                    name: 'name',
                    class: 'whitespace-nowrap'
                }, {
                    data: 'username',
                    name: 'username',
                    class: 'whitespace-nowrap'
                }, {
                    data: 'email',
                    name: 'email',
                    class: 'whitespace-nowrap'
                }, {
                    data: 'roles',
                    name: 'roles',
                    class: 'whitespace-nowrap'
                }, {
                    data: 'created_at',
                    name: 'created_at',
                    class: 'whitespace-nowrap',
                    visible: false
                }, {
                    data: 'updated_at',
                    name: 'updated_at',
                    class: 'whitespace-nowrap',
                    visible: false
                }, {
                    data: 'actions',
                    class: 'whitespace-nowrap text-center',
                    orderable: false,
                    searchable: false
                }]
            });
        });
    </script>
@endpush
