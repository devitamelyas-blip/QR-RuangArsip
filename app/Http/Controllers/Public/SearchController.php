<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Bantex;
use App\Models\DokumenBantex;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SearchController extends Controller
{
    public function index(Request $request): Response
    {
        $keyword = trim((string) $request->input('q', ''));
        $ruang = strtolower((string) $request->input('ruang', 'semua'));
        if (! in_array($ruang, ['semua', 'gp', 'ac-ml', 'ac_ml'])) {
            $ruang = 'semua';
        }

        $results = null;

        if ($keyword !== '' || $ruang !== 'semua') {
            $query = DokumenBantex::query()->with(['bantex']);

            if ($ruang === 'gp') {
                $query->whereHas('bantex', fn ($bq) => $bq->ruangArsip('gp'));
            } elseif ($ruang === 'ac-ml' || $ruang === 'ac_ml') {
                $query->whereHas('bantex', fn ($bq) => $bq->ruangArsip('ac-ml'));
            }

            if ($keyword !== '') {
                $query->where(function ($q) use ($keyword) {
                    $q->where('no_pengadaan', 'like', "%{$keyword}%")
                        ->orWhere('nama_pengadaan', 'like', "%{$keyword}%")
                        ->orWhere('tahun', 'like', "%{$keyword}%")
                        ->orWhereHas('bantex', function ($bq) use ($keyword) {
                            $bq->where('kode_bantex', 'like', "%{$keyword}%")
                                ->orWhere('keterangan_lokasi', 'like', "%{$keyword}%")
                                ->orWhere('tim', 'like', "%{$keyword}%")
                                ->orWhere('nomor_rak', 'like', "%{$keyword}%");
                        });
                });
            }

            $results = $query->orderBy('nama_pengadaan')
                ->paginate(15)
                ->withQueryString()
                ->through(fn ($doc) => [
                    'id' => $doc->id,
                    'no_pengadaan' => $doc->no_pengadaan,
                    'nama_pengadaan' => $doc->nama_pengadaan,
                    'tahun' => $doc->tahun,
                    'bantex' => $doc->bantex ? [
                        'id' => $doc->bantex->id,
                        'kode_bantex' => $doc->bantex->kode_bantex,
                        'slug' => $doc->bantex->slug,
                        'tim' => $doc->bantex->tim,
                        'nomor_rak' => $doc->bantex->nomor_rak,
                        'baris' => $doc->bantex->baris,
                        'urutan_bantex' => $doc->bantex->urutan_bantex,
                        'keterangan_lokasi' => $doc->bantex->keterangan_lokasi,
                        'ruang_arsip' => $doc->bantex->ruang_arsip,
                        'ruang_key' => $doc->bantex->ruang_arsip_key,
                        'status' => $doc->bantex->status,
                    ] : null,
                ]);
        }

        $roomCounts = [
            'all' => DokumenBantex::count(),
            'gp' => DokumenBantex::whereHas('bantex', fn ($q) => $q->ruangArsip('gp'))->count(),
            'ac_ml' => DokumenBantex::whereHas('bantex', fn ($q) => $q->ruangArsip('ac-ml'))->count(),
        ];

        return Inertia::render('Public/Search', [
            'filters' => [
                'q' => $keyword,
                'ruang' => $ruang,
            ],
            'results' => $results,
            'roomCounts' => $roomCounts,
        ]);
    }

    public function roomGp(Request $request): Response
    {
        $request->merge(['ruang' => 'gp']);

        return $this->index($request);
    }

    public function roomAcMl(Request $request): Response
    {
        $request->merge(['ruang' => 'ac-ml']);

        return $this->index($request);
    }
}
