<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'pemesanan_id',
        'midtrans_order_id',
        'midtrans_transaction_id',
        'amount',
        'payment_method',
        'status',
        'paid_at',
        'midtrans_payload',
    ];

    protected $casts = [
        'amount'           => 'decimal:2',
        'paid_at'          => 'datetime',
        'midtrans_payload' => 'array',
    ];

    // ── Helpers ───────────────────────────────────────────
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function labelMetode(): string
    {
        return match(true) {
            str_contains($this->payment_method ?? '', '_va') => 'Transfer Bank',
            $this->payment_method === 'qris'                 => 'QRIS',
            $this->payment_method === 'gopay'                => 'GoPay',
            $this->payment_method === 'shopeepay'            => 'ShopeePay',
            $this->payment_method === 'credit_card'          => 'Kartu Kredit',
            default                                          => $this->payment_method ?? '-',
        };
    }

    // ── Relasi ────────────────────────────────────────────
    public function pemesanan()
    {
        return $this->belongsTo(Pemesanan::class);
    }

    public function journalEntries()
    {
        return $this->hasMany(JournalEntry::class);
    }
}