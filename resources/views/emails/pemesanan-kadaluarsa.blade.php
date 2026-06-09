@extends('emails.layout')

@section('content')

<p class="greeting">Halo, <strong>{{ $pemesanan->user->name }}</strong>!</p>

<p class="message">
    Pemesanan Anda di bawah ini telah <strong>kadaluarsa</strong> karena pembayaran tidak
    diselesaikan dalam batas waktu yang ditentukan.
</p>

{{-- Detail pemesanan --}}
<div class="card">
    <div class="card-row">
        <span class="label">ID Pemesanan</span>
        <span class="value">#{{ $pemesanan->id }}</span>
    </div>
    <div class="card-row">
        <span class="label">Kendaraan</span>
        <span class="value">{{ $pemesanan->mobil->nama }}</span>
    </div>
    <div class="card-row">
        <span class="label">Plat Nomor</span>
        <span class="value">{{ $pemesanan->mobil->plat_nomor }}</span>
    </div>
    <div class="card-row">
        <span class="label">Tanggal Sewa</span>
        <span class="value">
            {{ $pemesanan->tanggal_mulai->isoFormat('D MMM Y') }}
            &mdash;
            {{ $pemesanan->tanggal_selesai->isoFormat('D MMM Y') }}
        </span>
    </div>
    <div class="card-row">
        <span class="label">Total Harga</span>
        <span class="value">Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}</span>
    </div>
    <div class="card-row">
        <span class="label">Status</span>
        <span class="value">
            <span class="badge badge-danger">Kadaluarsa</span>
        </span>
    </div>
</div>

{{-- Info batas waktu --}}
<table style="width:100%; background:#FEF3C7; border-left:4px solid #D97706; border-radius:6px;
              padding:16px 20px; margin-bottom:24px; border-collapse:collapse;">
    <tr>
        <td style="width:32px; vertical-align:top; padding-top:2px;">
            {{-- SVG: clock icon --}}
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                 fill="none" stroke="#D97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12 6 12 12 16 14"/>
            </svg>
        </td>
        <td style="font-size:14px; color:#92400E; line-height:1.6; padding-left:10px;">
            Pembayaran harus diselesaikan dalam
            <strong>{{ config('rental.payment_expiry_hours', 24) }} jam</strong>
            sejak pemesanan dibuat.
            Pemesanan ini dibuat pada
            <strong>{{ $pemesanan->created_at->isoFormat('D MMM Y, HH:mm') }}</strong>
            dan telah melewati batas waktu tersebut.
        </td>
    </tr>
</table>

<p class="message">
    Kendaraan ini mungkin masih tersedia. Anda dapat membuat pemesanan baru kapan saja.
</p>

<a href="{{ route('mobil.index') }}" class="btn">Pesan Kembali</a>

<p style="font-size:13px; color:#6B7280; margin-top:16px;">
    Butuh bantuan atau ada pertanyaan? Hubungi kami melalui
    <a href="{{ route('chat.index') }}" style="color:#2E75B6;">Live Chat</a>.
</p>

@endsection