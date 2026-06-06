<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mobil;
use App\Models\Payment;
use App\Models\Pemesanan;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->get('tahun', now()->year);

        $ringkasan = [
            'total_pendapatan' => Payment::where('status', 'paid')
                ->whereYear('paid_at', $tahun)->sum('amount'),
            'total_selesai'    => Pemesanan::where('status', 'selesai')
                ->whereYear('updated_at', $tahun)->count(),
            'total_pending'    => Pemesanan::where('status', 'pending')->count(),
            'total_dibatalkan' => Pemesanan::where('status', 'dibatalkan')
                ->whereYear('updated_at', $tahun)->count(),
        ];

        $topMobil = Mobil::withCount([
                'pemesanans as total_sewa' => fn($q) => $q->where('status', 'selesai'),
            ])
            ->withSum([
                'pemesanans as total_pendapatan' => fn($q) => $q->where('status', 'selesai'),
            ], 'total_harga')
            ->orderByDesc('total_sewa')
            ->take(5)
            ->get();

        return view('admin.laporan.index', compact('ringkasan', 'topMobil', 'tahun'));
    }

    public function chartData(Request $request)
    {
        $tahun = $request->get('tahun', now()->year);

        // Pendapatan per bulan
        $pendapatan = collect(range(1, 12))->map(fn($bulan) =>
            Payment::where('status', 'paid')
                ->whereYear('paid_at', $tahun)
                ->whereMonth('paid_at', $bulan)
                ->sum('amount')
        );

        // Distribusi status pemesanan
        $statusCounts = Pemesanan::selectRaw('status, COUNT(*) as total')
            ->whereYear('created_at', $tahun)
            ->groupBy('status')
            ->pluck('total', 'status');

        return response()->json([
            'pendapatan_per_bulan' => $pendapatan,
            'status_distribusi'    => $statusCounts,
        ]);
    }

    public function exportCsv(Request $request)
    {
        $tahun  = $request->get('tahun', now()->year);
        $status = $request->get('status');

        $pemesanans = Pemesanan::with(['user', 'mobil', 'payment'])
            ->whereYear('created_at', $tahun)
            ->when($status, fn($q) => $q->where('status', $status))
            ->oldest()
            ->get();

        $filename = "laporan-pemesanan-{$tahun}.csv";

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function () use ($pemesanans) {
            $file = fopen('php://output', 'w');

            // UTF-8 BOM agar Excel terbaca benar
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'ID', 'Pelanggan', 'Email', 'Mobil', 'Tgl Mulai',
                'Tgl Selesai', 'Durasi', 'Opsi Supir', 'Total Harga',
                'Status', 'Metode Bayar', 'Tgl Dibuat',
            ]);

            foreach ($pemesanans as $p) {
                fputcsv($file, [
                    $p->id,
                    $p->user->name,
                    $p->user->email,
                    $p->mobil->nama,
                    $p->tanggal_mulai->format('d/m/Y'),
                    $p->tanggal_selesai->format('d/m/Y'),
                    $p->durasi() . ' hari',
                    $p->opsi_supir ? 'Dengan Supir' : 'Self-Drive',
                    $p->total_harga,
                    $p->labelStatus(),
                    $p->payment?->labelMetode() ?? '-',
                    $p->created_at->format('d/m/Y H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}