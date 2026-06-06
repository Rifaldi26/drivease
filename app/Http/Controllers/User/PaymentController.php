<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Jobs\KirimEmailPemesanan;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\Notifikasi;
use App\Models\Payment;
use App\Models\Pemesanan;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Midtrans\Config as MidtransConfig;
use Midtrans\Snap;
use Midtrans\Notification;

class PaymentController extends Controller
{
    public function __construct()
    {
        MidtransConfig::$serverKey    = config('midtrans.server_key');
        MidtransConfig::$isProduction = config('midtrans.is_production');
        MidtransConfig::$isSanitized  = true;
        MidtransConfig::$is3ds        = true;
    }

    // ── Halaman checkout ──────────────────────────────────────
    public function checkout(Pemesanan $pemesanan)
    {
        abort_if($pemesanan->user_id !== Auth::id(), 403);

        if (!in_array($pemesanan->status, ['pending'])) {
            return redirect()->route('pemesanan.show', $pemesanan)
                ->with('info', 'Pemesanan ini tidak memerlukan pembayaran.');
        }

        $pemesanan->load(['mobil', 'user']);

        return view('user.payment.checkout', compact('pemesanan'));
    }

    // ── Generate Snap Token (dipanggil via AJAX) ──────────────
    public function snapToken(Pemesanan $pemesanan)
    {
        abort_if($pemesanan->user_id !== Auth::id(), 403);

        // Idempotent: jika payment sudah ada dan pending, pakai yang lama
        $payment = $pemesanan->payment;

        if (!$payment || $payment->status === 'expired') {
            $orderId = 'ORDER-' . $pemesanan->id . '-' . time();

            $payment = Payment::updateOrCreate(
                ['pemesanan_id' => $pemesanan->id],
                [
                    'midtrans_order_id' => $orderId,
                    'amount'            => $pemesanan->total_harga,
                    'status'            => 'pending',
                ]
            );
        }

        $params = [
            'transaction_details' => [
                'order_id'     => $payment->midtrans_order_id,
                'gross_amount' => (int) $payment->amount,
            ],
            'customer_details' => [
                'first_name' => $pemesanan->user->name,
                'email'      => $pemesanan->user->email,
                'phone'      => $pemesanan->user->no_hp ?? '',
            ],
            'item_details' => $this->buildItemDetails($pemesanan),
            'expiry' => [
                'unit'     => 'hours',
                'duration' => 24,
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            return response()->json(['snap_token' => $snapToken]);
        } catch (\Exception $e) {
            Log::error('Midtrans Snap Token Error: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal membuat sesi pembayaran.'], 500);
        }
    }

    // ── Webhook Midtrans ──────────────────────────────────────
    public function webhook(Request $request)
    {
        MidtransConfig::$serverKey = config('midtrans.server_key');

        try {
            $notif       = new Notification();
            $orderId     = $notif->order_id;
            $statusCode  = $notif->status_code;
            $grossAmount = $notif->gross_amount;
            $transStatus = $notif->transaction_status;
            $fraudStatus = $notif->fraud_status ?? 'accept';

            // Verifikasi signature
            $signature = hash('sha512',
                $orderId . $statusCode . $grossAmount . config('midtrans.server_key')
            );

            if ($signature !== $notif->signature_key) {
                Log::warning('Midtrans webhook signature invalid: ' . $orderId);
                return response()->json(['message' => 'Invalid signature'], 403);
            }

            $payment = Payment::where('midtrans_order_id', $orderId)->first();

            if (!$payment) {
                Log::warning('Midtrans webhook: payment not found for order ' . $orderId);
                return response()->json(['message' => 'Payment not found'], 404);
            }

            // Idempotent: skip jika sudah diproses
            if ($payment->status === 'paid') {
                return response()->json(['message' => 'Already processed']);
            }

            // Simpan raw payload
            $payment->update(['midtrans_payload' => $request->all()]);

            if ($transStatus === 'capture' && $fraudStatus === 'accept') {
                $this->prosesSettlement($payment, $notif);
            } elseif ($transStatus === 'settlement') {
                $this->prosesSettlement($payment, $notif);
            } elseif (in_array($transStatus, ['cancel', 'deny', 'failure'])) {
                $this->prosesFailed($payment);
            } elseif ($transStatus === 'expire') {
                $this->prosesExpired($payment);
            }

            return response()->json(['message' => 'OK']);

        } catch (\Exception $e) {
            Log::error('Midtrans Webhook Error: ' . $e->getMessage());
            return response()->json(['message' => 'Server error'], 500);
        }
    }

