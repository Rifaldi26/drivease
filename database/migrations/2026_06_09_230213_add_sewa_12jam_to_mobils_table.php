<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mobils', function (Blueprint $table) {
            // Harga khusus 12 jam — NULL berarti tidak tersedia opsi 12 jam
            $table->decimal('harga_12jam', 10, 2)
                ->nullable()
                ->after('harga_per_hari')
                ->comment('Harga sewa 12 jam. NULL = tidak tersedia opsi 12 jam.');
        });
    }

    public function down(): void
    {
        Schema::table('mobils', function (Blueprint $table) {
            $table->dropColumn('harga_12jam');
        });
    }
};