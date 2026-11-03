<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBantexRequest;
use App\Http\Requests\UpdateBantexRequest;
use App\Models\Bantex;
use App\Models\DokumenBantex;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class BantexController extends Controller
{
    /**
     * Tampilkan daftar master data bantex.
     */
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

    /**
     * Form tambah bantex baru.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Bantex/Create');
    }

    /**
     * Simpan bantex baru beserta dokumennya.
     */
    public function store(StoreBantexRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $slug = Bantex::generateSlug($request->validated('kode_bantex'));

            $bantex = Bantex::create([
                'kode_bantex' => $request->validated('kode_bantex'),
                'slug' => $slug,
                'tim' => $request->validated('tim'),
                'nomor_rak' => $request->validated('nomor_rak'),
                'baris' => $request->validated('baris'),
                'urutan_bantex' => $request->validated('urutan_bantex'),
                'keterangan_lokasi' => $request->validated('keterangan_lokasi'),
                'status' => 'tersedia',
            ]);

            $dokumenList = $request->validated('dokumen', []);
            foreach ($dokumenList as $doc) {
                if (! empty($doc['no_pengadaan']) && ! empty($doc['nama_pengadaan'])) {
                    $bantex->dokumen()->create([
                        'no_pengadaan' => trim($doc['no_pengadaan']),
                        'nama_pengadaan' => trim($doc['nama_pengadaan']),
                        'tahun' => trim($doc['tahun'] ?? date('Y')),
                    ]);
                }
            }
        });

        return redirect()->route('admin.bantex.index')
            ->with('success', 'Bantex dan daftar dokumen berhasil ditambahkan.');
    }

    /**
     * Form edit bantex dan dokumennya.
     */
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

    /**
     * Update data bantex dan sinkronisasi dokumennya.
     */
    public function update(UpdateBantexRequest $request, Bantex $bantex): RedirectResponse
    {
        DB::transaction(function () use ($request, $bantex) {
            $newCode = $request->validated('kode_bantex');
            $newSlug = Bantex::generateSlug($newCode);

            $bantex->update([
                'kode_bantex' => $newCode,
                'slug' => $newSlug,
                'tim' => $request->validated('tim'),
                'nomor_rak' => $request->validated('nomor_rak'),
                'baris' => $request->validated('baris'),
                'urutan_bantex' => $request->validated('urutan_bantex'),
                'keterangan_lokasi' => $request->validated('keterangan_lokasi'),
            ]);

            $submittedDocs = $request->validated('dokumen', []);
            $keptDocIds = [];

            foreach ($submittedDocs as $doc) {
                if (empty($doc['no_pengadaan']) || empty($doc['nama_pengadaan'])) {
                    continue;
                }

                if (! empty($doc['id'])) {
                    $existing = DokumenBantex::where('bantex_id', $bantex->id)
                        ->where('id', $doc['id'])
                        ->first();

                    if ($existing) {
                        $existing->update([
                            'no_pengadaan' => trim($doc['no_pengadaan']),
                            'nama_pengadaan' => trim($doc['nama_pengadaan']),
                            'tahun' => trim($doc['tahun'] ?? date('Y')),
                        ]);
                        $keptDocIds[] = $existing->id;
                    }
                } else {
                    $newDoc = $bantex->dokumen()->create([
                        'no_pengadaan' => trim($doc['no_pengadaan']),
                        'nama_pengadaan' => trim($doc['nama_pengadaan']),
                        'tahun' => trim($doc['tahun'] ?? date('Y')),
                    ]);
                    $keptDocIds[] = $newDoc->id;
                }
            }

            // Hapus dokumen yang dibuang dari form edit
            $bantex->dokumen()->whereNotIn('id', $keptDocIds)->delete();
        });

        return redirect()->route('admin.bantex.index')
            ->with('success', 'Data bantex dan daftar dokumen berhasil diperbarui.');
    }

    /**
     * Hapus bantex (dicegah jika sedang berstatus dipinjam).
     */
    public function destroy(Bantex $bantex): RedirectResponse
    {
        if ($bantex->status === 'dipinjam') {
            return back()->with('error', "Gagal menghapus: Bantex '{$bantex->kode_bantex}' saat ini sedang dipinjam.");
        }

        $code = $bantex->kode_bantex;
        $bantex->delete();

        return redirect()->route('admin.bantex.index')
            ->with('success', "Bantex '{$code}' beserta dokumen di dalamnya berhasil dihapus.");
    }
}