    // ── Download invoice PDF ──────────────────────────────────
    public function invoice(Pemesanan $pemesanan)
    {
        abort_if($pemesanan->user_id !== Auth::id(), 403);
        abort_if(!$pemesanan->payment?->isPaid(), 403);

        $pemesanan->load(['mobil', 'user', 'payment']);

        $pdf = Pdf::loadView('pdf.invoice', compact('pemesanan'))
            ->setPaper('a4');

        return $pdf->download("invoice-drivease-{$pemesanan->id}.pdf");
    }

    // ── Private helpers ───────────────────────────────────────
    private function buildItemDetails(Pemesanan $pemesanan): array
    {
        $items = [];
        $durasi = $pemesanan->durasi();

        $items[] = [
            'id'       => 'SEWA-' . $pemesanan->mobil_id,
            'price'    => (int) $pemesanan->mobil->harga_per_hari,
            'quantity' => $durasi,
            'name'     => 'Sewa ' . $pemesanan->mobil->nama . ' (' . $durasi . ' hari)',
        ];

        if ($pemesanan->opsi_supir && $pemesanan->biaya_supir > 0) {
            $items[] = [
                'id'       => 'SUPIR-' . $pemesanan->mobil_id,
                'price'    => (int) $pemesanan->mobil->biaya_supir_per_hari,
                'quantity' => $durasi,
                'name'     => 'Jasa Supir (' . $durasi . ' hari)',
            ];
        }

        return $items;
    }

    private function prosesSettlement(Payment $payment, $notif): void
    {
        $payment->update([
            'status'                  => 'paid',
            'midtrans_transaction_id' => $notif->transaction_id,
            'payment_method'          => $notif->payment_type,
            'paid_at'                 => now(),
        ]);

        $pemesanan = $payment->pemesanan;
        $pemesanan->update(['status' => 'menunggu_konfirmasi_admin']);

        // Notifikasi ke pelanggan
        Notifikasi::kirim(
            $pemesanan->user_id,
            'Pembayaran Diterima',
            "Pembayaran untuk pemesanan #{$pemesanan->id} berhasil. Menunggu konfirmasi admin.",
            'success',
            route('pemesanan.show', $pemesanan)
        );

        // Notifikasi ke semua admin
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Notifikasi::kirim(
                $admin->id,
                'Pesanan Baru Masuk',
                "Pemesanan #{$pemesanan->id} dari {$pemesanan->user->name} sudah dibayar dan menunggu konfirmasi.",
                'info',
                route('admin.pemesanan.show', $pemesanan)
            );
        }

        // Email async
        KirimEmailPemesanan::dispatch($pemesanan, 'dibayar');

        Log::info("Midtrans settlement processed: order {$payment->midtrans_order_id}");
    }

    private function prosesFailed(Payment $payment): void
    {
        $payment->update(['status' => 'failed']);
        $payment->pemesanan->update(['status' => 'dibatalkan']);

        Log::info("Midtrans payment failed: order {$payment->midtrans_order_id}");
    }

    private function prosesExpired(Payment $payment): void
    {
        $payment->update(['status' => 'expired']);
        $payment->pemesanan->update(['status' => 'kadaluarsa']);

        Log::info("Midtrans payment expired: order {$payment->midtrans_order_id}");
    }
}