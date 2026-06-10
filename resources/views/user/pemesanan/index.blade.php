@extends('layouts.app')
@section('title', 'Pemesanan Saya')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-8">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-[#18213a]">Pemesanan Saya</h1>
        <p class="mt-1 text-sm text-[#7a8499]">
            Riwayat dan status semua pemesanan Anda.
        </p>
    </div>

    {{-- Filter Tab --}}
    @php
    $tabs = [
        ''                          => 'Semua',
        'pending'                   => 'Menunggu Bayar',
        'menunggu_konfirmasi_admin' => 'Menunggu Konfirmasi',
        'dikonfirmasi'              => 'Dikonfirmasi',
        'selesai'                   => 'Selesai',
        'dibatalkan'                => 'Dibatalkan',
    ];
    @endphp
    <div class="mb-4 flex gap-1.5 overflow-x-auto pb-1">
        @foreach($tabs as $val => $label)
            <a href="{{ route('pemesanan.index', array_filter(['status' => $val])) }}"
               class="whitespace-nowrap rounded-full border px-3 py-1 text-xs font-medium
                      transition-colors
                      {{ request('status', '') === $val
                          ? 'border-[#3b6fd4] bg-[#3b6fd4] text-white'
                          : 'border-[#e5e9f2] bg-white text-[#7a8499] hover:bg-[#f1f4fa]' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- List Pemesanan --}}
    @forelse($pemesanans as $p)
    <div class="mb-3 overflow-hidden rounded-2xl border border-[#e5e9f2] bg-white shadow-sm">

        {{-- Header --}}
        <div class="flex items-center justify-between border-b border-[#e5e9f2] px-4 py-3">
            <div class="flex items-center gap-2">
                <span class="font-mono text-xs text-[#7a8499]">#{{ $p->id }}</span>

                {{-- Badge durasi sewa --}}
                @if($p->adalah12Jam())
                    <span class="inline-flex items-center gap-1 rounded-full bg-[#eef2fb]
                                 px-2 py-0.5 text-[11px] font-medium text-[#3b6fd4]">
                        <x-icon name="clock" class="w-3 h-3" />
                        12 Jam
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 rounded-full bg-[#f1f4fa]
                                 px-2 py-0.5 text-[11px] font-medium text-[#7a8499]">
                        <x-icon name="calendar" class="w-3 h-3" />
                        Harian
                    </span>
                @endif

                <x-status-badge :status="$p->status">
                    {{ $p->labelStatus() }}
                </x-status-badge>
            </div>
            <span class="text-xs text-[#7a8499]">
                {{ $p->created_at->format('d M Y') }}
            </span>
        </div>

        {{-- Body --}}
        <div class="flex items-start gap-4 px-4 py-3">
            <div class="grid h-12 w-12 flex-shrink-0 place-items-center rounded-xl bg-[#eef2fb]">
                <x-icon name="car" class="w-6 h-6 text-[#3b6fd4]" />
            </div>
            <div class="min-w-0 flex-1">
                <p class="font-semibold text-[#18213a]">{{ $p->mobil->nama }}</p>

                {{-- Info waktu --}}
                @if($p->adalah12Jam())
                    <p class="text-sm text-[#7a8499]">
                        {{ $p->tanggal_mulai->format('d M Y') }}
                        @if($p->waktu_mulai)
                            &middot; Mulai pukul {{ substr($p->waktu_mulai, 0, 5) }}
                        @endif
                        &middot; 1 sesi 12 jam
                    </p>
                @else
                    <p class="text-sm text-[#7a8499]">
                        {{ $p->tanggal_mulai->format('d M Y') }}
                        &ndash; {{ $p->tanggal_selesai->format('d M Y') }}
                        &middot; {{ $p->durasi() }} hari
                    </p>
                @endif

                <p class="text-xs text-[#7a8499]">
                    {{ $p->opsi_supir ? 'Dengan Supir' : 'Self-Drive' }}
                </p>
            </div>
            <div class="flex-shrink-0 text-right">
                <p class="text-base font-bold text-[#18213a]">
                    Rp {{ number_format($p->total_harga, 0, ',', '.') }}
                </p>
            </div>
        </div>

        {{-- Footer Aksi --}}
        <div class="flex items-center justify-between border-t border-[#e5e9f2] px-4 py-2.5">
            <div class="flex flex-wrap gap-2">

                {{-- Bayar --}}
                @if($p->status === 'pending')
                    <a href="{{ route('payment.checkout', $p) }}"
                       class="inline-flex items-center gap-1.5 rounded-lg bg-[#3b6fd4]
                              px-3 py-1.5 text-xs font-medium text-white
                              hover:bg-[#2e5bb8] transition-colors">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/>
                        </svg>
                        Bayar via WA
                    </a>

                    <form method="POST"
                          action="{{ route('pemesanan.cancel', $p) }}"
                          x-data>
                        @csrf @method('PATCH')
                        <button type="submit"
                                @click.prevent="$dispatch('open-modal-cancel-{{ $p->id }}')"
                                class="inline-flex items-center gap-1.5 rounded-lg border
                                       border-[#e5e9f2] px-3 py-1.5 text-xs font-medium
                                       text-[#7a8499] hover:bg-[#f1f4fa] transition-colors">
                            <x-icon name="x-circle" class="w-3.5 h-3.5" />
                            Batalkan
                        </button>
                    </form>
                @endif

                {{-- Invoice --}}
                @if(in_array($p->status, ['menunggu_konfirmasi_admin','dikonfirmasi','selesai']))
                    <a href="{{ route('payment.invoice', $p) }}"
                       class="inline-flex items-center gap-1.5 rounded-lg border border-[#e5e9f2]
                              px-3 py-1.5 text-xs font-medium text-[#7a8499]
                              hover:bg-[#f1f4fa] transition-colors">
                        <x-icon name="download" class="w-3.5 h-3.5" />
                        Invoice
                    </a>
                @endif
            </div>

            <a href="{{ route('pemesanan.show', $p) }}"
               class="text-xs font-medium text-[#3b6fd4] hover:underline">
                Detail
            </a>
        </div>
    </div>

    {{-- Modal Konfirmasi Batal --}}
    <x-modal id="cancel-{{ $p->id }}" title="Batalkan Pemesanan" size="sm">
        <p class="text-sm text-gray-600">
            Yakin ingin membatalkan pemesanan
            <strong class="text-gray-900">{{ $p->mobil->nama }}</strong>
            pada {{ $p->tanggal_mulai->format('d M Y') }}?
        </p>
        <x-slot:footer>
            <button @click="$dispatch('close-modal-cancel-{{ $p->id }}')"
                    class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium
                           text-gray-700 hover:bg-gray-50 transition-colors">
                Kembali
            </button>
            <form method="POST" action="{{ route('pemesanan.cancel', $p) }}">
                @csrf @method('PATCH')
                <button type="submit"
                        class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium
                               text-white hover:bg-red-700 transition-colors">
                    Ya, Batalkan
                </button>
            </form>
        </x-slot:footer>
    </x-modal>

    @empty
        <x-empty-state
            icon="calendar"
            title="Belum ada pemesanan"
            description="Pemesanan Anda akan muncul di sini.">
            <x-slot:action>
                <a href="{{ route('home') }}"
                   class="inline-flex items-center gap-1.5 rounded-lg bg-[#3b6fd4]
                          px-4 py-2 text-sm font-medium text-white
                          hover:bg-[#2e5bb8] transition-colors">
                    <x-icon name="car" class="w-4 h-4" />
                    Lihat Katalog Mobil
                </a>
            </x-slot:action>
        </x-empty-state>
    @endforelse

    @if($pemesanans->hasPages())
        <div class="mt-4">{{ $pemesanans->links() }}</div>
    @endif
</div>
@endsection