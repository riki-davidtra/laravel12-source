@props([
    'columns' => [],
])

<div class="relative inline-block text-left" x-data="{ open: false }">
    <button type="button" @click="open = !open" class="inline-flex items-center px-3 py-1.5 bg-white text-gray-700 text-xs font-semibold rounded hover:bg-gray-50 transition border border-gray-300">
        Columns
        <svg class="-mr-1 ml-1 w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7l3-3 3 3m0 6l-3 3-3-3" />
        </svg>
    </button>
    <div x-show="open" @click.away="open = false" x-transition class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow bg-white ring-1 ring-black ring-opacity-5 z-10" style="display: none;">
        <div class="p-2">
            @foreach ($columns as $col)
                <label class="flex items-center space-x-2 mb-1">
                    <input type="checkbox" class="toggle-col cursor-pointer" data-column="{{ $col['index'] }}">
                    <span class="cursor-pointer text-sm font-semibold text-gray-700">{{ $col['label'] }}</span>
                </label>
            @endforeach
        </div>
    </div>
</div>

@push('styles')
@endpush

@push('scripts')
    <script>
        $(function() {
            $('.toggle-col').on('change', function() {
                let column = $('#dataTable').DataTable().column($(this).data('column'));
                column.visible(!column.visible());
            });
        });
    </script>
@endpush
