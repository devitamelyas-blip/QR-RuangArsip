<?php

use App\Models\Bantex;
use App\Models\DokumenBantex;
use App\Models\Peminjaman;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

test('halaman scan publik dapat diakses tanpa login', function () {
    $bantex = Bantex::factory()->create([
        'kode_bantex' => 'SCM/GP/R-01/01/HCGA/GA/01',
        'status' => 'tersedia',
    ]);

    DokumenBantex::factory()->create([
        'bantex_id' => $bantex->id,
        'no_pengadaan' => 'S260071A',
        'nama_pengadaan' => 'Pengadaan Atap Galvalum',
        'tahun' => '2026',
    ]);

    $response = $this->get(route('public.scan', $bantex->slug));

    $response->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Public/Scan')
            ->where('bantex.kode_bantex', 'SCM/GP/R-01/01/HCGA/GA/01')
            ->has('bantex.dokumen', 1)
            ->where('bantex.dokumen.0.nama_pengadaan', 'Pengadaan Atap Galvalum')
        );
});

test('peminjaman berhasil jika bantex berstatus tersedia', function () {
    $bantex = Bantex::factory()->create([
        'status' => 'tersedia',
    ]);

    $response = $this->post(route('public.scan.borrow', $bantex->slug), [
        'nama_peminjam' => 'Ahmad Dani',
        'keperluan' => 'Pengecekan fisik invoice dan faktur pajak',
    ]);

    $response->assertSessionHas('success');

    $bantex->refresh();
    expect($bantex->status)->toBe('dipinjam');

    $this->assertDatabaseHas('peminjaman', [
        'bantex_id' => $bantex->id,
        'nama_peminjam' => 'Ahmad Dani',
        'tanggal_kembali' => null,
    ]);
});

test('peminjaman ditolak jika bantex sudah berstatus dipinjam', function () {
    $bantex = Bantex::factory()->create([
        'status' => 'dipinjam',
    ]);

    Peminjaman::factory()->create([
        'bantex_id' => $bantex->id,
        'nama_peminjam' => 'Peminjam Pertama',
        'tanggal_kembali' => null,
    ]);

    $response = $this->post(route('public.scan.borrow', $bantex->slug), [
        'nama_peminjam' => 'Peminjam Kedua',
        'keperluan' => 'Mencoba pinjam bersamaan',
    ]);

    $response->assertSessionHas('error');

    $this->assertDatabaseMissing('peminjaman', [
        'bantex_id' => $bantex->id,
        'nama_peminjam' => 'Peminjam Kedua',
    ]);

    $bantex->refresh();
    expect($bantex->status)->toBe('dipinjam');
});

test('validasi input wajib nama peminjam dan keperluan saat meminjam', function () {
    $bantex = Bantex::factory()->create([
        'status' => 'tersedia',
    ]);

    $response = $this->post(route('public.scan.borrow', $bantex->slug), [
        'nama_peminjam' => '',
        'keperluan' => '',
    ]);

    $response->assertSessionHasErrors(['nama_peminjam', 'keperluan']);
});

test('pengembalian bantex berhasil mengubah status menjadi tersedia', function () {
    $bantex = Bantex::factory()->create([
        'status' => 'dipinjam',
    ]);

    $peminjaman = Peminjaman::factory()->create([
        'bantex_id' => $bantex->id,
        'nama_peminjam' => 'Budi Utomo',
        'tanggal_kembali' => null,
    ]);

    $response = $this->post(route('public.scan.return', $bantex->slug));

    $response->assertSessionHas('success');

    $bantex->refresh();
    expect($bantex->status)->toBe('tersedia');

    $peminjaman->refresh();
    expect($peminjaman->tanggal_kembali)->not->toBeNull();
});

test('pengembalian ditolak jika bantex sudah berstatus tersedia', function () {
    $bantex = Bantex::factory()->create([
        'status' => 'tersedia',
    ]);

    $response = $this->post(route('public.scan.return', $bantex->slug));

    $response->assertSessionHas('error');
    expect($bantex->fresh()->status)->toBe('tersedia');
});
