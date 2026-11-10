<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bantex;
use App\Models\Peminjaman;
use App\Services\LoanExportService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LoanHistoryController extends Controller
{
    public function __construct(
        protected LoanExportService $loanExportService
    ) {}

    public function index(Request $request): Response
    {
        $dueDays = (int) config('arsip.loan_due_days', 7);

        $query = $this->buildFilteredQuery($request, $dueDays);

        $loans = $query->orderByDesc('tanggal_pinjam')
            ->paginate(15)
            ->withQueryString()
            ->through(fn ($item) => [
                'id' => $item->id,
                'bantex' => $item->bantex ? [
                    'id' => $item->bantex->id,
                    'kode_bantex' => $item->bantex->kode_bantex,
                    'slug' => $item->bantex->slug,
                    'tim' => $item->bantex->tim,
                    'nomor_rak' => $item->bantex->nomor_rak,
                    'baris' => $item->bantex->baris,
                    'urutan_bantex' => $item->bantex->urutan_bantex,
                    'keterangan_lokasi' => $item->bantex->keterangan_lokasi,
                ] : null,
                'nama_peminjam' => $item->nama_peminjam,
                'keperluan' => $item->keperluan,
                'tanggal_pinjam' => $item->tanggal_pinjam?->translatedFormat('d M Y, H:i'),
                'tanggal_kembali' => $item->tanggal_kembali?->translatedFormat('d M Y, H:i'),
                'lama_pinjam_hari' => $item->lama_pinjam_hari,
                'is_overdue' => $item->is_overdue,
            ]);

        $allBantex = Bantex::select('id', 'kode_bantex')->orderBy('kode_bantex')->get();

        return Inertia::render('Admin/Loans/Index', [
            'loans' => $loans,
            'allBantex' => $allBantex,
            'filters' => [
                'search' => trim((string) $request->input('search', '')),
                'status' => $request->input('status', 'semua'),
                'bantex_id' => $request->input('bantex_id', 'semua'),
                'date_from' => $request->input('date_from', ''),
                'date_to' => $request->input('date_to', ''),
            ],
            'loan_due_days' => $dueDays,
        ]);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $dueDays = (int) config('arsip.loan_due_days', 7);
        $query = $this->buildFilteredQuery($request, $dueDays);
        $records = $query->with(['bantex.dokumen'])->orderByDesc('tanggal_pinjam')->get();

        $format = strtolower((string) $request->input('format', 'csv'));

        if ($format === 'xlsx') {
            $filters = [
                'search' => trim((string) $request->input('search', '')),
                'status' => $request->input('status', 'semua'),
                'bantex_id' => $request->input('bantex_id', 'semua'),
                'date_from' => $request->input('date_from', ''),
                'date_to' => $request->input('date_to', ''),
            ];

            return $this->loanExportService->exportXlsx($records, $filters, $dueDays);
        }

        return $this->loanExportService->exportCsv($records, $dueDays);
    }

    /**
     * Build the reusable filtered query for both listing and export.
     */
    protected function buildFilteredQuery(Request $request, int $dueDays): Builder
    {
        $overdueThreshold = Carbon::now()->subDays($dueDays);

        $search = trim((string) $request->input('search', ''));
        $status = $request->input('status', 'semua');
        $bantexId = $request->input('bantex_id', 'semua');
        $dateFrom = $request->input('date_from', '');
        $dateTo = $request->input('date_to', '');

        $query = Peminjaman::query()->with('bantex');

        if ($search !== '') {
            $query->where(function (Builder $q) use ($search) {
                $q->where('nama_peminjam', 'like', "%{$search}%")
                    ->orWhere('keperluan', 'like', "%{$search}%")
                    ->orWhereHas('bantex', function (Builder $bq) use ($search) {
                        $bq->where('kode_bantex', 'like', "%{$search}%")
                            ->orWhere('tim', 'like', "%{$search}%")
                            ->orWhere('nomor_rak', 'like', "%{$search}%")
                            ->orWhere('baris', 'like', "%{$search}%")
                            ->orWhere('keterangan_lokasi', 'like', "%{$search}%")
                            ->orWhereHas('dokumen', function (Builder $dq) use ($search) {
                                $dq->where('nama_pengadaan', 'like', "%{$search}%")
                                    ->orWhere('no_pengadaan', 'like', "%{$search}%")
                                    ->orWhere('keterangan', 'like', "%{$search}%");
                            });
                    });
            });
        }

        if ($bantexId !== 'semua' && ! empty($bantexId)) {
            $query->where('bantex_id', $bantexId);
        }

        if ($dateFrom !== '') {
            $query->where('tanggal_pinjam', '>=', Carbon::parse($dateFrom)->startOfDay());
        }

        if ($dateTo !== '') {
            $query->where('tanggal_pinjam', '<=', Carbon::parse($dateTo)->endOfDay());
        }

        if ($status === 'dipinjam') {
            $query->whereNull('tanggal_kembali');
        } elseif ($status === 'kembali') {
            $query->whereNotNull('tanggal_kembali');
        } elseif ($status === 'terlambat') {
            $query->whereNull('tanggal_kembali')
                ->where('tanggal_pinjam', '<=', $overdueThreshold);
        }

        return $query;
    }
}
