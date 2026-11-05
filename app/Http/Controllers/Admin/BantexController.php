<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBantexRequest;
use App\Http\Requests\UpdateBantexRequest;
use App\Models\Bantex;
use App\Services\BantexService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BantexController extends Controller
{
    public function __construct(
        protected BantexService $bantexService
    ) {}

    public function index(Request $request): Response
    {
        $search = trim((string) $request->input('search', ''));
        $tim = $request->input('tim', 'semua');
        $status = $request->input('status', 'semua');

        $query = Bantex::query()->withCount('dokumen');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('kode_bantex', 'like', "%{$search}%")
                    ->orWhere('nomor_rak', 'like', "%{$search}%")
                    ->orWhere('keterangan_lokasi', 'like', "%{$search}%")
                    ->orWhereHas('dokumen', function ($dq) use ($search) {
                        $dq->where('no_pengadaan', 'like', "%{$search}%")
                            ->orWhere('nama_pengadaan', 'like', "%{$search}%");
                    });
            });
        }

        if ($tim !== 'semua' && $tim !== '') {
            $query->where('tim', $tim);
        }

        if ($status !== 'semua' && $status !== '') {
            $query->where('status', $status);
        }

        $bantex = $query->orderBy('kode_bantex')
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
            ]);

        $availableTeams = Bantex::select('tim')->distinct()->orderBy('tim')->pluck('tim');

        return Inertia::render('Admin/Bantex/Index', [
            'bantex' => $bantex,
            'filters' => [
                'search' => $search,
                'tim' => $tim,
                'status' => $status,
            ],
            'availableTeams' => $availableTeams,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Bantex/Create');
    }

    public function store(StoreBantexRequest $request): RedirectResponse
    {
        $this->bantexService->create($request->validated());

        return redirect()->route('admin.bantex.index')
            ->with('success', 'Bantex dan daftar dokumen berhasil ditambahkan.');
    }

    public function edit(Bantex $bantex): Response
    {
        $bantex->load('dokumen');

        return Inertia::render('Admin/Bantex/Edit', [
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
            ],
        ]);
    }

    public function update(UpdateBantexRequest $request, Bantex $bantex): RedirectResponse
    {
        $this->bantexService->update($bantex, $request->validated());

        return redirect()->route('admin.bantex.index')
            ->with('success', 'Data bantex dan daftar dokumen berhasil diperbarui.');
    }

    public function destroy(Bantex $bantex): RedirectResponse
    {
        $code = $bantex->kode_bantex;

        if (! $this->bantexService->delete($bantex)) {
            return back()->with('error', "Gagal menghapus: Bantex '{$code}' saat ini sedang dipinjam.");
        }

        return redirect()->route('admin.bantex.index')
            ->with('success', "Bantex '{$code}' beserta dokumen di dalamnya berhasil dihapus.");
    }
}
