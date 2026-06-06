<header class="flex h-16 items-center gap-4 border-b border-gray-200 bg-white px-4 lg:px-6">

    {{-- Hamburger (mobile) --}}
    <button
        @click="sidebarOpen = true"
        class="lg:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors"
        aria-label="Buka menu"
    >
        <x-icon name="menu" class="w-5 h-5" />
    </button>

    {{-- Page Title --}}
    <div class="flex-1 min-w-0">
        <h1 class="text-lg font-semibold text-gray-900 truncate">
            @yield('title', 'Dashboard')
        </h1>
        @hasSection('breadcrumb')
            <nav class="text-xs text-gray-500 mt-0.5">
                @yield('breadcrumb')
            </nav>
        @endif
    </div>

    {{-- Right Actions --}}
    <div class="flex items-center gap-2">

        {{-- Notifikasi --}}
        <div class="relative" x-data="{ open: false }">
            <button
                @click="open = !open"
                class="relative p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors"
            >
                <x-icon name="bell" class="w-5 h-5" />
                <span
                    id="admin-notif-badge"
                    data-route="{{ route('admin.notifikasi.unread-count') }}"
                    class="absolute top-1 right-1 hidden min-w-[16px] h-4 px-0.5 rounded-full
                           bg-red-500 text-white text-[10px] font-bold items-center justify-center"
                ></span>
            </button>

            {{-- Dropdown notifikasi --}}
            <div
                x-show="open"
                @click.away="open = false"
                x-transition
                class="absolute right-0 top-full mt-2 w-80 rounded-xl border border-gray-200
                       bg-white shadow-lg z-50"
                x-cloak
            >
                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                    <span class="text-sm font-semibold text-gray-900">Notifikasi</span>
                    <a href="{{ route('admin.notifikasi.index') }}"
                       class="text-xs text-blue-600 hover:underline">Lihat semua</a>
                </div>
                <div id="admin-notif-list" class="max-h-72 overflow-y-auto divide-y divide-gray-50">
                    <div class="px-4 py-8 text-center text-sm text-gray-400">Memuat...</div>
                </div>
            </div>
        </div>

        {{-- Profile Dropdown --}}
        <div class="relative" x-data="{ open: false }">
            <button
                @click="open = !open"
                class="flex items-center gap-2 rounded-lg px-2 py-1.5
                       hover:bg-gray-100 transition-colors"
            >
                <x-avatar :name="auth()->user()->name" size="sm" />
                <span class="hidden md:block text-sm font-medium text-gray-700">
                    {{ auth()->user()->name }}
                </span>
                <x-icon name="chevron-down" class="w-4 h-4 text-gray-400" />
            </button>

            <div
                x-show="open"
                @click.away="open = false"
                x-transition
                class="absolute right-0 top-full mt-2 w-48 rounded-xl border border-gray-200
                       bg-white shadow-lg z-50 py-1"
                x-cloak
            >
                <div class="px-4 py-2 border-b border-gray-100">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="flex w-full items-center gap-2 px-4 py-2 text-sm text-red-600
                               hover:bg-red-50 transition-colors">
                        <x-icon name="logout" class="w-4 h-4" />
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>