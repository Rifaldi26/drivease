<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Jobs\KirimEmailPemesanan;
use App\Models\Mobil;
use App\Models\Notifikasi;
use App\Models\Pemesanan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PemesananController extends Controller
{
    // ── Index ─────────────────────────────────────────────
    public function index()
    {
        $pemesanans = Pemesanan::with(['mobil', 'payment'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('user.pemesanan.index', compact('pemesanans'));
    }

    // ── Create ────────────────────────────────────────────
    public function create(Request $request)
    {
        $mobil = Mobil::findOrFail($request->mobil_id);

        if (! $mobil->tersedia()) {
            return redirect()
                ->route('home')
                ->with('error', 'Mobil ini sedang tidak tersedia.');
        }

        return view('user.pemesanan.create', compact('mobil'));
    }

    // ── Store ─────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $this->validasiStore($request);

        $mobil       = Mobil::findOrFail($validated['mobil_id']);
        $durasi      = $validated['durasi_sewa'];
        $denganSupir = (bool) ($validated['opsi_supir'] ?? false);

        // Tentukan tanggal_selesai berdasarkan tipe durasi
        $tanggalMulai  = Carbon::parse($validated['tanggal_mulai']);
        $tanggalSelesai = $this->hitungTanggalSelesai(
            $tanggalMulai,
            $durasi,
            (int) ($validated['jumlah_hari'] ?? 1)
        );

        // Cek konflik pemesanan aktif
        if (Pemesanan::adaKonflik(
            $mobil->id,
            $tanggalMulai->toDateString(),
            $tanggalSelesai->toDateString()
        )) {
            return back()
                ->withInput()
                ->with('error', 'Mobil sudah dipesan pada rentang tanggal tersebut.');
        }

        // Hitung jumlah unit (1 untuk 12 jam, jumlah hari untuk harian)
        $jumlahUnit = $durasi === '12jam'
            ? 1
            : $tanggalMulai->diffInDays($tanggalSelesai);

        // Hitung total server-side — tidak bisa dimanipulasi client
        $kalkulasi = Pemesanan::hitungTotal(
            $mobil,
            $durasi,
            $jumlahUnit,
            $denganSupir
        );

        $pemesanan = Pemesanan::create([
            'user_id'         => Auth::id(),
            'mobil_id'        => $mobil->id,
            'durasi_sewa'     => $durasi,
            'tanggal_mulai'   => $tanggalMulai->toDateString(),
            'waktu_mulai'     => $durasi === '12jam' ? ($validated['waktu_mulai'] ?? null) : null,
            'tanggal_selesai' => $tanggalSelesai->toDateString(),
            'opsi_supir'      => $denganSupir,
            'biaya_supir'     => $kalkulasi['subtotal_supir'] > 0
                ? $kalkulasi['subtotal_supir']
                : null,
            'total_harga'     => $kalkulasi['total'],
            'status'          => 'pending',
            'catatan'         => $validated['catatan'] ?? null,
        ]);

        Notifikasi::kirim(
            Auth::id(),
            'Pemesanan Dibuat',
            "Pemesanan #{$pemesanan->id} untuk {$mobil->nama} berhasil dibuat. Silakan selesaikan pembayaran.",
            'info',
            route('payment.checkout', $pemesanan)
        );

        KirimEmailPemesanan::dispatch(
            $pemesanan->load(['user', 'mobil']),
            'dibuat'
        );

        return redirect()
            ->route('payment.checkout', $pemesanan)
            ->with('success', 'Pemesanan berhasil dibuat. Pilih metode pembayaran.');
    }

    // ── Show ──────────────────────────────────────────────
    public function show(Pemesanan $pemesanan)
    {
        abort_if($pemesanan->user_id !== Auth::id(), 403);

        $pemesanan->load(['mobil', 'payment', 'journalEntries']);

        return view('user.pemesanan.show', compact('pemesanan'));
    }

    // ── Cancel ────────────────────────────────────────────
    public function cancel(Pemesanan $pemesanan)
    {
        abort_if($pemesanan->user_id !== Auth::id(), 403);

        if (! $pemesanan->isBisaDibatalkan()) {
            return back()->with('error', 'Pemesanan ini tidak dapat dibatalkan.');
        }

        $pemesanan->update(['status' => 'dibatalkan']);

        Notifikasi::kirim(
            Auth::id(),
            'Pemesanan Dibatalkan',
            "Pemesanan #{$pemesanan->id} untuk {$pemesanan->mobil->nama} telah dibatalkan.",
            'warning',
            route('pemesanan.index')
        );

        KirimEmailPemesanan::dispatch(
            $pemesanan->load(['user', 'mobil']),
            'dibatalkan'
        );

        return redirect()
            ->route('pemesanan.index')
            ->with('success', 'Pemesanan berhasil dibatalkan.');
    }

    // ── Private helpers ───────────────────────────────────

    /**
     * Validasi request store pemesanan.
     * Aturan durasi_sewa menentukan field mana yang wajib diisi.
     */
    private function validasiStore(Request $request): array
    {
        $rules = [
            'mobil_id'    => 'required|exists:mobils,id',
            'durasi_sewa' => 'required|in:12jam,harian',
            'opsi_supir'  => 'boolean',
            'catatan'     => 'nullable|string|max:500',
        ];

        // Aturan kondisional berdasarkan tipe durasi
        if ($request->durasi_sewa === '12jam') {
            $rules['tanggal_mulai'] = 'required|date|after_or_equal:today';
            $rules['waktu_mulai']   = 'required|date_format:H:i';
        } else {
            $rules['tanggal_mulai']  = 'required|date|after_or_equal:today';
            $rules['tanggal_selesai']= 'required|date|after:tanggal_mulai';
            $rules['jumlah_hari']    = 'required|integer|min:1|max:365';
        }

        return $request->validate($rules, [
            'mobil_id.required'         => 'Mobil wajib dipilih.',
            'durasi_sewa.required'       => 'Tipe durasi sewa wajib dipilih.',
            'durasi_sewa.in'             => 'Tipe durasi tidak valid.',
            'tanggal_mulai.required'     => 'Tanggal mulai wajib diisi.',
            'tanggal_mulai.after_or_equal' => 'Tanggal mulai tidak boleh di masa lalu.',
            'tanggal_selesai.required'   => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.after'      => 'Tanggal selesai harus setelah tanggal mulai.',
            'waktu_mulai.required'       => 'Waktu mulai wajib diisi untuk sewa 12 jam.',
            'waktu_mulai.date_format'    => 'Format waktu tidak valid (HH:MM).',
            'jumlah_hari.min'            => 'Minimal sewa 1 hari.',
            'jumlah_hari.max'            => 'Maksimal sewa 365 hari.',
        ]);
    }

    /**
     * Hitung tanggal_selesai berdasarkan tipe durasi.
     *
     * - 12 jam  → tanggal_selesai = tanggal_mulai (same day)
     * - harian  → tanggal_selesai = tanggal_mulai + jumlah_hari
     */
    private function hitungTanggalSelesai(
        Carbon $tanggalMulai,
        string $durasi,
        int $jumlahHari
    ): Carbon {
        return match($durasi) {
            '12jam' => $tanggalMulai->copy(),
            default => $tanggalMulai->copy()->addDays($jumlahHari),
        };
    }
}