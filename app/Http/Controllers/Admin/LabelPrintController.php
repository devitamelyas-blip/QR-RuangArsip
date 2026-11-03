<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bantex;
use App\Services\QrCodeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LabelPrintController extends Controller
{
    public function __construct(
        protected QrCodeService $qrCodeService
    ) {}

    /**
     * Tampilkan cetak label untuk satu bantex.
     */
    public function printSingle(Bantex $bantex): Response
    {
        $qrSvg = $this->qrCodeService->generateForBantex($bantex->slug, 160);

        return Inertia::render('Admin/Labels/Print', [
            'labels' => [
                [
                    'id' => $bantex->id,
                    'kode_bantex' => $bantex->kode_bantex,
                    'slug' => $bantex->slug,
                    'tim' => $bantex->tim,
                    'nomor_rak' => $bantex->nomor_rak,
                    'baris' => $bantex->baris,
                    'urutan_bantex' => $bantex->urutan_bantex,
                    'keterangan_lokasi' => $bantex->keterangan_lokasi,
                    'qr_svg' => $qrSvg,
                ],
            ],
        ]);
    }

    /**
     * Tampilkan cetak label massal untuk beberapa bantex yang dipilih.
     */
    public function printBatch(Request $request): Response|RedirectResponse
    {
        $ids = $request->input('ids');

        if (is_string($ids)) {
            $ids = array_filter(explode(',', $ids));
        }

        if (empty($ids) || ! is_array($ids)) {
            return back()->with('error', 'Pilih minimal satu bantex untuk dicetak labelnya.');
        }

        $items = Bantex::whereIn('id', $ids)
            ->orderBy('kode_bantex')
            ->get();

        if ($items->isEmpty()) {
            return back()->with('error', 'Tidak ada data bantex yang ditemukan untuk dicetak.');
        }

        $labels = $items->map(function ($bantex) {
            return [
                'id' => $bantex->id,
                'kode_bantex' => $bantex->kode_bantex,
                'slug' => $bantex->slug,
                'tim' => $bantex->tim,
                'nomor_rak' => $bantex->nomor_rak,
                'baris' => $bantex->baris,
                'urutan_bantex' => $bantex->urutan_bantex,
                'keterangan_lokasi' => $bantex->keterangan_lokasi,
                'qr_svg' => $this->qrCodeService->generateForBantex($bantex->slug, 160),
            ];
        })->values()->all();

        return Inertia::render('Admin/Labels/Print', [
            'labels' => $labels,
        ]);
    }
}
