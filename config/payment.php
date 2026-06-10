<?php

/**
 * Konfigurasi sistem pembayaran DriveEase.
 *
 * Semua nilai sensitif (nomor WA, rekening bank) harus diisi
 * melalui environment variable — jangan hardcode di sini.
 *
 * Lihat .env.example untuk daftar lengkap variabel yang dibutuhkan.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Nomor WhatsApp Admin
    |--------------------------------------------------------------------------
    | Format internasional tanpa tanda + atau spasi.
    | Contoh: 628123456789 (Indonesia)
    */
    'wa_number' => env('PAYMENT_WA_NUMBER', '6281234567890'),

    /*
    |--------------------------------------------------------------------------
    | Metode Pembayaran yang Tersedia
    |--------------------------------------------------------------------------
    | Key digunakan sebagai nilai form, label ditampilkan di UI.
    | Tambah atau hapus metode di sini tanpa mengubah controller.
    */
    'metode' => [

        'cash' => [
            'label'     => 'Cash',
            'deskripsi' => 'Bayar tunai saat pengambilan kendaraan',
            'icon'      => 'cash',
            'instruksi' => 'Siapkan uang tunai sejumlah total tagihan saat pengambilan kendaraan di lokasi kami.',
        ],

        'transfer' => [
            'label'     => 'Transfer Bank',
            'deskripsi' => 'Transfer ke rekening ' . env('PAYMENT_TRANSFER_BANK', 'BCA'),
            'icon'      => 'bank',
            'bank'      => env('PAYMENT_TRANSFER_BANK', 'BCA'),
            'rekening'  => env('PAYMENT_TRANSFER_REKENING', '1234567890'),
            'atas_nama' => env('PAYMENT_TRANSFER_ATAS_NAMA', 'DriveEase Rental'),
            'instruksi' => 'Transfer ke rekening '
                . env('PAYMENT_TRANSFER_BANK', 'BCA') . ' '
                . env('PAYMENT_TRANSFER_REKENING', '1234567890')
                . ' a/n ' . env('PAYMENT_TRANSFER_ATAS_NAMA', 'DriveEase Rental')
                . ', lalu kirim bukti via WhatsApp.',
        ],

        'qris' => [
            'label'      => 'QRIS',
            'deskripsi'  => 'Scan QR Code dengan aplikasi apapun',
            'icon'       => 'qris',
            'qris_image' => env('PAYMENT_QRIS_IMAGE', 'payment/qris.png'),
            'instruksi'  => 'Scan QRIS dengan GoPay, OVO, Dana, ShopeePay, atau m-Banking, lalu kirim bukti via WhatsApp.',
        ],

        'edc' => [
            'label'     => 'EDC / Kartu',
            'deskripsi' => 'Debit atau kredit saat pengambilan',
            'icon'      => 'card',
            'instruksi' => 'Bayar menggunakan kartu debit atau kredit via mesin EDC saat pengambilan kendaraan.',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Template Pesan WhatsApp per Metode
    |--------------------------------------------------------------------------
    | Placeholder yang tersedia:
    |   {id}              — ID pemesanan
    |   {nama}            — Nama pelanggan
    |   {mobil}           — Nama kendaraan
    |   {tanggal_mulai}   — Tanggal mulai sewa
    |   {tanggal_selesai} — Tanggal selesai sewa
    |   {durasi}          — Durasi dalam hari
    |   {total}           — Total harga (diformat Rp)
    |   {bank}            — Nama bank (hanya transfer)
    |   {rekening}        — Nomor rekening (hanya transfer)
    |   {atas_nama}       — Nama pemilik rekening (hanya transfer)
    */
    'wa_template' => [

        'cash' => implode("\n", [
            'Halo Admin DriveEase,',
            '',
            'Saya ingin mengkonfirmasi pemesanan berikut:',
            '--------------------',
            'ID Pemesanan : #{id}',
            'Nama         : {nama}',
            'Kendaraan    : {mobil}',
            'Tanggal      : {tanggal_mulai} s/d {tanggal_selesai} ({durasi} hari)',
            'Total        : Rp {total}',
            '--------------------',
            'Metode Bayar : CASH',
            '',
            'Saya akan membayar tunai saat pengambilan kendaraan.',
            'Mohon konfirmasi pemesanan saya. Terima kasih.',
        ]),

        'transfer' => implode("\n", [
            'Halo Admin DriveEase,',
            '',
            'Saya ingin mengkonfirmasi pembayaran transfer:',
            '--------------------',
            'ID Pemesanan : #{id}',
            'Nama         : {nama}',
            'Kendaraan    : {mobil}',
            'Tanggal      : {tanggal_mulai} s/d {tanggal_selesai} ({durasi} hari)',
            'Total        : Rp {total}',
            '--------------------',
            'Metode Bayar : TRANSFER {bank}',
            'Rekening     : {rekening} a/n {atas_nama}',
            '',
            'Saya sudah melakukan transfer. Bukti transfer terlampir.',
            'Mohon konfirmasi pemesanan saya. Terima kasih.',
        ]),

        'qris' => implode("\n", [
            'Halo Admin DriveEase,',
            '',
            'Saya ingin mengkonfirmasi pembayaran QRIS:',
            '--------------------',
            'ID Pemesanan : #{id}',
            'Nama         : {nama}',
            'Kendaraan    : {mobil}',
            'Tanggal      : {tanggal_mulai} s/d {tanggal_selesai} ({durasi} hari)',
            'Total        : Rp {total}',
            '--------------------',
            'Metode Bayar : QRIS',
            '',
            'Saya sudah melakukan pembayaran via QRIS. Bukti pembayaran terlampir.',
            'Mohon konfirmasi pemesanan saya. Terima kasih.',
        ]),

        'edc' => implode("\n", [
            'Halo Admin DriveEase,',
            '',
            'Saya ingin mengkonfirmasi pemesanan berikut:',
            '--------------------',
            'ID Pemesanan : #{id}',
            'Nama         : {nama}',
            'Kendaraan    : {mobil}',
            'Tanggal      : {tanggal_mulai} s/d {tanggal_selesai} ({durasi} hari)',
            'Total        : Rp {total}',
            '--------------------',
            'Metode Bayar : EDC / KARTU',
            '',
            'Saya akan membayar via kartu saat pengambilan kendaraan.',
            'Mohon konfirmasi pemesanan saya. Terima kasih.',
        ]),

    ],

];