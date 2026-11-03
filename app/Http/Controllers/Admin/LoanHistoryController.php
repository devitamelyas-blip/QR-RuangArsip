<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bantex;
use App\Models\Peminjaman;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LoanHistoryController extends Controller
{
    /**
     * Tampilkan riwayat transaksi peminjaman.
     */
    public function index(Request $request): Response
    {
        $dueDays = (int) config('arsip.loan_due_days', 7);
        $overdueThreshold = Carbon::now()->subDays($dueDays);

        $search = trim((string) $request->input('search', ''));
        $status = $request->input('status', 'semua');
        $bantexId = $request->input('bantex_id', 'semua');
        $dateFrom = $request->input('date_from', '');
        $dateTo = $request->input('date_to', '');

        $query = Peminjaman::query()->with('bantex');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nama_peminjam', 'like', "%{$search}%")
                    ->orWhere('keperluan', 'like', "%{$search}%")
                    ->orWhereHas('bantex', function ($bq) use ($search) {
                        $bq->where('kode_bantex', 'like', "%{$search}%");
                    });
            });
        }

        if ($bantexId !== 'semua' && ! empty($bantexId)) {
            $query->where('bantex_id', $bantexId);
        }

        if ($dateFrom !== '') {
            $query->whereDate('tanggal_pinjam', '>=', $dateFrom);
        }

        if ($dateTo !== '') {
            $query->whereDate('tanggal_pinjam', '<=', $dateTo);
        }

        if ($status === 'dipinjam') {
            $query->whereNull('tanggal_kembali');
        } elseif ($status === 'kembali') {
            $query->whereNotNull('tanggal_kembali');
        } elseif ($status === 'terlambat') {
            $query->whereNull('tanggal_kembali')
                ->where('tanggal_pinjam', '<=', $overdueThreshold);
        }

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
                ] : null,
                'nama_peminjam' => $item->nama_peminjam,
                'keperluan' => $item->keperluan,
                'tanggal_pinjam' => $item->tanggal_pinjam?->translatedFormat('d M Y, H:i'),
                'tanggal_kembali' => $item->tanggal_kembali?->translatedFormat('d M Y, H:i'),
                'is_returned' => $item->tanggal_kembali !== null,
                'is_overdue' => $item->is_overdue,
                'lama_pinjam_hari' => $item->lama_pinjam_hari,
            ]);

        $availableBantex = Bantex::orderBy('kode_bantex')
            ->get(['id', 'kode_bantex']);

        return Inertia::render('Admin/Loans/Index', [
            'loans' => $loans,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'bantex_id' => $bantexId,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
            'availableBantex' => $availableBantex,
            'loanDueDays' => $dueDays,
        ]);
    }

    /**
     * Export data riwayat peminjaman ke format CSV.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $dueDays = (int) config('arsip.loan_due_days', 7);
        $overdueThreshold = Carbon::now()->subDays($dueDays);

        $search = trim((string) $request->input('search', ''));
        $status = $request->input('status', 'semua');
        $bantexId = $request->input('bantex_id', 'semua');
        $dateFrom = $request->input('date_from', '');
        $dateTo = $request->input('date_to', '');

        $query = Peminjaman::query()->with('bantex');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nama_peminjam', 'like', "%{$search}%")
                    ->orWhere('keperluan', 'like', "%{$search}%")
                    ->orWhereHas('bantex', function ($bq) use ($search) {
                        $bq->where('kode_bantex', 'like', "%{$search}%");
                    });
            });
        }

        if ($bantexId !== 'semua' && ! empty($bantexId)) {
            $query->where('bantex_id', $bantexId);
        }

        if ($dateFrom !== '') {
            $query->whereDate('tanggal_pinjam', '>=', $dateFrom);
        }

        if ($dateTo !== '') {
            $query->whereDate('tanggal_pinjam', '<=', $dateTo);
        }

        if ($status === 'dipinjam') {
            $query->whereNull('tanggal_kembali');
        } elseif ($status === 'kembali') {
            $query->whereNotNull('tanggal_kembali');
        } elseif ($status === 'terlambat') {
            $query->whereNull('tanggal_kembali')
                ->where('tanggal_pinjam', '<=', $overdueThreshold);
        }

        $records = $query->orderByDesc('tanggal_pinjam')->get();

        $filename = 'riwayat_peminjaman_arsip_'.date('Y-m-d_His').'.csv';

        return response()->streamDownload(function () use ($records) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM agar rapi di Microsoft Excel
            fwrite($handle, "\xEF\xBB\xBF");

            // Header kolom CSV
            fputcsv($handle, [
                'No',
                'Kode Bantex',
                'Tim',
                'Lokasi Rak',
                'Nama Peminjam',
                'Keperluan',
                'Tanggal Pinjam',
                'Tanggal Kembali',
                'Lama Pinjam (Hari)',
                'Status',
                'Keterangan Terlambat',
            ]);

            $no = 1;
            foreach ($records as $item) {
                $statusText = $item->tanggal_kembali ? 'Sudah Kembali' : 'Sedang Dipinjam';
                $overdueText = $item->is_overdue ? 'Terlambat (> '.config('arsip.loan_due_days', 7).' hari)' : '-';

                fputcsv($handle, [
                    $no++,
                    $item->bantex?->kode_bantex ?? '-',
                    $item->bantex?->tim ?? '-',
                    $item->bantex ? "{$item->bantex->nomor_rak}, {$item->bantex->baris}, {$item->bantex->urutan_bantex}" : '-',
                    $item->nama_peminjam,
                    $item->keperluan,
                    $item->tanggal_pinjam?->format('Y-m-d H:i:s') ?? '-',
                    $item->tanggal_kembali?->format('Y-m-d H:i:s') ?? '-',
                    $item->lama_pinjam_hari,
                    $statusText,
                    $overdueText,
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
