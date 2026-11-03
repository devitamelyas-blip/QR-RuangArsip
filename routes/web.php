<?php

use App\Http\Controllers\Admin\BantexController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LabelPrintController;
use App\Http\Controllers\Admin\LoanHistoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Public\ScanController;
use App\Http\Controllers\Public\SearchController;
use Illuminate\Support\Facades\Route;

// Redirect halaman utama ke pencarian publik
Route::get('/', function () {
    return redirect()->route('public.search');
});

// Group Halaman Publik (dengan proteksi opsional IP Internal bila diset di .env)
Route::middleware(['restrict.ip'])->group(function () {
    // Scan QR Code Bantex
    Route::get('/b/{slug}', [ScanController::class, 'show'])->name('public.scan');
    Route::post('/b/{slug}/pinjam', [ScanController::class, 'borrow'])
        ->middleware('throttle:20,1')
        ->name('public.scan.borrow');
    Route::post('/b/{slug}/kembali', [ScanController::class, 'returnBantex'])
        ->middleware('throttle:20,1')
        ->name('public.scan.return');

    // Pencarian Dokumen & Lokasi
    Route::get('/cari', [SearchController::class, 'index'])->name('public.search');
});

// Redirect default dashboard ke admin dashboard
Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Group Area Admin Arsip
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard Monitoring & Statistik
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/bantex/{bantex}/kembalikan-manual', [DashboardController::class, 'returnManual'])
        ->name('dashboard.return-manual');

    // Master Data Bantex & Dokumen
    Route::resource('bantex', BantexController::class);

    // Riwayat Peminjaman & Export CSV
    Route::get('/riwayat-peminjaman', [LoanHistoryController::class, 'index'])->name('loans.index');
    Route::get('/riwayat-peminjaman/export', [LoanHistoryController::class, 'exportCsv'])->name('loans.export');

    // Cetak Label QR Code Satuan & Massal
    Route::get('/bantex/{bantex}/cetak', [LabelPrintController::class, 'printSingle'])->name('labels.print-single');
    Route::match(['get', 'post'], '/cetak-massal', [LabelPrintController::class, 'printBatch'])->name('labels.print-batch');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
