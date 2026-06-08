@extends('layouts.app')
@section('title', 'Pemberitahuan Privasi')

@section('content')
<div class="mx-auto max-w-4xl px-4 py-12">
    <div class="rounded-2xl bg-white p-8 shadow-sm border border-[#e5e9f2]">
        <h1 class="mb-6 text-center text-2xl font-bold text-[#18213a]">PEMBERITAHUAN PRIVASI</h1>
        <p class="mt-2 text-center">Terakhir diperbarui : 8 Juni 2026</p>
        <div class="prose prose-sm max-w-none text-[#18213a]">
            
            {{-- Bagian 1 --}}
            <h3 class="font-semibold text-[#18213a] mt-4">1. Pengumpulan Data Pribadi</h3>
            <p class="mt-2">Untuk menyediakan layanan penyewaan kendaraan yang aman dan terpercaya, kami mengumpulkan beberapa informasi pribadi Anda pada saat melakukan pemesanan, yang meliputi namun tidak terbatas pada:</p>
            <ul class="list-disc pl-5 mt-2 space-y-2">
                <li><strong>Informasi Identitas:</strong> Kartu Tanda Penduduk (e-KTP).</li>
                <li><strong>Informasi Pekerjaan/Pendidikan:</strong> Kartu Tanda Mahasiswa (KTM), Kartu Rencana Studi (KRS), ID Card Karyawan, atau Surat Tugas.</li>
                <li><strong>Informasi Perjalanan:</strong> Tiket kedatangan transportasi umum (Kereta/Bus) dan bukti pemesanan hotel (khusus untuk pengguna dari luar kota).</li>
                <li><strong>Informasi Kontak dan Lokasi:</strong> Nomor telepon aktif, alamat email, dan alamat pengiriman/penjemputan kendaraan.</li>
            </ul>
            
            {{-- Bagian 2 --}}
            <h3 class="font-semibold text-[#18213a] mt-8">2. Penggunaan Data Pribadi</h3>
            <p class="mt-2">
                Data pribadi yang Anda berikan akan digunakan secara khusus untuk keperluan berikut:
            </p>
            <ul class="list-disc pl-5 mt-2 space-y-2">
                <li><strong>Verifikasi Identitas:</strong> Memastikan keabsahan profil penyewa untuk memitigasi risiko penipuan atau penggelapan armada kendaraan.</li>
                <li><strong>Pelaksanaan Layanan:</strong> Memfasilitasi proses antar-jemput armada ke lokasi yang telah ditentukan.</li>
                <li><strong>Komunikasi Layanan:</strong> Menghubungi Anda terkait status pemesanan, konfirmasi pembayaran, dan bantuan darurat selama masa sewa.</li>
            </ul>

            {{-- Bagian 3 --}}
            <h3 class="font-semibold text-[#18213a] mt-8">3. Keamanan dan Penyimpanan Data</h3>
            <ul class="list-disc pl-5 mt-2 space-y-2">
                <li>Kami berkomitmen untuk melindungi dokumen jaminan fisik (seperti KTP atau STNK yang dititipkan) dan data digital Anda dengan standar keamanan yang ketat.</li>
                <li>Dokumen fisik akan dikembalikan kepada Anda segera setelah masa sewa berakhir dan kendaraan telah dikembalikan dalam kondisi baik.</li>
                <li>Kami tidak akan menjual, menyewakan, atau menukar data pribadi Anda kepada pihak ketiga untuk tujuan pemasaran tanpa persetujuan eksplisit dari Anda. Data hanya akan diserahkan kepada pihak berwajib (Kepolisian) apabila terjadi indikasi tindak pidana atau pelanggaran hukum selama masa sewa.</li>
            </ul>
            
        </div>
    </div>
</div>
@endsection