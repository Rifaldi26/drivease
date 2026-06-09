<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pemesanan extends Model
{
    protected $fillable = [
        'user_id',
        'mobil_id',
        'durasi_sewa',
        'tanggal_mulai',
        'waktu_mulai',
        'tanggal_selesai',
        'opsi_supir',
        'biaya_supir',
        'total_harga',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
        'opsi_supir'      => 'boolean',
        'biaya_supir'     => 'decimal:2',
        'total_harga'     => 'decimal:2',
    ];

    public function adalah12Jam(): bool
    {
        return $this->durasi_sewa === '12jam';
    }

    public function adalahHarian(): bool
    {
        return $this->durasi_sewa === 'harian';
    }

    public function durasi(): int
    {
        if ($this->adalah12Jam()) {
            return 1;
        }

        return $this->tanggal_mulai->diffInDays($this->tanggal_selesai);
    }

    public function labelDurasi(): string
    {
        if ($this->adalah12Jam()) {
            $waktu = $this->waktu_mulai
                ? ' · mulai ' . substr($this->waktu_mulai, 0, 5)
                : '';

            return '12 Jam' . $waktu;
        }

        $hari = $this->durasi();

        return $hari . ' Hari';
    }
    
    public function labelSatuan(): string
    {
        return $this->adalah12Jam() ? 'sesi' : 'hari';
    }

    /**
     * Harga pokok per unit (tanpa supir).
     */
    public function hargaPokok(): float
    {
        return $this->adalah12Jam()
            ? (float) ($this->mobil->harga_12jam ?? $this->mobil->harga_per_hari)
            : (float) $this->mobil->harga_per_hari;
    }

    // ── Status helpers ────────────────────────────────────
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isDikonfirmasi(): bool
    {
        return $this->status === 'dikonfirmasi';
    }

    public function isSelesai(): bool
    {
        return $this->status === 'selesai';
    }

    public function isBisaDibatalkan(): bool
    {
        return $this->status === 'pending';
    }

    public function labelStatus(): string
    {
        return match($this->status) {
            'pending'                   => 'Menunggu Pembayaran',
            'menunggu_konfirmasi_admin' => 'Menunggu Konfirmasi',
            'dikonfirmasi'              => 'Dikonfirmasi',
            'selesai'                   => 'Selesai',
            'dibatalkan'                => 'Dibatalkan',
            'kadaluarsa'                => 'Kadaluarsa',
            default                     => $this->status,
        };
    }

    public function warnaBadgeStatus(): string
    {
        return match($this->status) {
            'pending'                   => 'bg-yellow-100 text-yellow-800',
            'menunggu_konfirmasi_admin' => 'bg-blue-100 text-blue-800',
            'dikonfirmasi'              => 'bg-green-100 text-green-800',
            'selesai'                   => 'bg-gray-100 text-gray-800',
            'dibatalkan'                => 'bg-red-100 text-red-800',
            'kadaluarsa'                => 'bg-orange-100 text-orange-800',
            default                     => 'bg-gray-100 text-gray-800',
        };
    }

    // ── Relasi ────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mobil()
    {
        return $this->belongsTo(Mobil::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function journalEntries()
    {
        return $this->hasMany(JournalEntry::class);
    }

    public function pesans()
    {
        return $this->hasMany(Pesan::class);
    }

    // ── Scopes ────────────────────────────────────────────
    public function scopeAktif($query)
    {
        return $query->whereIn('status', [
            'pending',
            'menunggu_konfirmasi_admin',
            'dikonfirmasi',
        ]);
    }

    public function scopeBulan($query, int $bulan, int $tahun)
    {
        return $query
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun);
    }

    // ── Static: cek konflik tanggal ───────────────────────

    /**
     * Cek apakah ada pemesanan aktif yang bentrok pada rentang tanggal tertentu.
     *
     * Untuk sewa 12 jam: cek bentrok pada tanggal yang sama saja
     * (karena satu hari bisa 2 sesi: 06.00–18.00 dan 18.00–06.00 misalnya).
     * Namun untuk simplisitas, satu mobil hanya bisa satu pemesanan aktif per hari,
     * terlepas dari tipe durasi.
     *
     * @param  int         $mobilId
     * @param  string      $mulai       Format: Y-m-d
     * @param  string      $selesai     Format: Y-m-d (sama dengan mulai untuk 12 jam)
     * @param  int|null    $excludeId   Kecualikan ID pemesanan tertentu (untuk edit)
     */
    public static function adaKonflik(
        int $mobilId,
        string $mulai,
        string $selesai,
        ?int $excludeId = null
    ): bool {
        return static::where('mobil_id', $mobilId)
            ->whereIn('status', [
                'pending',
                'menunggu_konfirmasi_admin',
                'dikonfirmasi',
            ])
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->where(function ($q) use ($mulai, $selesai) {
                $q->whereBetween('tanggal_mulai', [$mulai, $selesai])
                  ->orWhereBetween('tanggal_selesai', [$mulai, $selesai])
                  ->orWhere(function ($q2) use ($mulai, $selesai) {
                      $q2->where('tanggal_mulai', '<=', $mulai)
                         ->where('tanggal_selesai', '>=', $selesai);
                  });
            })
            ->exists();
    }

    /**
     * Hitung total harga dari parameter — dipakai di controller sebelum simpan.
     *
     * @param  Mobil   $mobil
     * @param  string  $durasi      '12jam' | 'harian'
     * @param  int     $jumlahUnit  Jumlah hari (harian) atau 1 (12 jam)
     * @param  bool    $denganSupir
     */
    public static function hitungTotal(
        Mobil $mobil,
        string $durasi,
        int $jumlahUnit,
        bool $denganSupir = false
    ): array {
        $hargaPokok = match($durasi) {
            '12jam' => (float) ($mobil->harga_12jam ?? $mobil->harga_per_hari),
            default => (float) $mobil->harga_per_hari,
        };

        $biayaSupir = $denganSupir && $mobil->adaSupir()
            ? (float) $mobil->biaya_supir_per_hari
            : 0;

        $subtotalPokok = $hargaPokok * $jumlahUnit;
        $subtotalSupir = $biayaSupir * $jumlahUnit;
        $total         = $subtotalPokok + $subtotalSupir;

        return [
            'harga_pokok'     => $hargaPokok,
            'biaya_supir'     => $biayaSupir,
            'subtotal_pokok'  => $subtotalPokok,
            'subtotal_supir'  => $subtotalSupir,
            'total'           => $total,
        ];
    }
}