<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DokumenBantex extends Model
{
    use HasFactory;

    protected $table = 'dokumen_bantex';

    protected $fillable = [
        'bantex_id',
        'no_pengadaan',
        'nama_pengadaan',
        'tahun',
    ];

    public function bantex(): BelongsTo
    {
        return $this->belongsTo(Bantex::class, 'bantex_id');
    }
}

