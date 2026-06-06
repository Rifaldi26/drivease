@props(['collapsed' => false])

@php
$navItems = [
    [
        'route'  => 'admin.dashboard',
        'label'  => 'Dashboard',
        'icon'   => 'chart-bar',
        'active' => request()->routeIs('admin.dashboard'),
    ],
    [
        'route'  => 'admin.mobil.index',
        'label'  => 'Armada Mobil',
        'icon'   => 'car',
        'active' => request()->routeIs('admin.mobil.*'),
    ],
    [
        'route'  => 'admin.pemesanan.index',
        'label'  => 'Pemesanan',
        'icon'   => 'calendar',
        'active' => request()->routeIs('admin.pemesanan.*'),
        'badge'  => \App\Models\Pemesanan::where('status','menunggu_konfirmasi_admin')->count(),
    ],
    [
        'route'  => 'admin.user.index',
        'label'  => 'Pengguna',
        'icon'   => 'users',
        'active' => request()->routeIs('admin.user.*'),
    ],
    [
        'route'  => 'admin.laporan.index',
        'label'  => 'Laporan',
        'icon'   => 'trending-up',
        'active' => request()->routeIs('admin.laporan.*'),
    ],
    [
        'route'  => 'admin.akuntansi.index',
        'label'  => 'Akuntansi',
        'icon'   => 'book-open',
        'active' => request()->routeIs('admin.akuntansi.*'),
    ],
    [
        'route'  => 'admin.notifikasi.index',
        'label'  => 'Notifikasi',
        'icon'   => 'bell',
        'active' => request()->routeIs('admin.notifikasi.*'),
        'badge'  => auth()->user()->unreadNotifikasi(),
    ],
    [
        'route'  => 'admin.chat.index',
        'label'  => 'Chat',
        'icon'   => 'chat',
        'active' => request()->routeIs('admin.chat.*'),
        'badge'  => auth()->user()->unreadPesan(),
    ],
];
@endphp

{{-- Logo --}}
<div class="flex h-16 items-center px-4 border-b border-white/10">
    <div :class="sidebarCollapsed ? 'justify-center w-full' : 'gap-3'" class="flex items-center">
        <x-icon name="shield" class="w-8 h-8 text-blue-300 flex-shrink-0" />
        <span :class="sidebarCollapsed ? 'lg:hidden' : ''" class="text-white font-bold text-lg tracking-wide">
            DriveEase
        </span>
    </div>
</div>

{{-- Navigation --}}
<nav class="flex-1 overflow-y-auto py-4 px-2 space-y-1">
    @foreach($navItems as $item)
        
            href="{{ route($item['route']) }}"
            title="{{ $item['label'] }}"
            class="group relative flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors
                   {{ $item['active']
                       ? 'bg-white/15 text-white'
                       : 'text-blue-100 hover:bg-white/10 hover:text-white' }}"
        >
            <x-icon :name="$item['icon']" class="w-5 h-5 flex-shrink-0" />

            <span :class="sidebarCollapsed ? 'lg:hidden' : ''" class="flex-1 whitespace-nowrap">
                {{ $item['label'] }}
            </span>

            @if(!empty($item['badge']) && $item['badge'] > 0)
                <span
                    :class="sidebarCollapsed ? 'lg:absolute lg:top-1 lg:right-1 lg:w-4 lg:h-4 lg:text-[10px]' : ''"
                    class="inline-flex items-center justify-center min-w-[20px] h-5 px-1 rounded-full
                           bg-red-500 text-white text-xs font-bold"
                >
                    {{ $item['badge'] > 99 ? '99+' : $item['badge'] }}
                </span>
            @endif

            {{-- Tooltip saat collapsed --}}
            <span
                :class="sidebarCollapsed ? 'lg:flex' : 'hidden'"
                class="absolute left-full ml-2 hidden whitespace-nowrap rounded-md bg-gray-900
                       px-2 py-1 text-xs text-white shadow-lg z-50 pointer-events-none
                       group-hover:flex"
            >
                {{ $item['label'] }}
            </span>
        </a>
    @endforeach
</nav>

{{-- Collapse Toggle (desktop only) --}}
<div class="hidden lg:block border-t border-white/10 p-2">
    <button
        @click="sidebarCollapsed = !sidebarCollapsed"
        class="flex w-full items-center justify-center gap-2 rounded-lg px-3 py-2
               text-blue-200 hover:bg-white/10 hover:text-white transition-colors text-sm"
    >
        <x-icon name="chevron-left" class="w-4 h-4 transition-transform"
            ::class="sidebarCollapsed ? 'rotate-180' : ''" />
        <span :class="sidebarCollapsed ? 'lg:hidden' : ''" class="font-medium">Ciutkan</span>
    </button>
</div>

{{-- User Info --}}
<div class="border-t border-white/10 p-3">
    <div :class="sidebarCollapsed ? 'lg:justify-center' : 'gap-3'" class="flex items-center">
        <x-avatar :name="auth()->user()->name" size="sm" class="flex-shrink-0" />
        <div :class="sidebarCollapsed ? 'lg:hidden' : ''" class="min-w-0 flex-1">
            <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
            <p class="text-xs text-blue-300 truncate">Administrator</p>
        </div>
    </div>
</div>