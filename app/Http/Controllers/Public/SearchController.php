<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\DokumenBantex;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SearchController extends Controller
{
    public function index(Request $request): Response
    {
        $keyword = trim((string) $request->input('q', ''));
        $results = null;

        if ($keyword !== '') {
            $results = DokumenBantex::query()
                ->with(['bantex'])
                ->where(function ($query) use ($keyword) {
                    $query->where('no_pengadaan', 'like', "%{$keyword}%")
                        ->orWhere('nama_pengadaan', 'like', "%{$keyword}%")
                        ->orWhere('tahun', 'like', "%{$keyword}%")
                        ->orWhereHas('bantex', function ($bQuery) use ($keyword) {
                            $bQuery->where('kode_bantex', 'like', "%{$keyword}%")
                                ->orWhere('keterangan_lokasi', 'like', "%{$keyword}%")
                                ->orWhere('tim', 'like', "%{$keyword}%")
                                ->orWhere('nomor_rak', 'like', "%{$keyword}%");
                        });
                })
                ->orderBy('nama_pengadaan')
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
                        'status' => $doc->bantex->status,
                    ] : null,
                ]);
        }

        return Inertia::render('Public/Search', [
            'filters' => [
                'q' => $keyword,
            ],
            'results' => $results,
        ]);
    }
}
