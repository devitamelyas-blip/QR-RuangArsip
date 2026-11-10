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

    public function getRuangArsipAttribute(): string
    {
        $timUpper = strtoupper((string) $this->tim);
        if ($timUpper === 'GP' || str_contains($this->kode_bantex, '/GP/')) {
            return 'Ruang Arsip 1 (GP)';
        }
        if (in_array($timUpper, ['AC', 'ML']) || str_contains($this->kode_bantex, '/AC/') || str_contains($this->kode_bantex, '/ML/')) {
            return 'Ruang Arsip 2 (AC & ML)';
        }
        return 'Ruang Arsip ' . ($timUpper ?: '-');
    }

    public function getRuangArsipKeyAttribute(): string
    {
        $timUpper = strtoupper((string) $this->tim);
        if ($timUpper === 'GP' || str_contains($this->kode_bantex, '/GP/')) {
            return 'gp';
        }
        return 'ac-ml';
    }

    public function scopeRuangArsip($query, string $ruang)
    {
        if ($ruang === 'gp') {
            return $query->where(function ($q) {
                $q->where('tim', 'GP')
                  ->orWhere('kode_bantex', 'like', '%/GP/%');
            });
        }

        if ($ruang === 'ac-ml' || $ruang === 'ac_ml') {
            return $query->where(function ($q) {
                $q->whereIn('tim', ['AC', 'ML'])
                  ->orWhere('kode_bantex', 'like', '%/AC/%')
                  ->orWhere('kode_bantex', 'like', '%/ML/%');
            });
        }

        return $query;
    }
}
