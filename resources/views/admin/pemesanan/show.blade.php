@extends('layouts.admin')
@section('title', 'Detail Pemesanan #' . $pemesanan->id)

@section('content')

<div class="mb-6">
    <a href="{{ route('admin.pemesanan.index') }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition-colors">
        <x-icon name="arrow-left" class="w-4 h-4" />
        Kembali ke Pemesanan
    </a>
</div>

<div class="grid gap-4 lg:grid-cols-3">

    {{-- Detail Utama --}}
    <div class="lg:col-span-2 space-y-4">

        {{-- Info Pemesanan --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h2 class="text-base font-semibold text-gray-900">
                        Pemesanan #{{ $pemesanan->id }}
                    </h2>
                    <p class="text-xs text-gray-400 mt-0.5">
                        Dibuat {{ $pemesanan->created_at->diffForHumans() }}
                    </p>
                </div>
                <x-status-badge :status="$pemesanan->status">
                    {{ $pemesanan->labelStatus() }}
                </x-status-badge>
            </div>

            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Pelanggan</p>
                    <p class="mt-1 font-medium text-gray-900">{{ $pemesanan->user->name }}</p>
                    <p class="text-xs text-gray-500">{{ $pemesanan->user->email }}</p>
                    <p class="text-xs text-gray-500">{{ $pemesanan->user->no_hp ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Kendaraan</p>
                    <p class="mt-1 font-medium text-gray-900">{{ $pemesanan->mobil->nama }}</p>
                    <p class="text-xs text-gray-500">{{ $pemesanan->mobil->plat_nomor }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Periode Sewa</p>
                    <p class="mt-1 font-medium text-gray-900">
                        {{ $pemesanan->tanggal_mulai->format('d M Y') }}
                    </p>
                    <p class="text-xs text-gray-500">
                        s/d {{ $pemesanan->tanggal_selesai->format('d M Y') }}
                        ({{ $pemesanan->durasi() }} hari)
                    </p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Opsi</p>
                    <p class="mt-1 font-medium text-gray-900">
                        {{ $pemesanan->opsi_supir ? 'Dengan Supir' : 'Self-Drive' }}
                    </p>
                </div>
            </div>

            @if($pemesanan->catatan)
                <div class="mt-4 rounded-lg bg-gray-50 p-3">
                    <p class="text-xs font-medium text-gray-500">Catatan pelanggan</p>
                    <p class="mt-1 text-sm text-gray-700">{{ $pemesanan->catatan }}</p>
                </div>
            @endif
        </div>

        {{-- Rincian Harga --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Rincian Harga</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between text-gray-600">
                    <span>Sewa mobil ({{ $pemesanan->durasi() }} hari × Rp
                        {{ number_format($pemesanan->mobil->harga_per_hari, 0, ',', '.') }})
                    </span>
                    <span class="tabular-nums">
                        Rp {{ number_format($pemesanan->durasi() * $pemesanan->mobil->harga_per_hari, 0, ',', '.') }}
                    </span>
                </div>
                @if($pemesanan->opsi_supir && $pemesanan->biaya_supir)
                    <div class="flex justify-between text-gray-600">
                        <span>Jasa supir ({{ $pemesanan->durasi() }} hari × Rp
                            {{ number_format($pemesanan->mobil->biaya_supir_per_hari, 0, ',', '.') }})
                        </span>
                        <span class="tabular-nums">
                            Rp {{ number_format($pemesanan->biaya_supir, 0, ',', '.') }}
                        </span>
                    </div>
                @endif
                <div class="flex justify-between border-t border-gray-100 pt-2 font-semibold text-gray-900">
                    <span>Total</span>
                    <span class="tabular-nums">
                        Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Info Payment --}}
        @if($pemesanan->payment)
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Pembayaran</h3>
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div>
                    <p class="text-xs text-gray-400">Order ID Midtrans</p>
                    <p class="mt-0.5 font-mono text-xs font-medium text-gray-900">
                        {{ $pemesanan->payment->midtrans_order_id }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Metode</p>
                    <p class="mt-0.5 font-medium text-gray-900">
                        {{ $pemesanan->payment->labelMetode() }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Status</p>
                    <x-status-badge :status="$pemesanan->payment->status" class="mt-0.5">
                        {{ ucfirst($pemesanan->payment->status) }}
                    </x-status-badge>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Waktu Bayar</p>
                    <p class="mt-0.5 text-gray-900">
                        {{ $pemesanan->payment->paid_at?->format('d M Y, H:i') ?? '-' }}
                    </p>
                </div>
            </div>
        </div>
        @endif

    </div>

    {{-- Sidebar Aksi --}}
    <div class="space-y-4">

        {{-- Aksi Admin --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Tindakan Admin</h3>

            @if($pemesanan->status === 'menunggu_konfirmasi_admin')
                <div class="space-y-2">
                    <form method="POST"
                          action="{{ route('admin.pemesanan.konfirmasi', $pemesanan) }}">
                        @csrf @method('PATCH')
                        <button type="submit"
                                class="flex w-full items-center justify-center gap-2 rounded-lg
                                       bg-green-600 px-4 py-2.5 text-sm font-medium text-white
                                       hover:bg-green-700 transition-colors">
                            <x-icon name="check-circle" class="w-4 h-4" />
                            Konfirmasi Pemesanan
                        </button>
                    </form>
                    <form method="POST"
                          action="{{ route('admin.pemesanan.tolak', $pemesanan) }}"
                          x-data
                          @submit.prevent="$dispatch('open-modal-tolak')">
                        <button type="button"
                                @click="$dispatch('open-modal-tolak')"
                                class="flex w-full items-center justify-center gap-2 rounded-lg
                                       border border-red-200 px-4 py-2.5 text-sm font-medium
                                       text-red-600 hover:bg-red-50 transition-colors">
                            <x-icon name="x-circle" class="w-4 h-4" />
                            Tolak Pemesanan
                        </button>
                    </form>
                </div>

            @elseif($pemesanan->status === 'dikonfirmasi')
                <form method="POST"
                      action="{{ route('admin.pemesanan.selesai', $pemesanan) }}">
                    @csrf @method('PATCH')
                    <button type="submit"
                            class="flex w-full items-center justify-center gap-2 rounded-lg
                                   bg-blue-600 px-4 py-2.5 text-sm font-medium text-white
                                   hover:bg-blue-700 transition-colors">
                        <x-icon name="check-circle" class="w-4 h-4" />
                        Tandai Selesai
                    </button>
                </form>

            @else
                <p class="text-xs text-gray-400 text-center py-2">
                    Tidak ada tindakan tersedia untuk status ini.
                </p>
            @endif

            {{-- Download Invoice --}}
            @if($pemesanan->payment?->isPaid())
                <a href="{{ route('admin.pemesanan.invoice', $pemesanan) }}"
                   class="mt-2 flex w-full items-center justify-center gap-2 rounded-lg
                          border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700
                          hover:bg-gray-50 transition-colors">
                    <x-icon name="download" class="w-4 h-4" />
                    Download Invoice
                </a>
            @endif
        </div>

        {{-- Timeline --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Timeline</h3>
            <ol class="space-y-3">
                <li class="flex gap-3">
                    <div class="flex flex-col items-center">
                        <div class="h-2 w-2 rounded-full bg-blue-600 mt-1.5 flex-shrink-0"></div>
                        <div class="w-px flex-1 bg-gray-200 mt-1"></div>
                    </div>
                    <div class="pb-3">
                        <p class="text-xs font-medium text-gray-900">Pemesanan dibuat</p>
                        <p class="text-xs text-gray-400">
                            {{ $pemesanan->created_at->format('d M Y, H:i') }}
                        </p>
                    </div>
                </li>
                @if($pemesanan->payment?->paid_at)
                <li class="flex gap-3">
                    <div class="flex flex-col items-center">
                        <div class="h-2 w-2 rounded-full bg-green-500 mt-1.5 flex-shrink-0"></div>
                        <div class="w-px flex-1 bg-gray-200 mt-1"></div>
                    </div>
                    <div class="pb-3">
                        <p class="text-xs font-medium text-gray-900">Pembayaran diterima</p>
                        <p class="text-xs text-gray-400">
                            {{ $pemesanan->payment->paid_at->format('d M Y, H:i') }}
                        </p>
                    </div>
                </li>
                @endif
                <li class="flex gap-3">
                    <div class="flex flex-col items-center">
                        <div class="h-2 w-2 rounded-full
                            {{ in_array($pemesanan->status, ['dikonfirmasi','selesai'])
                                ? 'bg-green-500' : 'bg-gray-200' }}
                            mt-1.5 flex-shrink-0"></div>
                    </div>
                    <div>
                        <p class="text-xs font-medium
                            {{ in_array($pemesanan->status, ['dikonfirmasi','selesai'])
                                ? 'text-gray-900' : 'text-gray-300' }}">
                            Dikonfirmasi admin
                        </p>
                    </div>
                </li>
            </ol>
        </div>

    </div>
</div>

{{-- Modal Tolak --}}
<x-modal id="tolak" title="Tolak Pemesanan" size="sm">
    <p class="text-sm text-gray-600">
        Yakin ingin menolak pemesanan <strong>#{{ $pemesanan->id }}</strong>?
        Pelanggan akan diberitahu melalui notifikasi dan email.
    </p>
    <x-slot:footer>
        <button @click="$dispatch('close-modal-tolak')"
                class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium
                       text-gray-700 hover:bg-gray-50 transition-colors">
            Batal
        </button>
        <form method="POST" action="{{ route('admin.pemesanan.tolak', $pemesanan) }}">
            @csrf @method('PATCH')
            <button type="submit"
                    class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white
                           hover:bg-red-700 transition-colors">
                Ya, Tolak
            </button>
        </form>
    </x-slot:footer>
</x-modal>

@endsection