<?php

use App\Models\Bantex;
use App\Models\DokumenBantex;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

test('tamu tidak dapat mengakses area admin bantex', function () {
    $response = $this->get(route('admin.bantex.index'));

    $response->assertRedirect(route('login'));
});

test('admin dapat melihat daftar data bantex', function () {
    $admin = User::factory()->create();
    $bantex = Bantex::factory()->create();

    $response = $this->actingAs($admin)->get(route('admin.bantex.index'));

    $response->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Bantex/Index')
            ->has('bantex.data')
        );
});

test('admin dapat menambah bantex baru beserta dokumennya', function () {
    $admin = User::factory()->create();

    $response = $this->actingAs($admin)->post(route('admin.bantex.store'), [
        'kode_bantex' => 'SCM/GP/R-03/01/HCGA/GA/05',
        'tim' => 'GP',
        'nomor_rak' => 'Rak 3',
        'baris' => 'Baris 1',
        'urutan_bantex' => 'Bantex 05',
        'keterangan_lokasi' => 'Kelompok GP Rak 3 Baris 1 Urutan 5',
        'dokumen' => [
            [
                'no_pengadaan' => 'S260901A',
                'nama_pengadaan' => 'Pengadaan Komputer Kantor',
                'tahun' => '2026',
            ],
            [
                'no_pengadaan' => 'S260902B',
                'nama_pengadaan' => 'Pengadaan Printer Thermal',
                'tahun' => '2026',
            ],
        ],
    ]);

    $response->assertRedirect(route('admin.bantex.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('bantex', [
        'kode_bantex' => 'SCM/GP/R-03/01/HCGA/GA/05',
        'slug' => 'SCM-GP-R-03-01-HCGA-GA-05',
        'tim' => 'GP',
        'status' => 'tersedia',
    ]);

    $bantex = Bantex::where('kode_bantex', 'SCM/GP/R-03/01/HCGA/GA/05')->first();
    expect($bantex->dokumen)->toHaveCount(2);
});

test('kode bantex harus unik saat admin membuat bantex baru', function () {
    $admin = User::factory()->create();
    Bantex::factory()->create([
        'kode_bantex' => 'SCM/GP/R-01/01/HCGA/GA/01',
    ]);

    $response = $this->actingAs($admin)->post(route('admin.bantex.store'), [
        'kode_bantex' => 'SCM/GP/R-01/01/HCGA/GA/01',
        'tim' => 'GP',
        'nomor_rak' => 'Rak 1',
        'baris' => 'Baris 1',
        'urutan_bantex' => 'Bantex 01',
    ]);

    $response->assertSessionHasErrors('kode_bantex');
});

test('admin dapat memperbarui bantex dan sinkronisasi daftar dokumen', function () {
    $admin = User::factory()->create();
    $bantex = Bantex::factory()->create([
        'kode_bantex' => 'SCM/AC/R-01/01/FIN/ACC/01',
    ]);

    $doc1 = DokumenBantex::factory()->create([
        'bantex_id' => $bantex->id,
        'no_pengadaan' => 'DOC-LAMA-1',
    ]);
    $doc2 = DokumenBantex::factory()->create([
        'bantex_id' => $bantex->id,
        'no_pengadaan' => 'DOC-LAMA-2',
    ]);

    $response = $this->actingAs($admin)->put(route('admin.bantex.update', $bantex->id), [
        'kode_bantex' => 'SCM/AC/R-01/01/FIN/ACC/01-REV',
        'tim' => 'AC',
        'nomor_rak' => 'Rak 1',
        'baris' => 'Baris 2',
        'urutan_bantex' => 'Bantex 01',
        'keterangan_lokasi' => 'Lokasi Diperbarui',
        'dokumen' => [
            // Perbarui doc1
            [
                'id' => $doc1->id,
                'no_pengadaan' => 'DOC-BARU-1',
                'nama_pengadaan' => 'Nama Dokumen Diperbarui',
                'tahun' => '2026',
            ],
            // doc2 dihapus (tidak disertakan)
            // Tambah doc3 baru
            [
                'no_pengadaan' => 'DOC-BARU-3',
                'nama_pengadaan' => 'Dokumen Tambahan Ketiga',
                'tahun' => '2026',
            ],
        ],
    ]);

    $response->assertRedirect(route('admin.bantex.index'));

    $bantex->refresh();
    expect($bantex->kode_bantex)->toBe('SCM/AC/R-01/01/FIN/ACC/01-REV');
    expect($bantex->slug)->toBe('SCM-AC-R-01-01-FIN-ACC-01-REV');
    expect($bantex->dokumen)->toHaveCount(2);

    $this->assertDatabaseMissing('dokumen_bantex', ['id' => $doc2->id]);
    $this->assertDatabaseHas('dokumen_bantex', ['no_pengadaan' => 'DOC-BARU-1']);
    $this->assertDatabaseHas('dokumen_bantex', ['no_pengadaan' => 'DOC-BARU-3']);
});

test('penghapusan bantex ditolak jika status sedang dipinjam', function () {
    $admin = User::factory()->create();
    $bantex = Bantex::factory()->create([
        'status' => 'dipinjam',
    ]);

    $response = $this->actingAs($admin)->delete(route('admin.bantex.destroy', $bantex->id));

    $response->assertSessionHas('error');
    $this->assertDatabaseHas('bantex', ['id' => $bantex->id]);
});

test('admin dapat menghapus bantex jika status tersedia', function () {
    $admin = User::factory()->create();
    $bantex = Bantex::factory()->create([
        'status' => 'tersedia',
    ]);

    $response = $this->actingAs($admin)->delete(route('admin.bantex.destroy', $bantex->id));

    $response->assertRedirect(route('admin.bantex.index'));
    $this->assertDatabaseMissing('bantex', ['id' => $bantex->id]);
});

