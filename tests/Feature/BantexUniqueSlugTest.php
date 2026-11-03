<?php

use App\Models\Bantex;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('slug dibuat otomatis dari kode_bantex dan bersifat aman untuk url', function () {
    $bantex = Bantex::create([
        'kode_bantex' => 'SCM/GP/R-01/01/HCGA/GA/02',
        'tim' => 'GP',
        'nomor_rak' => 'Rak 1',
        'baris' => 'Baris 1',
        'urutan_bantex' => 'Bantex 02',
        'status' => 'tersedia',
    ]);

    expect($bantex->slug)->toBe('SCM-GP-R-01-01-HCGA-GA-02');
});

test('kode_bantex harus unik di database', function () {
    Bantex::create([
        'kode_bantex' => 'SCM/GP/R-01/01/HCGA/GA/99',
        'tim' => 'GP',
        'nomor_rak' => 'Rak 1',
        'baris' => 'Baris 1',
        'urutan_bantex' => 'Bantex 99',
        'status' => 'tersedia',
    ]);

    expect(fn () => Bantex::create([
        'kode_bantex' => 'SCM/GP/R-01/01/HCGA/GA/99',
        'tim' => 'GP',
        'nomor_rak' => 'Rak 1',
        'baris' => 'Baris 1',
        'urutan_bantex' => 'Bantex 99',
        'status' => 'tersedia',
    ]))->toThrow(QueryException::class);
});
