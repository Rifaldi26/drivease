@extends('emails.layout')

@section('content')
<p class="greeting">Halo, <strong>{{ $pemesanan->user->name }}</strong>!</p>
<p class="message">
    ⏰ Pengingat: Sewa mobil Anda dimulai <strong>besok</strong>!
    Pastikan Anda sudah siap untuk pengambilan kendaraan.
</p>

<div class="card">
    <div class="card-row">
        <span class="label">ID Pemesanan</span>
        <span class="value">#{{ $pemesanan->id }}</span>
    </div>
    <div class="card-row">
        <span class="label">Mobil</span>
        <span class="value">{{ $pemesanan->mobil->nama }} — {{ $pemesanan->mobil->plat_nomor }}</span>
    </div>
    <div class="card-row">
        <span class="label">Tanggal Mulai</span>
        <span class="value">{{ $pemesanan->tanggal_mulai->format('d M Y') }}</span>
    </div>
    <div class="card-row">
        <span class="label">Tanggal Selesai</span>
        <span class="value">{{ $pemesanan->tanggal_selesai->format('d M Y') }}</span>
    </div>
    <div class="card-row">
        <span class="label">Opsi Supir</span>
        <span class="value">{{ $pemesanan->opsi_supir ? 'Dengan Supir' : 'Self-Drive' }}</span>
    </div>
</div>

<p class="message">Ada pertanyaan sebelum keberangkatan? Hubungi kami via chat.</p>
<a href="{{ config('app.url') }}/chat" class="btn">Chat dengan Admin</a>
@endsection