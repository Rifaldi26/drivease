<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pemesanan extends Model
{
    protected $fillable = [
        'user_id',
        'mobil_id',
        'tanggal_mulai',
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

    // ── Status constants ──────────────────────────────────────────────────

    const STATUS_PENDING                    = 'pending';
    const STATUS_MENUNGGU_KONFIRMASI_ADMIN  = 'menunggu_konfirmasi_admin';
    const STATUS_DIKONFIRMASI               = 'dikonfirmasi';
    const STATUS_SELESAI                    = 'selesai';
    const STATUS_DIBATALKAN                 = 'dibatalkan';
    const STATUS_KADALUARSA                 = 'kadaluarsa';

    /** Status yang dianggap "terminal" — tidak bisa berubah lagi. */
    const STATUS_TERMINAL = [
        self::STATUS_SELESAI,
        self::STATUS_DIBATALKAN,
        self::STATUS_KADALUARSA,
    ];

    /** Status yang dianggap "aktif" — masih dalam proses. */
    const STATUS_AKTIF = [
        self::STATUS_PENDING,
        self::STATUS_MENUNGGU_KONFIRMASI_ADMIN,
        self::STATUS_DIKONFIRMASI,
    ];

    // ── Helpers: status checks ────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isMenungguKonfirmasiAdmin(): bool
    {
        return $this->status === self::STATUS_MENUNGGU_KONFIRMASI_ADMIN;
    }

    public function isDikonfirmasi(): bool
    {
        return $this->status === self::STATUS_DIKONFIRMASI;
    }

    public function isSelesai(): bool
    {
        return $this->status === self::STATUS_SELESAI;
    }

    public function isDibatalkan(): bool
    {
        return $this->status === self::STATUS_DIBATALKAN;
    }

    public function isKadaluarsa(): bool
    {
        return $this->status === self::STATUS_KADALUARSA;
    }

    public function isTerminal(): bool
    {
        return in_array($this->status, self::STATUS_TERMINAL, strict: true);
    }

    public function isAktif(): bool
    {
        return in_array($this->status, self::STATUS_AKTIF, strict: true);
    }

    /**
     * Pemesanan hanya bisa dibatalkan oleh user selagi masih pending.
     * Status lain sudah terlalu jauh dalam alur atau sudah terminal.
     */
    public function isBisaDibatalkan(): bool
    {
        return $this->isPending();
    }

    // ── Helpers: display ──────────────────────────────────────────────────

    public function durasi(): int
    {
        return $this->tanggal_mulai->diffInDays($this->tanggal_selesai);
    }

    public function labelStatus(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING                   => 'Menunggu Pembayaran',
            self::STATUS_MENUNGGU_KONFIRMASI_ADMIN => 'Menunggu Konfirmasi',
            self::STATUS_DIKONFIRMASI              => 'Dikonfirmasi',
            self::STATUS_SELESAI                   => 'Selesai',
            self::STATUS_DIBATALKAN                => 'Dibatalkan',
            self::STATUS_KADALUARSA                => 'Kadaluarsa',
            default                                => $this->status,
        };
    }

    public function warnaBadgeStatus(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING                   => 'bg-yellow-100 text-yellow-800',
            self::STATUS_MENUNGGU_KONFIRMASI_ADMIN => 'bg-blue-100 text-blue-800',
            self::STATUS_DIKONFIRMASI              => 'bg-green-100 text-green-800',
            self::STATUS_SELESAI                   => 'bg-gray-100 text-gray-800',
            self::STATUS_DIBATALKAN                => 'bg-red-100 text-red-800',
            self::STATUS_KADALUARSA                => 'bg-orange-100 text-orange-800',
            default                                => 'bg-gray-100 text-gray-800',
        };
    }

    // ── Relasi ────────────────────────────────────────────────────────────

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

    // ── Scopes ────────────────────────────────────────────────────────────

    public function scopeAktif($query)
    {
        return $query->whereIn('status', self::STATUS_AKTIF);
    }

    public function scopeTerminal($query)
    {
        return $query->whereIn('status', self::STATUS_TERMINAL);
    }

    public function scopeBulan($query, int $bulan, int $tahun)
    {
        return $query->whereMonth('created_at', $bulan)
                     ->whereYear('created_at', $tahun);
    }

    // ── Static helpers ────────────────────────────────────────────────────

    /**
     * Cek apakah ada konflik tanggal untuk mobil tertentu.
     * Hanya mempertimbangkan pemesanan dengan status aktif.
     */
    public static function adaKonflik(
        int $mobilId,
        string $mulai,
        string $selesai,
        ?int $excludeId = null,
    ): bool {
        return static::where('mobil_id', $mobilId)
            ->whereIn('status', self::STATUS_AKTIF)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
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
}