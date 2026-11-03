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

