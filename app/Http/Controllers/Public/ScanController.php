<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\BorrowBantexRequest;
use App\Models\Bantex;
use App\Models\Peminjaman;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ScanController extends Controller
{
    /**
     * Tampilkan informasi bantex hasil scan QR Code.
     */
    public function show(string $slug): Response
    {
        $bantex = Bantex::where('slug', $slug)
            ->with(['dokumen', 'activePeminjaman'])
            ->firstOrFail();

        return Inertia::render('Public/Scan', [
            'bantex' => [
                'id' => $bantex->id,
                'kode_bantex' => $bantex->kode_bantex,
                'slug' => $bantex->slug,
                'tim' => $bantex->tim,
                'nomor_rak' => $bantex->nomor_rak,
                'baris' => $bantex->baris,
                'urutan_bantex' => $bantex->urutan_bantex,
                'keterangan_lokasi' => $bantex->keterangan_lokasi,
                'status' => $bantex->status,
                'dokumen' => $bantex->dokumen->map(fn ($doc) => [
                    'id' => $doc->id,
                    'no_pengadaan' => $doc->no_pengadaan,
                    'nama_pengadaan' => $doc->nama_pengadaan,
                    'tahun' => $doc->tahun,
                ]),
                'peminjaman_aktif' => $bantex->activePeminjaman ? [
                    'id' => $bantex->activePeminjaman->id,
                    'nama_peminjam' => $bantex->activePeminjaman->nama_peminjam,
                    'keperluan' => $bantex->activePeminjaman->keperluan,
                    'tanggal_pinjam' => $bantex->activePeminjaman->tanggal_pinjam?->translatedFormat('d F Y, H:i'),
                    'is_overdue' => $bantex->activePeminjaman->is_overdue,
                    'lama_pinjam_hari' => $bantex->activePeminjaman->lama_pinjam_hari,
                ] : null,
            ],
        ]);
    }

    /**
     * Proses peminjaman bantex dengan locking transaksi database.
     */
    public function borrow(BorrowBantexRequest $request, string $slug): RedirectResponse
    {
        return DB::transaction(function () use ($request, $slug) {
            $bantex = Bantex::where('slug', $slug)
                ->lockForUpdate()
                ->firstOrFail();

            if ($bantex->status !== 'tersedia') {
                return back()->with('error', 'Gagal meminjam: Bantex ini baru saja dipinjam oleh peminjam lain.');
            }

            Peminjaman::create([
                'bantex_id' => $bantex->id,
                'nama_peminjam' => $request->validated('nama_peminjam'),
                'keperluan' => $request->validated('keperluan'),
                'tanggal_pinjam' => Carbon::now(),
                'tanggal_kembali' => null,
            ]);

            $bantex->update(['status' => 'dipinjam']);

            return back()->with('success', "Peminjaman bantex {$bantex->kode_bantex} berhasil dicatat. Silakan ambil dokumen dari rak.");
        });
    }

    /**
     * Proses pengembalian bantex dengan locking transaksi database.
     */
    public function returnBantex(Request $request, string $slug): RedirectResponse
    {
        return DB::transaction(function () use ($slug) {
            $bantex = Bantex::where('slug', $slug)
                ->lockForUpdate()
                ->firstOrFail();

            if ($bantex->status !== 'dipinjam') {
                return back()->with('error', 'Gagal memproses: Bantex ini sudah berstatus tersedia atau sudah dikembalikan.');
            }

            $activeLoan = Peminjaman::where('bantex_id', $bantex->id)
                ->whereNull('tanggal_kembali')
                ->latest('tanggal_pinjam')
                ->first();

            if ($activeLoan) {
                $activeLoan->update([
                    'tanggal_kembali' => Carbon::now(),
                ]);
            }

            $bantex->update(['status' => 'tersedia']);

            return back()->with('success', "Bantex {$bantex->kode_bantex} berhasil dikembalikan ke status tersedia. Terima kasih!");
        });
    }
}

