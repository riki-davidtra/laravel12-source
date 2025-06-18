<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {!! $settingItems['meta']->value ?? '' !!}

    <title>@stack('title', 'Title') - {{ $settingItems['site_name']->value ?? 'Site Name' }}</title>

    @if ($settingItems['favicon']->value && Storage::disk('public')->exists($settingItems['favicon']->value))
        <link rel="shortcut icon" type="image/x-icon" href="{{ Storage::url($settingItems['favicon']->value) }}" rel="shortcut icon">
    @else
        <link rel="icon" href="{{ asset('/') }}assets/images/favicon.png" type="image/png" />
    @endif

    <link href="{{ asset('assets/select2/select2.min.css') }}" rel="stylesheet" />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    @stack('styles')
</head>

<body x-data="{ page: '{{ url()->current() }}', 'loaded': true, 'darkMode': false, 'stickyMenu': false, 'sidebarToggle': false, 'scrollTop': false, 'isProfileInfoModal': false, 'isProfileAddressModal': false }" x-init="darkMode = JSON.parse(localStorage.getItem('darkMode'));
$watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)));
sidebarToggle = JSON.parse(localStorage.getItem('sidebarToggle')) ?? false;
$watch('sidebarToggle', value => localStorage.setItem('sidebarToggle', JSON.stringify(value)));" :class="{ 'dark bg-gray-900': darkMode === true }">

    <!-- ===== Preloader Start ===== -->
    @include('dashboard.partials.preloader')
    <!-- ===== Preloader End ===== -->

    <!-- ===== Page Wrapper Start ===== -->
    <div class="flex h-screen overflow-hidden">
        <!-- ===== Sidebar Start ===== -->
        @include('dashboard.partials.sidebar')
        <!-- ===== Sidebar End ===== -->

        <!-- ===== Content Area Start ===== -->
        <div class="relative flex flex-col flex-1 overflow-x-hidden overflow-y-auto">
            <!-- Small Device Overlay Start -->
            @include('dashboard.partials.overlay')
            <!-- Small Device Overlay End -->

            <!-- ===== Header Start ===== -->
            @include('dashboard.partials.header')
            <!-- ===== Header End ===== -->

            <!-- ===== Main Content Start ===== -->
            <main>
                @yield('content')
            </main>
            <!-- ===== Main Content End ===== -->
        </div>
        <!-- ===== Content Area End ===== -->
    </div>
    <!-- ===== Page Wrapper End ===== -->

    <script src="{{ asset('/') }}assets/jquery/jquery-3.6.4.min.js"></script>
    <script src="{{ asset('assets/select2/select2.min.js') }}"></script>
    <script src="{{ asset('/') }}assets/sweetalert2/sweetalert2.all.min.js"></script>
    <script>
        const showToast = (icon, message) => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: icon,
                title: message,
                showCloseButton: true,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.parentElement.style.zIndex = '1000000';
                }
            });
        };

        @if (session('success'))
            showToast('success', '{{ session('success') }}');
        @endif

        @if (session('error'))
            showToast('error', '{{ session('error') }}');
        @endif
    </script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: 'Select an option',
                width: '100%'
            });
        });
    </script>
    @stack('scripts')
</body>

</html>
