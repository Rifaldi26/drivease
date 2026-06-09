<?php

namespace App\Jobs;

use App\Mail\PemesananDibatalkan;
use App\Mail\PemesananDibayar;
use App\Mail\PemesananDibuat;
use App\Mail\PemesananDikonfirmasi;
use App\Mail\PemesananDitolak;
use App\Mail\PemesananKadaluarsa;
use App\Mail\PemesananSelesai;
use App\Mail\PesananBaruAdmin;
use App\Models\Pemesanan;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Mengirim email notifikasi pemesanan berdasarkan event yang terjadi.
 *
 * Event yang didukung:
 *   dibuat | menunggu_konfirmasi | dikonfirmasi | ditolak |
 *   selesai | dibatalkan | kadaluarsa
 *
 * Setiap event dipetakan ke Mailable yang sesuai.
 * Event 'menunggu_konfirmasi' juga mengirim notifikasi ke seluruh admin.
 */
class KirimEmailPemesanan implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $backoff = 60;

    public function __construct(
        public readonly Pemesanan $pemesanan,
        public readonly string $event,
    ) {}

    // ── Handle ────────────────────────────────────────────────────────────

    public function handle(): void
    {
        $this->pemesanan->load(['user', 'mobil', 'payment']);

        $email = $this->pemesanan->user->email;

        try {
            match ($this->event) {
                'dibuat'              => Mail::to($email)->send(new PemesananDibuat($this->pemesanan)),
                'menunggu_konfirmasi' => $this->kirimMenungguKonfirmasi(),
                'dikonfirmasi'        => Mail::to($email)->send(new PemesananDikonfirmasi($this->pemesanan)),
                'ditolak'             => Mail::to($email)->send(new PemesananDitolak($this->pemesanan)),
                'selesai'             => Mail::to($email)->send(new PemesananSelesai($this->pemesanan)),
                'dibatalkan'          => Mail::to($email)->send(new PemesananDibatalkan($this->pemesanan)),
                'kadaluarsa'          => Mail::to($email)->send(new PemesananKadaluarsa($this->pemesanan)),
                default               => Log::warning(
                    "[KirimEmailPemesanan] Event tidak dikenal: '{$this->event}' "
                    . "(pemesanan #{$this->pemesanan->id})"
                ),
            };

            Log::info(sprintf(
                "[KirimEmailPemesanan] Event '%s' terkirim ke %s (pemesanan #%d).",
                $this->event,
                $email,
                $this->pemesanan->id,
            ));

        } catch (\Throwable $e) {
            Log::error(sprintf(
                "[KirimEmailPemesanan] Gagal kirim event '%s' ke %s: %s",
                $this->event,
                $email,
                $e->getMessage(),
            ));

            throw $e; // lempar ulang agar queue melakukan retry
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error(sprintf(
            "[KirimEmailPemesanan] Gagal permanen — pemesanan #%d event '%s': %s",
            $this->pemesanan->id,
            $this->event,
            $exception->getMessage(),
        ));
    }

    // ── Private ───────────────────────────────────────────────────────────

    /**
     * Kirim konfirmasi pembayaran ke pelanggan sekaligus
     * notifikasi pesanan baru ke semua admin.
     */
    private function kirimMenungguKonfirmasi(): void
    {
        Mail::to($this->pemesanan->user->email)
            ->send(new PemesananDibayar($this->pemesanan));

        User::where('role', 'admin')->each(
            fn (User $admin) => Mail::to($admin->email)
                ->send(new PesananBaruAdmin($this->pemesanan))
        );
    }
}