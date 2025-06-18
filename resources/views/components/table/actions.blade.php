<div class="flex items-center gap-1">
    @isset($permissionBase)
        @can($permissionBase . ' index')
            @isset($showUrl)
                <x-table.buttons.show :url="$showUrl" />
            @endisset
        @endcan
        @can($permissionBase . ' edit')
            @isset($editUrl)
                <x-table.buttons.edit :url="$editUrl" />
            @endisset
        @endcan
        @can($permissionBase . ' delete')
            @isset($deleteUrl)
                <x-table.buttons.delete :url="$deleteUrl" />
            @endisset
        @endcan
    @endisset
</div>
