<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjaman';

    protected $fillable = [
        'bantex_id',
        'nama_peminjam',
        'keperluan',
        'tanggal_pinjam',
        'tanggal_kembali',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pinjam' => 'datetime',
            'tanggal_kembali' => 'datetime',
        ];
    }

    protected $appends = [
        'is_overdue',
        'lama_pinjam_hari',
    ];

    public function bantex(): BelongsTo
    {
        return $this->belongsTo(Bantex::class, 'bantex_id');
    }

    protected function isOverdue(): Attribute
    {
        return Attribute::make(
            get: function (): bool {
                if ($this->tanggal_kembali !== null) {
                    return false;
                }
                $dueDays = (int) config('arsip.loan_due_days', 7);
                return $this->tanggal_pinjam ? $this->tanggal_pinjam->copy()->addDays($dueDays)->isPast() : false;
            }
        );
    }

    protected function lamaPinjamHari(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                $endDate = $this->tanggal_kembali ?? Carbon::now();
                return $this->tanggal_pinjam ? (int) $this->tanggal_pinjam->diffInDays($endDate) : 0;
            }
        );
    }
}

