<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Jobs\KirimEmailPemesanan;
use App\Models\Notifikasi;
use App\Models\Payment;
use App\Models\Pemesanan;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    // ── Halaman pilih metode pembayaran ──────────────────────────────────

    public function checkout(Pemesanan $pemesanan)
    {
        abort_if($pemesanan->user_id !== Auth::id(), 403);

        if (! $pemesanan->isPending()) {
            return redirect()->route('pemesanan.show', $pemesanan)
                ->with('info', 'Pemesanan ini tidak memerlukan pembayaran.');
        }

        $pemesanan->load(['mobil', 'user']);
        $metode = config('payment.metode');

        return view('user.payment.checkout', compact('pemesanan', 'metode'));
    }

    // ── Proses pilihan metode → simpan Payment → redirect WA ────────────

    public function pilihMetode(Request $request, Pemesanan $pemesanan)
    {
        abort_if($pemesanan->user_id !== Auth::id(), 403);

        $request->validate([
            'metode' => ['required', 'in:' . implode(',', array_keys(config('payment.metode', [])))],
        ]);

        if (! $pemesanan->isPending()) {
            return back()->with('error', 'Pemesanan sudah tidak dalam status pending.');
        }

        Payment::updateOrCreate(
            ['pemesanan_id' => $pemesanan->id],
            [
                'amount'     => $pemesanan->total_harga,
                'metode'     => $request->metode,
                'status'     => 'menunggu_konfirmasi',
                'wa_sent_at' => now(),
            ]
        );

        $pemesanan->update(['status' => Pemesanan::STATUS_MENUNGGU_KONFIRMASI_ADMIN]);

        // Notifikasi in-app → pelanggan
        Notifikasi::kirim(
            userId : Auth::id(),
            judul  : 'Menunggu Konfirmasi',
            pesan  : "Pemesanan #{$pemesanan->id} sedang menunggu konfirmasi admin "
                     . 'setelah Anda mengirim pesan WhatsApp.',
            tipe   : 'info',
            link   : route('pemesanan.show', $pemesanan),
        );

        // Notifikasi in-app → semua admin
        $pemesanan->load(['user', 'mobil', 'payment']);
        User::where('role', 'admin')->each(function (User $admin) use ($pemesanan) {
            Notifikasi::kirim(
                userId : $admin->id,
                judul  : 'Pesanan Baru via WhatsApp',
                pesan  : "Pemesanan #{$pemesanan->id} dari {$pemesanan->user->name} "
                         . "memilih metode {$pemesanan->payment->labelMetode()}. Cek WhatsApp.",
                tipe   : 'info',
                link   : route('admin.pemesanan.show', $pemesanan),
            );
        });

        // Email async
        KirimEmailPemesanan::dispatch($pemesanan->fresh(['user', 'mobil', 'payment']), 'menunggu_konfirmasi');

        return redirect()->away($this->buildWhatsAppUrl($pemesanan, $request->metode));
    }

    // ── Halaman konfirmasi setelah kembali dari WhatsApp ─────────────────

    public function setelahWa(Pemesanan $pemesanan)
    {
        abort_if($pemesanan->user_id !== Auth::id(), 403);
        $pemesanan->load(['mobil', 'payment']);

        return view('user.payment.setelah-wa', compact('pemesanan'));
    }

    // ── Download Invoice PDF ─────────────────────────────────────────────

    public function invoice(Pemesanan $pemesanan)
    {
        abort_if($pemesanan->user_id !== Auth::id(), 403);

        // Invoice hanya tersedia untuk pemesanan yang sudah melewati tahap pembayaran
        $statusDiizinkan = [
            Pemesanan::STATUS_MENUNGGU_KONFIRMASI_ADMIN,
            Pemesanan::STATUS_DIKONFIRMASI,
            Pemesanan::STATUS_SELESAI,
        ];

        abort_unless(in_array($pemesanan->status, $statusDiizinkan, strict: true), 403);

        $pemesanan->load(['mobil', 'user', 'payment']);

        $pdf = Pdf::loadView('pdf.invoice', compact('pemesanan'))->setPaper('a4');

        return $pdf->download("invoice-drivease-{$pemesanan->id}.pdf");
    }

    // ── Private ──────────────────────────────────────────────────────────

    /**
     * Bangun URL WhatsApp dengan teks pesan terisi otomatis.
     * Template dan nomor admin diambil dari config/payment.php.
     */
    private function buildWhatsAppUrl(Pemesanan $pemesanan, string $metode): string
    {
        $template = config("payment.wa_template.{$metode}", '');
        $config   = config("payment.metode.{$metode}", []);

        $pesan = strtr($template, [
            '{id}'              => $pemesanan->id,
            '{nama}'            => $pemesanan->user->name,
            '{mobil}'           => $pemesanan->mobil->nama,
            '{tanggal_mulai}'   => $pemesanan->tanggal_mulai->isoFormat('D MMM Y'),
            '{tanggal_selesai}' => $pemesanan->tanggal_selesai->isoFormat('D MMM Y'),
            '{durasi}'          => $pemesanan->durasi(),
            '{total}'           => number_format($pemesanan->total_harga, 0, ',', '.'),
            '{bank}'            => $config['bank']      ?? '',
            '{rekening}'        => $config['rekening']  ?? '',
            '{atas_nama}'       => $config['atas_nama'] ?? '',
        ]);

        return 'https://wa.me/' . config('payment.wa_number') . '?text=' . rawurlencode($pesan);
    }
}