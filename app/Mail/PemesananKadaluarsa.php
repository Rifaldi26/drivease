<?php

namespace App\Mail;

use App\Models\Pemesanan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Email notifikasi bahwa pemesanan telah kadaluarsa
 * karena pembayaran tidak diselesaikan tepat waktu.
 */
class PemesananKadaluarsa extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Pemesanan $pemesanan) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Pemesanan #{$this->pemesanan->id} Kadaluarsa — DriveEase",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.pemesanan-kadaluarsa');
    }
}