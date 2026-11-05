<?php

use App\Models\Bantex;
use App\Models\Peminjaman;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

test('admin dashboard menampilkan metrik statistik dan daftar monitoring bantex', function () {
    $admin = User::factory()->create();

    $bantexTersedia = Bantex::factory()->create(['status' => 'tersedia']);
    $bantexDipinjam = Bantex::factory()->create(['status' => 'dipinjam']);

    Peminjaman::factory()->create([
        'bantex_id' => $bantexDipinjam->id,
        'nama_peminjam' => 'Doni Darmawan',
        'tanggal_pinjam' => Carbon::now()->subDays(10),
        'tanggal_kembali' => null,
    ]);

    $response = $this->actingAs($admin)->get(route('admin.dashboard'));

    $response->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Dashboard')
            ->where('stats.total_bantex', 2)
            ->where('stats.tersedia', 1)
            ->where('stats.dipinjam', 1)
            ->where('stats.terlambat', 1)
        );
});

test('admin dapat mengembalikan bantex secara manual dari dashboard', function () {
    $admin = User::factory()->create();

    $bantex = Bantex::factory()->create(['status' => 'dipinjam']);
    $loan = Peminjaman::factory()->create([
        'bantex_id' => $bantex->id,
        'nama_peminjam' => 'Lupa Scan',
        'tanggal_kembali' => null,
    ]);

    $response = $this->actingAs($admin)->post(route('admin.dashboard.return-manual', $bantex->id));

    $response->assertSessionHas('success');

    $bantex->refresh();
    $loan->refresh();

    expect($bantex->status)->toBe('tersedia');
    expect($loan->tanggal_kembali)->not->toBeNull();
});

test('admin dapat melihat halaman riwayat peminjaman dengan filter', function () {
    $admin = User::factory()->create();
    $bantex = Bantex::factory()->create();

    Peminjaman::factory()->create([
        'bantex_id' => $bantex->id,
        'nama_peminjam' => 'Sari Indah',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.loans.index', ['search' => 'Sari Indah']));

    $response->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Loans/Index')
            ->has('loans.data', 1)
            ->where('loans.data.0.nama_peminjam', 'Sari Indah')
        );
});

test('admin dapat mengekspor riwayat peminjaman ke format csv', function () {
    $admin = User::factory()->create();
    $bantex = Bantex::factory()->create([
        'kode_bantex' => 'SCM/GP/R-01/01/HCGA/GA/99',
    ]);

    Peminjaman::factory()->create([
        'bantex_id' => $bantex->id,
        'nama_peminjam' => 'Export User Test',
        'keperluan' => 'Pemeriksaan Export CSV',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.loans.export'));

    $response->assertOk();
    $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    expect($response->streamedContent())->toContain('Export User Test');
    expect($response->streamedContent())->toContain('SCM/GP/R-01/01/HCGA/GA/99');
});
