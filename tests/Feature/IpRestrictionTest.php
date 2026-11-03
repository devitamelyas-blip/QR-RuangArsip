<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;

uses(RefreshDatabase::class);

test('akses halaman publik diizinkan jika config allowed_ip_ranges kosong', function () {
    Config::set('arsip.allowed_ip_ranges', []);

    $response = $this->get(route('public.search'));

    $response->assertOk();
});

test('akses halaman publik diizinkan jika ip klien cocok dengan range ip', function () {
    Config::set('arsip.allowed_ip_ranges', ['192.168.1.0/24', '127.0.0.1']);

    $response = $this->withServerVariables(['REMOTE_ADDR' => '192.168.1.45'])
        ->get(route('public.search'));

    $response->assertOk();
});

test('akses halaman publik ditolak 403 jika ip klien di luar range ip yang ditentukan', function () {
    Config::set('arsip.allowed_ip_ranges', ['192.168.1.0/24']);

    $response = $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.99'])
        ->get(route('public.search'));

    $response->assertForbidden();
});
