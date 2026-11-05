<?php

namespace App\Services;

use App\Models\Bantex;
use App\Models\Peminjaman;
use Carbon\Carbon;
use DomainException;
use Illuminate\Support\Facades\DB;

class LoanService
{
    public function borrowBySlug(string $slug, array $data): Bantex
    {
        return DB::transaction(function () use ($slug, $data) {
            $bantex = Bantex::where('slug', $slug)
                ->lockForUpdate()
                ->firstOrFail();

            if ($bantex->status !== 'tersedia') {
                throw new DomainException('Gagal meminjam: Bantex ini baru saja dipinjam oleh peminjam lain.');
            }

            Peminjaman::create([
                'bantex_id' => $bantex->id,
                'nama_peminjam' => $data['nama_peminjam'],
                'keperluan' => $data['keperluan'],
                'tanggal_pinjam' => Carbon::now(),
                'tanggal_kembali' => null,
            ]);

            $bantex->update(['status' => 'dipinjam']);

            return $bantex;
        });
    }

    public function returnBySlug(string $slug): Bantex
    {
        return DB::transaction(function () use ($slug) {
            $bantex = Bantex::where('slug', $slug)
                ->lockForUpdate()
                ->firstOrFail();

            if ($bantex->status !== 'dipinjam') {
                throw new DomainException('Gagal memproses: Bantex ini sudah berstatus tersedia atau sudah dikembalikan.');
            }

            $activeLoan = Peminjaman::where('bantex_id', $bantex->id)
                ->whereNull('tanggal_kembali')
                ->latest('tanggal_pinjam')
                ->first();

            if ($activeLoan) {
                $activeLoan->update([
                    'tanggal_kembali' => Carbon::now(),
                ]);
            }

            $bantex->update(['status' => 'tersedia']);

            return $bantex;
        });
    }

    public function returnById(int $id): Bantex
    {
        return DB::transaction(function () use ($id) {
            $bantex = Bantex::where('id', $id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($bantex->status !== 'dipinjam') {
                throw new DomainException("Bantex {$bantex->kode_bantex} sudah dalam status tersedia.");
            }

            $activeLoan = Peminjaman::where('bantex_id', $bantex->id)
                ->whereNull('tanggal_kembali')
                ->latest('tanggal_pinjam')
                ->first();

            if ($activeLoan) {
                $activeLoan->update([
                    'tanggal_kembali' => Carbon::now(),
                ]);
            }

            $lockedBantex = Bantex::find($id);
            $lockedBantex->update(['status' => 'tersedia']);

            return $lockedBantex;
        });
    }
}

