<?php

use App\Models\Bantex;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

test('admin dapat membuka cetak label satuan dengan qr svg', function () {
    $admin = User::factory()->create();
    $bantex = Bantex::factory()->create([
        'kode_bantex' => 'SCM/GP/R-01/01/HCGA/GA/01',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.labels.print-single', $bantex->id));

    $response->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Labels/Print')
            ->has('labels', 1)
            ->where('labels.0.kode_bantex', 'SCM/GP/R-01/01/HCGA/GA/01')
            ->has('labels.0.qr_svg')
        );
});

test('admin dapat mencetak label secara massal', function () {
    $admin = User::factory()->create();
    $b1 = Bantex::factory()->create();
    $b2 = Bantex::factory()->create();

    $response = $this->actingAs($admin)->get(route('admin.labels.print-batch', [
        'ids' => "{$b1->id},{$b2->id}",
    ]));

    $response->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Labels/Print')
            ->has('labels', 2)
        );
});

test('admin dapat membuka cetak poster qr ruang arsip 1 dan 2', function () {
    $admin = User::factory()->create();

    $response = $this->actingAs($admin)->get(route('admin.labels.rooms'));

    $response->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Labels/Rooms')
            ->has('rooms', 2)
            ->where('rooms.0.key', 'gp')
            ->where('rooms.0.title', 'RUANG ARSIP 1')
            ->where('rooms.1.key', 'ac-ml')
            ->where('rooms.1.title', 'RUANG ARSIP 2')
            ->has('rooms.0.qr_svg')
            ->has('rooms.1.qr_svg')
        );
});
