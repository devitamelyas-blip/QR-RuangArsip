<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bantex;
use App\Models\Peminjaman;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Dashboard monitoring status bantex dan peminjaman.
     */
    public function index(Request $request): Response
    {
        $dueDays = (int) config('arsip.loan_due_days', 7);
        $overdueThreshold = Carbon::now()->subDays($dueDays);

        // Ringkasan Statistik
        $totalBantex = Bantex::count();
        $totalTersedia = Bantex::where('status', 'tersedia')->count();
        $totalDipinjam = Bantex::where('status', 'dipinjam')->count();
        $totalTerlambat = Peminjaman::whereNull('tanggal_kembali')
            ->where('tanggal_pinjam', '<=', $overdueThreshold)
            ->count();

        // Query tabel monitoring
        $search = trim((string) $request->input('search', ''));
        $statusFilter = $request->input('status', 'semua');
        $timFilter = $request->input('tim', 'semua');

        $query = Bantex::query()
            ->with(['activePeminjaman'])
            ->withCount('dokumen');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('kode_bantex', 'like', "%{$search}%")
                    ->orWhere('keterangan_lokasi', 'like', "%{$search}%")
                    ->orWhereHas('activePeminjaman', function ($pq) use ($search) {
                        $pq->where('nama_peminjam', 'like', "%{$search}%")
                            ->orWhere('keperluan', 'like', "%{$search}%");
                    });
            });
        }

        if ($timFilter !== 'semua' && $timFilter !== '') {
            $query->where('tim', $timFilter);
        }

        if ($statusFilter === 'tersedia') {
            $query->where('status', 'tersedia');
        } elseif ($statusFilter === 'dipinjam') {
            $query->where('status', 'dipinjam');
        } elseif ($statusFilter === 'terlambat') {
            $query->where('status', 'dipinjam')
                ->whereHas('activePeminjaman', function ($pq) use ($overdueThreshold) {
                    $pq->whereNull('tanggal_kembali')
                        ->where('tanggal_pinjam', '<=', $overdueThreshold);
                });
        }

        $bantexList = $query->orderByRaw("CASE WHEN status = 'dipinjam' THEN 0 ELSE 1 END")
            ->orderBy('kode_bantex')
            ->paginate(15)
            ->withQueryString()
            ->through(fn ($item) => [
                'id' => $item->id,
                'kode_bantex' => $item->kode_bantex,
                'slug' => $item->slug,
                'tim' => $item->tim,
                'nomor_rak' => $item->nomor_rak,
                'baris' => $item->baris,
                'urutan_bantex' => $item->urutan_bantex,
                'keterangan_lokasi' => $item->keterangan_lokasi,
                'status' => $item->status,
                'dokumen_count' => $item->dokumen_count,
                'peminjaman_aktif' => $item->activePeminjaman ? [
                    'id' => $item->activePeminjaman->id,
                    'nama_peminjam' => $item->activePeminjaman->nama_peminjam,
                    'keperluan' => $item->activePeminjaman->keperluan,
                    'tanggal_pinjam' => $item->activePeminjaman->tanggal_pinjam?->translatedFormat('d M Y, H:i'),
                    'lama_pinjam_hari' => $item->activePeminjaman->lama_pinjam_hari,
                    'is_overdue' => $item->activePeminjaman->is_overdue,
                ] : null,
            ]);

        $availableTeams = Bantex::select('tim')->distinct()->orderBy('tim')->pluck('tim');

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'total_bantex' => $totalBantex,
                'tersedia' => $totalTersedia,
                'dipinjam' => $totalDipinjam,
                'terlambat' => $totalTerlambat,
                'loan_due_days' => $dueDays,
            ],
            'filters' => [
                'search' => $search,
                'status' => $statusFilter,
                'tim' => $timFilter,
            ],
            'availableTeams' => $availableTeams,
            'bantexList' => $bantexList,
        ]);
    }

    /**
     * Pengembalian manual bantex dari dashboard admin (untuk kasus peminjam lupa scan).
     */
    public function returnManual(Request $request, Bantex $bantex): RedirectResponse
    {
        return DB::transaction(function () use ($bantex) {
            $lockedBantex = Bantex::where('id', $bantex->id)->lockForUpdate()->firstOrFail();

            if ($lockedBantex->status !== 'dipinjam') {
                return back()->with('error', "Bantex {$lockedBantex->kode_bantex} sudah dalam status tersedia.");
            }

            $activeLoan = Peminjaman::where('bantex_id', $lockedBantex->id)
                ->whereNull('tanggal_kembali')
                ->latest('tanggal_pinjam')
                ->first();

            if ($activeLoan) {
                $activeLoan->update([
                    'tanggal_kembali' => Carbon::now(),
                ]);
            }

            $lockedBantex->update(['status' => 'tersedia']);

            return back()->with('success', "Bantex {$lockedBantex->kode_bantex} berhasil dikembalikan secara manual ke rak.");
        });
    }
}

