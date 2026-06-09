<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mobil;
use App\Models\Pemesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MobilController extends Controller
{
    // ── Index ─────────────────────────────────────────────
    public function index()
    {
        $mobils = Mobil::latest()->paginate(12);

        return view('admin.mobil.index', compact('mobils'));
    }

    // ── Create ────────────────────────────────────────────
    public function create()
    {
        return view('admin.mobil.create');
    }

    // ── Store ─────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $this->validasi($request);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')
                ->store('mobil', 'public');
        }

        Mobil::create($validated);

        return redirect()
            ->route('admin.mobil.index')
            ->with('success', 'Mobil berhasil ditambahkan.');
    }

    // ── Edit ──────────────────────────────────────────────
    public function edit(Mobil $mobil)
    {
        return view('admin.mobil.edit', compact('mobil'));
    }

    // ── Update ────────────────────────────────────────────
    public function update(Request $request, Mobil $mobil)
    {
        $validated = $this->validasi($request, $mobil->id);

        if ($request->hasFile('foto')) {
            $this->hapusFotoLama($mobil);
            $validated['foto'] = $request->file('foto')
                ->store('mobil', 'public');
        }

        $mobil->update($validated);

        return redirect()
            ->route('admin.mobil.index')
            ->with('success', 'Data mobil berhasil diperbarui.');
    }

    // ── Destroy ───────────────────────────────────────────
    public function destroy(Mobil $mobil)
    {
        if ($this->punyaPemesananAktif($mobil)) {
            return back()->with(
                'error',
                'Mobil tidak dapat dihapus karena masih ada pemesanan aktif.'
            );
        }

        $this->hapusFotoLama($mobil);
        $mobil->delete();

        return redirect()
            ->route('admin.mobil.index')
            ->with('success', 'Mobil berhasil dihapus.');
    }

    // ── Toggle Status ─────────────────────────────────────
    public function toggleStatus(Mobil $mobil)
    {
        if ($mobil->status === 'disewa') {
            return back()->with(
                'error',
                'Status mobil yang sedang disewa tidak dapat diubah secara manual.'
            );
        }

        $statusBaru = $mobil->status === 'tersedia'
            ? 'perawatan'
            : 'tersedia';

        $mobil->update(['status' => $statusBaru]);

        return back()->with(
            'success',
            "Status mobil diubah menjadi {$statusBaru}."
        );
    }

    // ── Private helpers ───────────────────────────────────

    /**
     * Aturan validasi yang dipakai store dan update.
     * Pada update, kecualikan plat_nomor milik mobil itu sendiri.
     */
    private function validasi(Request $request, ?int $kecualiId = null): array
    {
        $platRule = 'required|string|max:20|unique:mobils,plat_nomor';

        if ($kecualiId) {
            $platRule .= ",{$kecualiId}";
        }

        $validated = $request->validate([
            'nama'                 => 'required|string|max:255',
            'merek'                => 'required|string|max:255',
            'tahun'                => 'required|integer|min:1990|max:' . (date('Y') + 1),
            'plat_nomor'           => $platRule,
            'harga_per_hari'       => 'required|numeric|min:0',
            'harga_12jam'          => 'nullable|numeric|min:0',
            'biaya_supir_per_hari' => 'nullable|numeric|min:0',
            'deskripsi'            => 'nullable|string|max:2000',
            'foto'                 => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'nama.required'           => 'Nama mobil wajib diisi.',
            'merek.required'          => 'Merek wajib diisi.',
            'tahun.required'          => 'Tahun wajib diisi.',
            'tahun.min'               => 'Tahun tidak boleh lebih awal dari 1990.',
            'plat_nomor.required'     => 'Plat nomor wajib diisi.',
            'plat_nomor.unique'       => 'Plat nomor sudah terdaftar.',
            'harga_per_hari.required' => 'Harga per hari wajib diisi.',
            'harga_per_hari.min'      => 'Harga tidak boleh negatif.',
            'harga_12jam.min'         => 'Harga 12 jam tidak boleh negatif.',
            'foto.image'              => 'File harus berupa gambar.',
            'foto.max'                => 'Ukuran foto maksimal 2MB.',
        ]);

        // Jika harga_12jam tidak diisi, pastikan NULL bukan string kosong
        $validated['harga_12jam']          = $validated['harga_12jam'] ?: null;
        $validated['biaya_supir_per_hari'] = $validated['biaya_supir_per_hari'] ?: null;

        return $validated;
    }

    /**
     * Cek apakah mobil masih punya pemesanan aktif.
     */
    private function punyaPemesananAktif(Mobil $mobil): bool
    {
        return Pemesanan::where('mobil_id', $mobil->id)
            ->whereIn('status', [
                'pending',
                'menunggu_konfirmasi_admin',
                'dikonfirmasi',
            ])
            ->exists();
    }

    /**
     * Hapus foto lama dari storage jika ada.
     */
    private function hapusFotoLama(Mobil $mobil): void
    {
        if ($mobil->foto) {
            Storage::disk('public')->delete($mobil->foto);
        }
    }
}