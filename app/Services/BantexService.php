<?php

namespace App\Services;

use App\Models\Bantex;
use App\Models\DokumenBantex;
use Illuminate\Support\Facades\DB;

class BantexService
{
    public function create(array $data): Bantex
    {
        return DB::transaction(function () use ($data) {
            $slug = Bantex::generateSlug($data['kode_bantex']);

            $bantex = Bantex::create([
                'kode_bantex' => $data['kode_bantex'],
                'slug' => $slug,
                'tim' => $data['tim'],
                'nomor_rak' => $data['nomor_rak'],
                'baris' => $data['baris'],
                'urutan_bantex' => $data['urutan_bantex'],
                'keterangan_lokasi' => $data['keterangan_lokasi'] ?? null,
                'status' => 'tersedia',
            ]);

            $dokumenList = $data['dokumen'] ?? [];
            foreach ($dokumenList as $doc) {
                if (! empty($doc['no_pengadaan']) && ! empty($doc['nama_pengadaan'])) {
                    $bantex->dokumen()->create([
                        'no_pengadaan' => trim($doc['no_pengadaan']),
                        'nama_pengadaan' => trim($doc['nama_pengadaan']),
                        'tahun' => trim($doc['tahun'] ?? date('Y')),
                    ]);
                }
            }

            return $bantex;
        });
    }

    public function update(Bantex $bantex, array $data): Bantex
    {
        return DB::transaction(function () use ($bantex, $data) {
            $newCode = $data['kode_bantex'];
            $newSlug = Bantex::generateSlug($newCode);

            $bantex->update([
                'kode_bantex' => $newCode,
                'slug' => $newSlug,
                'tim' => $data['tim'],
                'nomor_rak' => $data['nomor_rak'],
                'baris' => $data['baris'],
                'urutan_bantex' => $data['urutan_bantex'],
                'keterangan_lokasi' => $data['keterangan_lokasi'] ?? null,
            ]);

            $submittedDocs = $data['dokumen'] ?? [];
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

            $bantex->dokumen()->whereNotIn('id', $keptDocIds)->delete();

            return $bantex;
        });
    }

    public function delete(Bantex $bantex): bool
    {
        if ($bantex->status === 'dipinjam') {
            return false;
        }

        $bantex->delete();

        return true;
    }
}

