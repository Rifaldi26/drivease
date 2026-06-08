@extends('layouts.app')
@section('title', 'Syarat dan Ketentuan')

@section('content')
<div class="mx-auto max-w-4xl px-4 py-12">
    <div class="rounded-2xl bg-white p-8 shadow-sm border border-[#e5e9f2]">
        <h1 class="mb-6 text-center text-2xl font-bold text-[#18213a]">SYARAT DAN KETENTUAN PENGGUNAAN</h1>
        <p class="mt-2 text-center text-[#18213a]">Terakhir diperbarui : 8 Juni 2026</p>
        <div class="prose prose-sm max-w-none text-[#18213a]">
            
            <h3 class="font-semibold text-[#18213a] mt-4">1. Ketentuan Umum</h3>
            <ul class="list-disc pl-5 mt-2 space-y-2">
                <li>Penyewa menyetujui bahwa harga sewa kendaraan yang tertera pada aplikasi DriveEase <strong>tidak termasuk biaya Bahan Bakar Minyak (BBM)</strong>, tol, parkir, dan retribusi lainnya.</li>
                <li>Penyewa wajib mengembalikan kendaraan dengan <strong>kondisi BBM yang sama</strong> seperti saat awal serah terima.</li>
                <li>Penyewa <strong>bertanggung jawab penuh</strong> atas segala kerusakan, kehilangan, atau pelanggaran lalu lintas selama masa sewa kendaraan (untuk opsi <strong>Lepas Kunci</strong>).</li>
            </ul>

            <h3 class="font-semibold text-[#18213a] mt-8">2. Ketentuan Durasi dan Wilayah Pemakaian</h3>
            <ul class="list-disc pl-5 mt-2 space-y-2">
                <li><strong>Paket Sewa 12 Jam</strong> hanya berlaku pada <strong>hari kerja (Senin hingga Kamis)</strong> dan tidak berlaku pada akhir pekan (Jumat hingga Minggu) atau hari libur nasional. Area pemakaian dibatasi hanya untuk wilayah Banyumas, Purbalingga, Cilacap, Kebumen, Pemalang, dan Banjarnegara.</li>
                <li><strong>Paket Sewa 24 Jam</strong> berlaku dengan batas maksimal area pemakaian meliputi seluruh wilayah <strong>Jawa Tengah dan Daerah Istimewa Yogyakarta</strong>.</li>
                <li>Pemakaian kendaraan dengan tujuan ke <strong>luar provinsi</strong> atau di luar batas area yang telah ditentukan wajib mengambil durasi pemakaian <strong>minimal 2 (dua) hari</strong>.</li>
            </ul>

            <h3 class="font-semibold text-[#18213a] mt-8">3. Syarat Penyewaan Lepas Kunci</h3>
            <ul class="list-disc pl-5 mt-2 space-y-2">
                <li><strong>Penyewa Domisili Lokal (Purwokerto & Purbalingga):</strong> Wajib menyerahkan e-KTP asli. Kendaraan wajib diantar ke alamat rumah penyewa. Penyewa meminjamkan kendaraan roda dua (sepeda motor) beserta STNK asli kepada petugas kami untuk kemudahan mobilitas antar-jemput armada.</li>
                <li><strong>Karyawan / Mahasiswa Lokal:</strong> Wajib melampirkan e-KTP asli, beserta Kartu Tanda Mahasiswa (KTM) dan Kartu Rencana Studi (KRS) aktif bagi mahasiswa, atau ID Card / Kartu Nama / Surat Tugas untuk karyawan.</li>
                <li><strong>Penyewa Pendatang (Luar Kota):</strong> Wajib melampirkan e-KTP asli, KTM/KRS aktif atau ID Card karyawan, tiket kedatangan (Kereta/Bus/Pesawat), dan bukti pemesanan akomodasi (Booking Hotel).</li>
            </ul>

            <h3 class="font-semibold text-[#18213a] mt-8">4. Pengiriman dan Penjemputan Armada</h3>
            <ul class="list-disc pl-5 mt-2 space-y-2">
                <li>Untuk penyewa domisili lokal, pengiriman armada ke rumah <strong>tidak dikenakan biaya tambahan</strong> (syarat jaminan motor & STNK berlaku).</li>
                <li>Untuk penyewa luar kota, kendaraan dapat diantar dan dijemput di titik kedatangan (Stasiun Purwokerto, Terminal Bus Purwokerto/Purbalingga, atau Hotel) dengan <strong>biaya tambahan flat sebesar Rp 50.000</strong> per transaksi sewa.</li>
            </ul>
            
        </div>
    </div>
</div>
@endsection