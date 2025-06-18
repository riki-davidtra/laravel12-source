@props([
    'isAll' => false,
    'value' => '',
    'name' => 'ids[]',
    'class' => 'row-checkbox',
])

<input type="checkbox" {{ $isAll ? 'id=select-all' : '' }} name="{{ $isAll ? '' : $name }}" value="{{ $isAll ? '' : $value }}" class="cursor-pointer {{ $class }} {{ $isAll ? '' : 'row-checkbox' }}">

@push('styles')
@endpush

@push('scripts')
    <script>
        $('#select-all').on('click', function() {
            $('.row-checkbox').prop('checked', this.checked);
        });
    </script>
@endpush
