@extends('layouts.app')
@section('title', 'Pembayaran')

@section('content')
<div class="mx-auto max-w-xl px-4 py-8">

    <div class="mb-6 text-center">
        <div class="mx-auto mb-3 grid h-12 w-12 place-items-center rounded-2xl bg-[#eef2fb]">
            <x-icon name="shield" class="w-6 h-6 text-[#3b6fd4]" />
        </div>
        <h1 class="text-xl font-bold text-[#18213a]">Selesaikan Pembayaran</h1>
        <p class="mt-1 text-sm text-[#7a8499]">
            Selesaikan dalam <span class="font-semibold text-[#18213a]">24 jam</span>
            atau pemesanan otomatis dibatalkan.
        </p>
    </div>

    {{-- Ringkasan Pesanan --}}
    <div class="mb-4 rounded-2xl border border-[#e5e9f2] bg-white p-5 shadow-sm">
        <h3 class="text-sm font-semibold text-[#18213a] mb-3">Ringkasan Pesanan</h3>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-[#7a8499]">ID Pemesanan</span>
                <span class="font-mono font-medium text-[#18213a]">#{{ $pemesanan->id }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-[#7a8499]">Kendaraan</span>
                <span class="font-medium text-[#18213a]">{{ $pemesanan->mobil->nama }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-[#7a8499]">Periode</span>
                <span class="text-[#18213a]">
                    {{ $pemesanan->tanggal_mulai->format('d M') }}
                    &ndash; {{ $pemesanan->tanggal_selesai->format('d M Y') }}
                </span>
            </div>
            <div class="flex justify-between">
                <span class="text-[#7a8499]">Durasi</span>
                <span class="text-[#18213a]">{{ $pemesanan->durasi() }} hari</span>
            </div>
            <div class="flex justify-between">
                <span class="text-[#7a8499]">Opsi</span>
                <span class="text-[#18213a]">{{ $pemesanan->opsi_supir ? 'Dengan Supir' : 'Self-Drive' }}</span>
            </div>
            <div class="flex justify-between border-t border-[#e5e9f2] pt-2 font-semibold text-base">
                <span class="text-[#18213a]">Total</span>
                <span class="text-[#3b6fd4]">
                    Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}
                </span>
            </div>
        </div>
    </div>

    {{-- Bayar Button --}}
    <div class="rounded-2xl border border-[#e5e9f2] bg-white p-5 shadow-sm"
         x-data="checkoutPage({{ $pemesanan->id }})">

        <p class="mb-3 text-xs text-[#7a8499]">
            Tersedia: Transfer Bank, QRIS, GoPay, OVO, ShopeePay, Kartu Kredit
        </p>

        <button @click="bayar()" :disabled="loading"
                class="flex w-full items-center justify-center gap-2 rounded-xl bg-[#3b6fd4] py-3
                       text-sm font-semibold text-white hover:bg-[#2e5bb8] disabled:opacity-60
                       disabled:cursor-not-allowed transition-colors">
            <template x-if="!loading">
                <span class="flex items-center gap-2">
                    <x-icon name="shield" class="w-4 h-4" />
                    Bayar Sekarang
                </span>
            </template>
            <template x-if="loading">
                <span class="flex items-center gap-2">
                    <x-icon name="spinner" class="w-4 h-4 animate-spin" />
                    Memuat...
                </span>
            </template>
        </button>

        <p x-show="error" x-text="error" class="mt-2 text-center text-xs text-red-500" x-cloak></p>

        <p class="mt-3 text-center text-xs text-[#7a8499]">
            Transaksi diproses oleh
            <span class="font-semibold text-[#18213a]">Midtrans</span> — 100% aman.
        </p>
    </div>

    <a href="{{ route('pemesanan.index') }}"
       class="mt-3 flex w-full items-center justify-center text-xs text-[#7a8499] hover:text-[#18213a]">
        Kembali ke Pemesanan Saya
    </a>
</div>

@push('scripts')
<script src="{{ config('midtrans.snap_url') }}"
        data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
function checkoutPage(pemesananId) {
    return {
        loading: false,
        error: null,
        async bayar() {
            this.loading = true;
            this.error = null;
            try {
                const r = await fetch(`/pemesanan/${pemesananId}/snap-token`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                });
                const data = await r.json();
                if (!data.snap_token) throw new Error('Gagal mendapatkan token pembayaran.');
                this.loading = false;
                snap.pay(data.snap_token, {
                    onSuccess: () => window.location.href = '/pemesanan',
                    onPending: () => window.location.href = '/pemesanan',
                    onError: () => { this.error = 'Pembayaran gagal. Silakan coba lagi.'; },
                    onClose: () => { this.loading = false; }
                });
            } catch (e) {
                this.loading = false;
                this.error = e.message;
            }
        }
    }
}
</script>
@endpush
@endsection