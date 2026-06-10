@extends('layouts.app')
@section('title', 'Detail Pemesanan #' . $pemesanan->id)

@section('content')
<div class="mx-auto max-w-3xl px-4 py-8">

    <div class="mb-6">
        <a href="{{ route('pemesanan.index') }}"
           class="inline-flex items-center gap-1.5 text-sm text-[#7a8499]
                  hover:text-[#18213a] transition-colors">
            <x-icon name="arrow-left" class="w-4 h-4" />
            Kembali ke Pemesanan Saya
        </a>
    </div>

    <div class="grid gap-4 lg:grid-cols-3">

        {{-- ── Detail Utama ────────────────────────────── --}}
        <div class="space-y-4 lg:col-span-2">

            {{-- Header Status --}}
            <div class="overflow-hidden rounded-2xl border border-[#e5e9f2] bg-white shadow-sm">
                <div class="flex items-start justify-between p-5">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-mono text-sm text-[#7a8499]">
                                #{{ $pemesanan->id }}
                            </span>
                            @if($pemesanan->adalah12Jam())
                                <span class="inline-flex items-center gap-1 rounded-full
                                             bg-[#eef2fb] px-2 py-0.5 text-[11px]
                                             font-medium text-[#3b6fd4]">
                                    <x-icon name="clock" class="w-3 h-3" />
                                    Sewa 12 Jam
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full
                                             bg-[#f1f4fa] px-2 py-0.5 text-[11px]
                                             font-medium text-[#7a8499]">
                                    <x-icon name="calendar" class="w-3 h-3" />
                                    Sewa Harian
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-[#7a8499]">
                            Dibuat {{ $pemesanan->created_at->diffForHumans() }}
                        </p>
                    </div>
                    <x-status-badge :status="$pemesanan->status">
                        {{ $pemesanan->labelStatus() }}
                    </x-status-badge>
                </div>
            </div>

            {{-- Info Kendaraan & Periode --}}
            <div class="rounded-2xl border border-[#e5e9f2] bg-white p-5 shadow-sm">
                <h3 class="mb-4 text-sm font-semibold text-[#18213a]">Detail Pemesanan</h3>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider text-[#7a8499]">
                            Kendaraan
                        </p>
                        <p class="mt-1 font-semibold text-[#18213a]">
                            {{ $pemesanan->mobil->nama }}
                        </p>
                        <p class="text-xs text-[#7a8499]">
                            {{ $pemesanan->mobil->plat_nomor }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider text-[#7a8499]">
                            Tipe Sewa
                        </p>
                        <p class="mt-1 font-semibold text-[#18213a]">
                            {{ $pemesanan->labelDurasi() }}
                        </p>
                        <p class="text-xs text-[#7a8499]">
                            {{ $pemesanan->opsi_supir ? 'Dengan Supir' : 'Self-Drive' }}
                        </p>
                    </div>

                    @if($pemesanan->adalah12Jam())
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-[#7a8499]">
                                Tanggal Sewa
                            </p>
                            <p class="mt-1 font-semibold text-[#18213a]">
                                {{ $pemesanan->tanggal_mulai->format('d M Y') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-[#7a8499]">
                                Waktu Mulai
                            </p>
                            <p class="mt-1 font-semibold text-[#18213a]">
                                {{ $pemesanan->waktu_mulai
                                    ? substr($pemesanan->waktu_mulai, 0, 5)
                                    : '—' }}
                            </p>
                            <p class="text-xs text-[#7a8499]">
                                Selesai 12 jam kemudian
                            </p>
                        </div>
                    @else
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-[#7a8499]">
                                Tanggal Mulai
                            </p>
                            <p class="mt-1 font-semibold text-[#18213a]">
                                {{ $pemesanan->tanggal_mulai->format('d M Y') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-[#7a8499]">
                                Tanggal Selesai
                            </p>
                            <p class="mt-1 font-semibold text-[#18213a]">
                                {{ $pemesanan->tanggal_selesai->format('d M Y') }}
                            </p>
                            <p class="text-xs text-[#7a8499]">
                                {{ $pemesanan->durasi() }} hari
                            </p>
                        </div>
                    @endif
                </div>

                @if($pemesanan->catatan)
                    <div class="mt-4 rounded-xl bg-[#f4f6fb] p-3">
                        <p class="text-xs font-medium text-[#7a8499]">Catatan</p>
                        <p class="mt-0.5 text-sm text-[#18213a]">{{ $pemesanan->catatan }}</p>
                    </div>
                @endif
            </div>

            {{-- Rincian Harga --}}
            <div class="rounded-2xl border border-[#e5e9f2] bg-white p-5 shadow-sm">
                <h3 class="mb-3 text-sm font-semibold text-[#18213a]">Rincian Biaya</h3>
                <div class="space-y-2 text-sm">

                    {{-- Baris sewa --}}
                    <div class="flex items-center justify-between text-[#7a8499]">
                        @if($pemesanan->adalah12Jam())
                            <span>
                                Sewa 12 Jam (1 sesi &times;
                                Rp {{ number_format($pemesanan->hargaPokok(), 0, ',', '.') }})
                            </span>
                            <span class="tabular-nums">
                                Rp {{ number_format($pemesanan->hargaPokok(), 0, ',', '.') }}
                            </span>
                        @else
                            <span>
                                Sewa {{ $pemesanan->durasi() }} hari &times;
                                Rp {{ number_format($pemesanan->hargaPokok(), 0, ',', '.') }}
                            </span>
                            <span class="tabular-nums">
                                Rp {{ number_format(
                                    $pemesanan->hargaPokok() * $pemesanan->durasi(),
                                    0, ',', '.'
                                ) }}
                            </span>
                        @endif
                    </div>

                    {{-- Baris supir --}}
                    @if($pemesanan->opsi_supir && $pemesanan->biaya_supir)
                        <div class="flex items-center justify-between text-[#7a8499]">
                            <span>
                                Jasa Supir
                                ({{ $pemesanan->adalah12Jam() ? '1 sesi' : $pemesanan->durasi() . ' hari' }})
                            </span>
                            <span class="tabular-nums">
                                Rp {{ number_format($pemesanan->biaya_supir, 0, ',', '.') }}
                            </span>
                        </div>
                    @endif

                    <div class="flex items-center justify-between border-t border-[#e5e9f2]
                                pt-2 font-semibold text-[#18213a]">
                        <span>Total</span>
                        <span class="text-base tabular-nums text-[#3b6fd4]">
                            Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Info Pembayaran --}}
            @if($pemesanan->payment)
            <div class="rounded-2xl border border-[#e5e9f2] bg-white p-5 shadow-sm">
                <h3 class="mb-3 text-sm font-semibold text-[#18213a]">Pembayaran</h3>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <p class="text-xs text-[#7a8499]">Metode</p>
                        <p class="mt-0.5 font-semibold text-[#18213a]">
                            {{ $pemesanan->payment->labelMetode() }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-[#7a8499]">Status Pembayaran</p>
                        <p class="mt-0.5 font-semibold text-[#18213a]">
                            {{ $pemesanan->payment->labelStatus() }}
                        </p>
                    </div>
                    @if($pemesanan->payment->wa_sent_at)
                    <div>
                        <p class="text-xs text-[#7a8499]">WA Dikirim</p>
                        <p class="mt-0.5 text-[#18213a]">
                            {{ $pemesanan->payment->wa_sent_at->format('d M Y, H:i') }}
                        </p>
                    </div>
                    @endif
                    @if($pemesanan->payment->paid_at)
                    <div>
                        <p class="text-xs text-[#7a8499]">Dikonfirmasi</p>
                        <p class="mt-0.5 text-[#18213a]">
                            {{ $pemesanan->payment->paid_at->format('d M Y, H:i') }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

        </div>

        {{-- ── Sidebar Aksi ─────────────────────────────── --}}
        <div class="space-y-4">

            {{-- Aksi Utama --}}
            <div class="rounded-2xl border border-[#e5e9f2] bg-white p-4 shadow-sm space-y-2">

                @if($pemesanan->status === 'pending')
                    <a href="{{ route('payment.checkout', $pemesanan) }}"
                       class="flex w-full items-center justify-center gap-2 rounded-xl
                              bg-[#3b6fd4] py-2.5 text-sm font-semibold text-white
                              hover:bg-[#2e5bb8] transition-colors">
                        <x-icon name="calendar" class="w-4 h-4" />
                        Lanjutkan Pembayaran
                    </a>
                    <form method="POST" action="{{ route('pemesanan.cancel', $pemesanan) }}"
                          x-data>
                        @csrf @method('PATCH')
                        <button type="button"
                                @click="$dispatch('open-modal-cancel-detail')"
                                class="flex w-full items-center justify-center gap-2 rounded-xl
                                       border border-[#e5e9f2] py-2.5 text-sm font-medium
                                       text-[#7a8499] hover:bg-[#f1f4fa] transition-colors">
                            <x-icon name="x-circle" class="w-4 h-4" />
                            Batalkan Pemesanan
                        </button>
                    </form>

                @elseif($pemesanan->status === 'menunggu_konfirmasi_admin')
                    <div class="flex items-start gap-2.5 rounded-xl bg-blue-50 p-3">
                        <x-icon name="clock" class="w-4 h-4 flex-shrink-0 mt-0.5 text-[#3b6fd4]" />
                        <p class="text-xs text-[#18213a]">
                            Pembayaran sudah diterima. Admin sedang memverifikasi pesanan Anda.
                        </p>
                    </div>
                    <a href="{{ 'https://wa.me/' . config('payment.wa_number') }}"
                       target="_blank"
                       class="flex w-full items-center justify-center gap-2 rounded-xl border
                              border-[#e5e9f2] py-2.5 text-sm font-medium text-[#7a8499]
                              hover:bg-[#f1f4fa] transition-colors">
                        <svg class="w-4 h-4 text-[#25D366]" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/>
                        </svg>
                        Chat Admin WhatsApp
                    </a>
                @endif

                {{-- Invoice --}}
                @if(in_array($pemesanan->status, ['menunggu_konfirmasi_admin','dikonfirmasi','selesai']))
                    <a href="{{ route('payment.invoice', $pemesanan) }}"
                       class="flex w-full items-center justify-center gap-2 rounded-xl
                              border border-[#e5e9f2] py-2.5 text-sm font-medium
                              text-[#7a8499] hover:bg-[#f1f4fa] transition-colors">
                        <x-icon name="download" class="w-4 h-4" />
                        Download Invoice PDF
                    </a>
                @endif
            </div>

            {{-- Timeline --}}
            <div class="rounded-2xl border border-[#e5e9f2] bg-white p-4 shadow-sm">
                <h3 class="mb-3 text-sm font-semibold text-[#18213a]">Timeline</h3>

                @php
                $timeline = [
                    [
                        'label'  => 'Pemesanan dibuat',
                        'waktu'  => $pemesanan->created_at->format('d M Y, H:i'),
                        'aktif'  => true,
                    ],
                    [
                        'label'  => 'Pembayaran via WhatsApp',
                        'waktu'  => $pemesanan->payment?->wa_sent_at?->format('d M Y, H:i'),
                        'aktif'  => ! is_null($pemesanan->payment?->wa_sent_at),
                    ],
                    [
                        'label'  => 'Pembayaran dikonfirmasi admin',
                        'waktu'  => $pemesanan->payment?->paid_at?->format('d M Y, H:i'),
                        'aktif'  => ! is_null($pemesanan->payment?->paid_at),
                    ],
                    [
                        'label'  => 'Pemesanan dikonfirmasi',
                        'waktu'  => null,
                        'aktif'  => in_array($pemesanan->status, ['dikonfirmasi','selesai']),
                    ],
                    [
                        'label'  => 'Selesai',
                        'waktu'  => null,
                        'aktif'  => $pemesanan->status === 'selesai',
                    ],
                ];
                @endphp

                <ol class="space-y-3">
                    @foreach($timeline as $i => $step)
                    <li class="flex gap-3">
                        <div class="flex flex-shrink-0 flex-col items-center">
                            <div class="h-2 w-2 rounded-full mt-1.5 flex-shrink-0
                                        {{ $step['aktif'] ? 'bg-[#3b6fd4]' : 'bg-gray-200' }}">
                            </div>
                            @if(! $loop->last)
                                <div class="w-px flex-1 mt-1
                                            {{ $step['aktif'] ? 'bg-[#3b6fd4]/30' : 'bg-gray-100' }}">
                                </div>
                            @endif
                        </div>
                        <div class="pb-3">
                            <p class="text-xs font-medium
                                      {{ $step['aktif'] ? 'text-[#18213a]' : 'text-gray-300' }}">
                                {{ $step['label'] }}
                            </p>
                            @if($step['waktu'])
                                <p class="text-[10px] text-[#7a8499]">{{ $step['waktu'] }}</p>
                            @endif
                        </div>
                    </li>
                    @endforeach
                </ol>
            </div>

        </div>
    </div>

    {{-- Modal Batal dari halaman detail --}}
    <x-modal id="cancel-detail" title="Batalkan Pemesanan" size="sm">
        <p class="text-sm text-gray-600">
            Yakin ingin membatalkan pemesanan
            <strong class="text-gray-900">{{ $pemesanan->mobil->nama }}</strong>
            ini?
        </p>
        <x-slot:footer>
            <button @click="$dispatch('close-modal-cancel-detail')"
                    class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium
                           text-gray-700 hover:bg-gray-50 transition-colors">
                Kembali
            </button>
            <form method="POST" action="{{ route('pemesanan.cancel', $pemesanan) }}">
                @csrf @method('PATCH')
                <button type="submit"
                        class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium
                               text-white hover:bg-red-700 transition-colors">
                    Ya, Batalkan
                </button>
            </form>
        </x-slot:footer>
    </x-modal>

</div>
@endsection