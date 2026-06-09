<?php

namespace App\Console\Commands;

use App\Jobs\KirimEmailPemesanan;
use App\Models\Notifikasi;
use App\Models\Pemesanan;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Mengubah status pemesanan 'pending' yang melewati batas waktu pembayaran
 * menjadi 'kadaluarsa', lalu mengirim notifikasi in-app dan email ke pelanggan.
 *
 * Batas waktu dikonfigurasi via config/rental.php → payment_expiry_hours.
 * Interval pengecekan dikonfigurasi via config/rental.php → expiry_check_interval_minutes.
 *
 * Penggunaan:
 *   php artisan bookings:expire
 *   php artisan bookings:expire --dry-run
 */
class ExpireStaleBookings extends Command
{
    protected $signature = 'bookings:expire
                            {--dry-run : Tampilkan pemesanan yang akan dikadaluarsakan tanpa mengubah data}';

    protected $description = 'Tandai pemesanan pending yang melewati batas waktu pembayaran sebagai kadaluarsa.';

    // ── Handle ────────────────────────────────────────────────────────────

    public function handle(): int
    {
        $isDryRun    = $this->option('dry-run');
        $expiryHours = (int) config('rental.payment_expiry_hours', 24);
        $cutoff      = Carbon::now()->subHours($expiryHours);

        $pemesanans = Pemesanan::with(['user', 'mobil'])
            ->where('status', Pemesanan::STATUS_PENDING)
            ->where('created_at', '<=', $cutoff)
            ->get();

        if ($pemesanans->isEmpty()) {
            $this->components->info('Tidak ada pemesanan yang perlu dikadaluarsakan.');
            return self::SUCCESS;
        }

        if ($isDryRun) {
            $this->components->warn('Mode dry-run aktif — tidak ada data yang diubah.');
        }

        $this->components->info(sprintf(
            '%d pemesanan ditemukan (batas waktu: %d jam, cutoff: %s).',
            $pemesanans->count(),
            $expiryHours,
            $cutoff->toDateTimeString(),
        ));

        $berhasil = 0;
        $gagal    = 0;

        foreach ($pemesanans as $pemesanan) {
            $this->line(sprintf(
                '  <fg=gray>—</> #%d  %s  (%s)  dibuat: %s',
                $pemesanan->id,
                $pemesanan->mobil->nama,
                $pemesanan->user->email,
                $pemesanan->created_at->toDateTimeString(),
            ));

            if ($isDryRun) {
                continue;
            }

            $success = $this->expirePemesanan($pemesanan);
            $success ? $berhasil++ : $gagal++;
        }

        $this->newLine();

        if ($isDryRun) {
            $this->components->info("Dry-run selesai. {$pemesanans->count()} pemesanan akan dikadaluarsakan.");
            return self::SUCCESS;
        }

        $this->components->info("Selesai. Berhasil: {$berhasil}, Gagal: {$gagal}.");

        return $gagal > 0 ? self::FAILURE : self::SUCCESS;
    }

    // ── Private ───────────────────────────────────────────────────────────

    /**
     * Expire satu pemesanan dalam satu transaksi database.
     * Update status, kirim notifikasi in-app, dan dispatch email ke queue.
     */
    private function expirePemesanan(Pemesanan $pemesanan): bool
    {
        try {
            DB::transaction(function () use ($pemesanan) {
                $pemesanan->update(['status' => Pemesanan::STATUS_KADALUARSA]);

                Notifikasi::kirim(
                    userId : $pemesanan->user_id,
                    judul  : 'Pemesanan Kadaluarsa',
                    pesan  : "Pemesanan #{$pemesanan->id} untuk {$pemesanan->mobil->nama} "
                             . 'telah kadaluarsa karena pembayaran tidak diselesaikan tepat waktu. '
                             . 'Anda dapat membuat pemesanan baru kapan saja.',
                    tipe   : 'warning',
                    link   : route('pemesanan.index'),
                );
            });

            // Dispatch email di luar transaksi agar tidak rollback jika queue gagal
            KirimEmailPemesanan::dispatch($pemesanan->fresh(['user', 'mobil']), 'kadaluarsa');

            Log::info(sprintf(
                '[ExpireBookings] Pemesanan #%d dikadaluarsakan (user: %s).',
                $pemesanan->id,
                $pemesanan->user->email,
            ));

            return true;

        } catch (\Throwable $e) {
            Log::error(sprintf(
                '[ExpireBookings] Gagal mengkadaluarsakan pemesanan #%d: %s',
                $pemesanan->id,
                $e->getMessage(),
            ));

            $this->components->error("Gagal mengkadaluarsakan pemesanan #{$pemesanan->id}.");

            return false;
        }
    }
}