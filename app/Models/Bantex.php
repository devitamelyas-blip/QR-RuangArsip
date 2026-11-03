<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Bantex extends Model
{
    use HasFactory;

    protected $table = 'bantex';

    protected $fillable = [
        'kode_bantex',
        'slug',
        'tim',
        'nomor_rak',
        'baris',
        'urutan_bantex',
        'keterangan_lokasi',
        'status',
    ];

    protected static function booted(): void
    {
        static::saving(function (Bantex $bantex) {
            if (! empty($bantex->kode_bantex) && empty($bantex->slug)) {
                $bantex->slug = static::generateSlug($bantex->kode_bantex);
            }
        });
    }

    /**
     * Generate URL-safe slug from kode_bantex (e.g. SCM/GP/R-01/01/HCGA/GA/02 -> SCM-GP-R-01-01-HCGA-GA-02).
     */
    public static function generateSlug(string $kodeBantex): string
    {
        $cleaned = preg_replace('/[^A-Za-z0-9\-]+/', '-', $kodeBantex);

        return trim(preg_replace('/-+/', '-', $cleaned), '-');
    }

    public function dokumen(): HasMany
    {
        return $this->hasMany(DokumenBantex::class, 'bantex_id');
    }

    public function peminjaman(): HasMany
    {
        return $this->hasMany(Peminjaman::class, 'bantex_id');
    }

    public function activePeminjaman(): HasOne
    {
        return $this->hasOne(Peminjaman::class, 'bantex_id')
            ->whereNull('tanggal_kembali')
            ->latestOfMany('tanggal_pinjam');
    }

    public function isTersedia(): bool
    {
        return $this->status === 'tersedia';
    }

    public function isDipinjam(): bool
    {
        return $this->status === 'dipinjam';
    }
}
