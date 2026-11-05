<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\BorrowBantexRequest;
use App\Models\Bantex;
use App\Services\LoanService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ScanController extends Controller
{
    public function __construct(
        protected LoanService $loanService
    ) {}

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

    public function borrow(BorrowBantexRequest $request, string $slug): RedirectResponse
    {
        try {
            $bantex = $this->loanService->borrowBySlug($slug, $request->validated());

            return back()->with('success', "Peminjaman bantex {$bantex->kode_bantex} berhasil dicatat. Silakan ambil dokumen dari rak.");
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function returnBantex(Request $request, string $slug): RedirectResponse
    {
        try {
            $bantex = $this->loanService->returnBySlug($slug);

            return back()->with('success', "Bantex {$bantex->kode_bantex} berhasil dikembalikan ke status tersedia. Terima kasih!");
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
