<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pemesanans', function (Blueprint $table) {
            // Tipe durasi sewa yang dipilih pelanggan
            $table->enum('durasi_sewa', ['12jam', 'harian'])
                ->default('harian')
                ->after('mobil_id')
                ->comment('Tipe durasi: 12jam = satu sesi 12 jam, harian = per hari penuh.');

            // Untuk sewa 12 jam: tanggal_selesai = tanggal_mulai, waktu_mulai diisi
            // Untuk sewa harian: kolom ini NULL
            $table->time('waktu_mulai')
                ->nullable()
                ->after('tanggal_mulai')
                ->comment('Waktu mulai untuk sewa 12 jam. NULL untuk sewa harian.');
        });
    }

    public function down(): void
    {
        Schema::table('pemesanans', function (Blueprint $table) {
            $table->dropColumn(['durasi_sewa', 'waktu_mulai']);
        });
    }
};