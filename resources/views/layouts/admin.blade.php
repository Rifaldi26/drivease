<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — DriveEase Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="h-full font-inter antialiased" x-data="{ sidebarOpen: false, sidebarCollapsed: false }">

    <div class="flex h-full">

        {{-- ── Sidebar Overlay (mobile) ──────────────────── --}}
        <div
            x-show="sidebarOpen"
            x-transition:enter="transition-opacity ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="sidebarOpen = false"
            class="fixed inset-0 z-20 bg-black/50 lg:hidden"
            x-cloak
        ></div>

        {{-- ── Sidebar ────────────────────────────────────── --}}
        <aside
            :class="{
                'translate-x-0': sidebarOpen,
                '-translate-x-full': !sidebarOpen,
                'lg:w-64': !sidebarCollapsed,
                'lg:w-16': sidebarCollapsed
            }"
            class="fixed inset-y-0 left-0 z-30 flex w-64 flex-col bg-navy-900 transition-all duration-300
                   lg:static lg:translate-x-0 lg:z-auto"
            style="background-color: #1E3A5F;"
        >
            <x-admin-sidebar :collapsed="false" />
        </aside>

        {{-- ── Main Area ──────────────────────────────────── --}}
        <div class="flex flex-1 flex-col min-w-0 overflow-hidden">

            {{-- Top Bar --}}
            <x-admin-topbar />

            {{-- Page Content --}}
            <main class="flex-1 overflow-y-auto p-4 lg:p-6">

                {{-- Page Header --}}
                @hasSection('header')
                    <div class="mb-6">
                        @yield('header')
                    </div>
                @endif

                {{-- Flash Messages --}}
                @if(session('success'))
                    <x-alert type="success" class="mb-4" dismissible>
                        {{ session('success') }}
                    </x-alert>
                @endif
                @if(session('error'))
                    <x-alert type="error" class="mb-4" dismissible>
                        {{ session('error') }}
                    </x-alert>
                @endif
                @if(session('warning'))
                    <x-alert type="warning" class="mb-4" dismissible>
                        {{ session('warning') }}
                    </x-alert>
                @endif

                {{-- Main Slot --}}
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>