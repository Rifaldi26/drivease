<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mobil extends Model
{
    protected $fillable = [
        'nama',
        'merek',
        'tahun',
        'plat_nomor',
        'harga_per_hari',
        'harga_12jam',
        'biaya_supir_per_hari',
        'status',
        'foto',
        'deskripsi',
    ];

    protected $casts = [
        'harga_per_hari'       => 'decimal:2',
        'harga_12jam'          => 'decimal:2',
        'biaya_supir_per_hari' => 'decimal:2',
    ];

    // ── Helpers ───────────────────────────────────────────
    public function tersedia(): bool
    {
        return $this->status === 'tersedia';
    }

    public function adaSupir(): bool
    {
        return !is_null($this->biaya_supir_per_hari);
    }

    public function ada12Jam(): bool
    {
        return ! is_null($this->harga_12jam);
    }

    public function hargaUntuk(string $durasi, bool $denganSupir = false): float
    {
        $hargaPokok = match($durasi) {
            '12jam' => (float) ($this->harga_12jam ?? $this->harga_per_hari),
            default => (float) $this->harga_per_hari,
        };

        $hargaSupir = $denganSupir && $this->adaSupir()
            ? (float) $this->biaya_supir_per_hari
            : 0;

        return $hargaPokok + $hargaSupir;
    }

    public function hargaMulaiDari(): float
    {
        if ($this->ada12Jam()) {
            return min((float) $this->harga_12jam, (float) $this->harga_per_hari);
        }

        return (float) $this->harga_per_hari;
    }

    public function fotoUrl(): string
    {
        return $this->foto
            ? asset('storage/' . $this->foto)
            : asset('images/mobil-default.png');
    }

    // ── Relasi ────────────────────────────────────────────
    public function pemesanans()
    {
        return $this->hasMany(Pemesanan::class);
    }

    public function favorits()
    {
        return $this->hasMany(Favorit::class);
    }

    public function difavoritOleh(int $userId): bool
    {
        return $this->favorits()->where('user_id', $userId)->exists();
    }

    // ── Scope ─────────────────────────────────────────────
    public function scopeTersedia($query)
    {
        return $query->where('status', 'tersedia');
    }

    public function scopeDisewa($query)
    {
        return $query->where('status', 'disewa');
    }

    public function scopePerawatan($query)
    {
        return $query->where('status', 'perawatan');
    }
}