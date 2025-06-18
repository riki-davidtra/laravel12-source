@props(['path', 'alt' => 'Thumbnail'])

@php
    $defaultUrl = asset('assets/images/no-image.jpg');
    $oriUrl = $defaultUrl;
    $thumbUrl = $defaultUrl;
    if ($path) {
        if (Storage::disk('public')->exists($path)) {
            $oriUrl = Storage::url($path);
        }
        if (Storage::disk('public')->exists('thumbs/' . $path)) {
            $thumbUrl = Storage::url('thumbs/' . $path);
        }
    }
@endphp

<a href="{{ $oriUrl }}" alt="{{ $alt }}" target="_blank">
    <img src="{{ $thumbUrl }}" alt="{{ $alt }}" {{ $attributes->merge(['class' => 'w-12 h-12 object-cover mx-auto rounded']) }} />
</a>
