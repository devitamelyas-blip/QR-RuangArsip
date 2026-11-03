<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Batas Hari Peminjaman (Overdue)
    |--------------------------------------------------------------------------
    | Jumlah hari maksimal peminjaman sebelum ditandai 'terlambat'.
    */
    'loan_due_days' => (int) env('LOAN_DUE_DAYS', 7),

    /*
    |--------------------------------------------------------------------------
    | IP / CIDR Diizinkan untuk Halaman Publik
    |--------------------------------------------------------------------------
    | Daftar IP/CIDR yang diizinkan mengakses scan publik & pencarian publik.
    | Jika kosong (default), middleware tidak melakukan pembatasan.
    */
    'allowed_ip_ranges' => array_values(array_filter(
        array_map('trim', explode(',', env('ALLOWED_IP_RANGES', '')))
    )),
];
