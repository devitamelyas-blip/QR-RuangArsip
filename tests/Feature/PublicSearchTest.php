<?php

use App\Models\Bantex;
use App\Models\DokumenBantex;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

test('halaman pencarian publik dapat diakses', function () {
    $response = $this->get(route('public.search'));

    $response->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Public/Search'));
});

test('pencarian dapat menemukan dokumen berdasarkan nomor pengadaan', function () {
    $bantex = Bantex::factory()->create([
        'kode_bantex' => 'SCM/GP/R-01/01/HCGA/GA/01',
    ]);

    DokumenBantex::factory()->create([
        'bantex_id' => $bantex->id,
        'no_pengadaan' => 'S269999Z',
        'nama_pengadaan' => 'Pengadaan Khusus Seng Bergelombang',
        'tahun' => '2026',
    ]);

    $response = $this->get(route('public.search', ['q' => 'S269999Z']));

    $response->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Public/Search')
            ->has('results.data', 1)
            ->where('results.data.0.no_pengadaan', 'S269999Z')
            ->where('results.data.0.nama_pengadaan', 'Pengadaan Khusus Seng Bergelombang')
        );
});

test('pencarian dapat menemukan dokumen berdasarkan nama pengadaan', function () {
    $bantex = Bantex::factory()->create();

    DokumenBantex::factory()->create([
        'bantex_id' => $bantex->id,
        'nama_pengadaan' => 'Pengadaan Roll Forming Spesifik',
    ]);

    $response = $this->get(route('public.search', ['q' => 'Roll Forming Spesifik']));

    $response->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Public/Search')
            ->has('results.data', 1)
            ->where('results.data.0.nama_pengadaan', 'Pengadaan Roll Forming Spesifik')
        );
});

test('pencarian dokumen berdasarkan kode bantex', function () {
    $bantex = Bantex::factory()->create([
        'kode_bantex' => 'SCM/AC/R-05/02/FIN/ACC/09',
    ]);

    DokumenBantex::factory()->create([
        'bantex_id' => $bantex->id,
        'nama_pengadaan' => 'Dokumen Keuangan AC 2026',
    ]);

    $response = $this->get(route('public.search', ['q' => 'SCM/AC/R-05']));

    $response->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Public/Search')
            ->has('results.data', 1)
            ->where('results.data.0.nama_pengadaan', 'Dokumen Keuangan AC 2026')
        );
});

test('akses url qr ruang gp menyaring dokumen khusus ruang arsip 1 gp', function () {
    $bantexGp = Bantex::factory()->create(['tim' => 'GP', 'kode_bantex' => 'SCM/GP/R-01/01/GA/01']);
    $bantexAc = Bantex::factory()->create(['tim' => 'AC', 'kode_bantex' => 'SCM/AC/R-02/01/ACC/01']);

    DokumenBantex::factory()->create([
        'bantex_id' => $bantexGp->id,
        'nama_pengadaan' => 'Pengadaan GP Khusus',
    ]);
    DokumenBantex::factory()->create([
        'bantex_id' => $bantexAc->id,
        'nama_pengadaan' => 'Pengadaan AC Lain',
    ]);

    $response = $this->get(route('public.room.gp'));

    $response->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Public/Search')
            ->where('filters.ruang', 'gp')
            ->has('results.data', 1)
            ->where('results.data.0.nama_pengadaan', 'Pengadaan GP Khusus')
        );
});

test('akses url qr ruang ac-ml menyaring dokumen gabungan ac dan ml', function () {
    $bantexGp = Bantex::factory()->create(['tim' => 'GP', 'kode_bantex' => 'SCM/GP/R-01/01/GA/99']);
    $bantexAc = Bantex::factory()->create(['tim' => 'AC', 'kode_bantex' => 'SCM/AC/R-02/01/ACC/01']);
    $bantexMl = Bantex::factory()->create(['tim' => 'ML', 'kode_bantex' => 'SCM/ML/R-03/01/LOG/01']);

    DokumenBantex::factory()->create(['bantex_id' => $bantexGp->id, 'nama_pengadaan' => 'GP Doc']);
    DokumenBantex::factory()->create(['bantex_id' => $bantexAc->id, 'nama_pengadaan' => 'AC Doc']);
    DokumenBantex::factory()->create(['bantex_id' => $bantexMl->id, 'nama_pengadaan' => 'ML Doc']);

    $response = $this->get(route('public.room.ac-ml'));

    $response->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Public/Search')
            ->where('filters.ruang', 'ac-ml')
            ->has('results.data', 2)
        );
});
