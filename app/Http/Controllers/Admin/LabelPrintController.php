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
                    'ruang_arsip' => $bantex->ruang_arsip,
                    'ruang_key' => $bantex->ruang_arsip_key,
                    'qr_svg' => $qrSvg,
                ],
            ],
        ]);
    }

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
                'ruang_arsip' => $bantex->ruang_arsip,
                'ruang_key' => $bantex->ruang_arsip_key,
                'qr_svg' => $this->qrCodeService->generateForBantex($bantex->slug, 160),
            ];
        });

        return Inertia::render('Admin/Labels/Print', [
            'labels' => $labels,
        ]);
    }

    public function printRooms(Request $request): Response
    {
        $qrGp = $this->qrCodeService->generateForRoom('gp', 280);
        $qrAcMl = $this->qrCodeService->generateForRoom('ac-ml', 280);

        return Inertia::render('Admin/Labels/Rooms', [
            'rooms' => [
                [
                    'key' => 'gp',
                    'number' => 1,
                    'title' => 'RUANG ARSIP 1',
                    'subtitle' => 'Khusus Berkas Dokumen Pengadaan Tim GP (General Procurement)',
                    'tim_label' => 'KHUSUS DOKUMEN GP',
                    'tim_description' => 'General Procurement, Material Bangunan, Perlengkapan Gedung & Operasional',
                    'teams' => ['GP'],
                    'color' => '#0A2540',
                    'url' => $this->qrCodeService->getRoomUrl('gp'),
                    'qr_svg' => $qrGp,
                    'total_bantex' => Bantex::ruangArsip('gp')->count(),
                ],
                [
                    'key' => 'ac-ml',
                    'number' => 2,
                    'title' => 'RUANG ARSIP 2',
                    'subtitle' => 'Gabungan Berkas Dokumen Tim AC (Aircraft Commercial) & ML (Material Logistics)',
                    'tim_label' => 'DOKUMEN GABUNGAN AC & ML',
                    'tim_description' => 'Aircraft Commercial, Suku Cadang Armada, Logistik & Pemeliharaan Teknis',
                    'teams' => ['AC', 'ML'],
                    'color' => '#003865',
                    'url' => $this->qrCodeService->getRoomUrl('ac-ml'),
                    'qr_svg' => $qrAcMl,
                    'total_bantex' => Bantex::ruangArsip('ac-ml')->count(),
                ],
            ],
        ]);
    }
}
