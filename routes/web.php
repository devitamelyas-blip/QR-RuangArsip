<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Public\ScanController;
use App\Http\Controllers\Public\SearchController;
use Illuminate\Support\Facades\Route;

// Redirect halaman utama langsung ke halaman pencarian arsip
Route::get('/', function () {
    return redirect()->route('public.search');
});

// Halaman Publik: Scan QR Code Bantex
Route::get('/b/{slug}', [ScanController::class, 'show'])->name('public.scan');
Route::post('/b/{slug}/pinjam', [ScanController::class, 'borrow'])
    ->middleware('throttle:20,1')
    ->name('public.scan.borrow');
Route::post('/b/{slug}/kembali', [ScanController::class, 'returnBantex'])
    ->middleware('throttle:20,1')
    ->name('public.scan.return');

// Halaman Publik: Pencarian Dokumen & Lokasi
Route::get('/cari', [SearchController::class, 'index'])->name('public.search');

Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
