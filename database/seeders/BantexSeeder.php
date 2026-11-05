<?php

namespace Database\Seeders;

use App\Models\Bantex;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BantexSeeder extends Seeder
{
    public function run(): void
    {
        $bantexData = [
            [
                'kode_bantex' => 'SCM/GP/R-01/01/HCGA/GA/01',
                'tim' => 'GP',
                'nomor_rak' => 'Rak 1',
                'baris' => 'Baris 1',
                'urutan_bantex' => 'Bantex 01',
                'keterangan_lokasi' => 'Kelompok GP, Rak 1, Baris 1, Fungsi HCGA, Tim GA, Bantex ke-1',
                'status' => 'tersedia',
                'dokumen' => [
                    ['no_pengadaan' => 'S260071A', 'nama_pengadaan' => 'Pengadaan Atap Galvalum Spandek', 'tahun' => '2026'],
                    ['no_pengadaan' => 'S260072A', 'nama_pengadaan' => 'Pengadaan Rangka Baja Ringan Truss C75', 'tahun' => '2026'],
                    ['no_pengadaan' => 'S260073B', 'nama_pengadaan' => 'Pengadaan Baut Self Drilling Screw', 'tahun' => '2026'],
                ],
                'history' => [
                    [
                        'nama_peminjam' => 'Ahmad Fauzi',
                        'keperluan' => 'Pengecekan spesifikasi baut & material atap',
                        'tanggal_pinjam' => Carbon::now()->subDays(20),
                        'tanggal_kembali' => Carbon::now()->subDays(18),
                    ],
                ],
            ],
            [
                'kode_bantex' => 'SCM/GP/R-01/01/HCGA/GA/02',
                'tim' => 'GP',
                'nomor_rak' => 'Rak 1',
                'baris' => 'Baris 1',
                'urutan_bantex' => 'Bantex 02',
                'keterangan_lokasi' => 'Kelompok GP, Rak 1, Baris 1, Fungsi HCGA, Tim GA, Bantex ke-2',
                'status' => 'dipinjam',
                'dokumen' => [
                    ['no_pengadaan' => 'S250015A', 'nama_pengadaan' => 'Pengadaan Genset Ruang Arsip 50kVA', 'tahun' => '2025'],
                    ['no_pengadaan' => 'S250016C', 'nama_pengadaan' => 'Pengadaan Kabel & Panel Listrik Distribusi', 'tahun' => '2025'],
                    ['no_pengadaan' => 'S250017D', 'nama_pengadaan' => 'Pengadaan Trafo Listrik Step Down', 'tahun' => '2025'],
                ],
                'current_loan' => [
                    'nama_peminjam' => 'Budi Santoso',
                    'keperluan' => 'Audit internal pengadaan genset & panel listrik 2025',
                    'tanggal_pinjam' => Carbon::now()->subDays(10),
                ],
            ],
            [
                'kode_bantex' => 'SCM/GP/R-01/02/HCGA/GA/01',
                'tim' => 'GP',
                'nomor_rak' => 'Rak 1',
                'baris' => 'Baris 2',
                'urutan_bantex' => 'Bantex 01',
                'keterangan_lokasi' => 'Kelompok GP, Rak 1, Baris 2, Fungsi HCGA, Tim GA, Bantex ke-1',
                'status' => 'tersedia',
                'dokumen' => [
                    ['no_pengadaan' => 'S260088A', 'nama_pengadaan' => 'Pengadaan Forklift Elektrik Gudang', 'tahun' => '2026'],
                    ['no_pengadaan' => 'S260089B', 'nama_pengadaan' => 'Pengadaan Pallet Heavy Duty Kencana', 'tahun' => '2026'],
                ],
            ],
            [
                'kode_bantex' => 'SCM/AC/R-02/01/FIN/ACC/01',
                'tim' => 'AC',
                'nomor_rak' => 'Rak 2',
                'baris' => 'Baris 1',
                'urutan_bantex' => 'Bantex 01',
                'keterangan_lokasi' => 'Kelompok AC, Rak 2, Baris 1, Fungsi FIN, Tim ACC, Bantex ke-1',
                'status' => 'tersedia',
                'dokumen' => [
                    ['no_pengadaan' => 'S240030A', 'nama_pengadaan' => 'Pengadaan Software ERP Lisensi Tahunan', 'tahun' => '2024'],
                    ['no_pengadaan' => 'S240031B', 'nama_pengadaan' => 'Pengadaan Server Storage Ruang Arsip', 'tahun' => '2024'],
                    ['no_pengadaan' => 'S240032C', 'nama_pengadaan' => 'Pengadaan UPS Rackmount 10kVA', 'tahun' => '2024'],
                    ['no_pengadaan' => 'S240033D', 'nama_pengadaan' => 'Pengadaan Switch Managed 48 Port', 'tahun' => '2024'],
                ],
                'history' => [
                    [
                        'nama_peminjam' => 'Dwi Cahyono',
                        'keperluan' => 'Pembaruan kontrak software lisensi ERP',
                        'tanggal_pinjam' => Carbon::now()->subDays(14),
                        'tanggal_kembali' => Carbon::now()->subDays(11),
                    ],
                ],
            ],
            [
                'kode_bantex' => 'SCM/AC/R-02/02/FIN/ACC/02',
                'tim' => 'AC',
                'nomor_rak' => 'Rak 2',
                'baris' => 'Baris 2',
                'urutan_bantex' => 'Bantex 02',
                'keterangan_lokasi' => 'Kelompok AC, Rak 2, Baris 2, Fungsi FIN, Tim ACC, Bantex ke-2',
                'status' => 'dipinjam',
                'dokumen' => [
                    ['no_pengadaan' => 'S250060A', 'nama_pengadaan' => 'Pengadaan Kendaraan Operasional Logistik', 'tahun' => '2025'],
                    ['no_pengadaan' => 'S250061B', 'nama_pengadaan' => 'Pengadaan Asuransi Armada Pengiriman', 'tahun' => '2025'],
                ],
                'current_loan' => [
                    'nama_peminjam' => 'Siti Rahmawati',
                    'keperluan' => 'Verifikasi polis asuransi armada pengiriman 2025',
                    'tanggal_pinjam' => Carbon::now()->subDays(2),
                ],
            ],
            [
                'kode_bantex' => 'SCM/AC/R-02/03/PROC/PUR/01',
                'tim' => 'AC',
                'nomor_rak' => 'Rak 2',
                'baris' => 'Baris 3',
                'urutan_bantex' => 'Bantex 01',
                'keterangan_lokasi' => 'Kelompok AC, Rak 2, Baris 3, Fungsi PROC, Tim PUR, Bantex ke-1',
                'status' => 'tersedia',
                'dokumen' => [
                    ['no_pengadaan' => 'S260010A', 'nama_pengadaan' => 'Pengadaan Coil Baja Zincalume G550', 'tahun' => '2026'],
                    ['no_pengadaan' => 'S260011B', 'nama_pengadaan' => 'Pengadaan Pelumas Mesin Cold Roll', 'tahun' => '2026'],
                    ['no_pengadaan' => 'S260012C', 'nama_pengadaan' => 'Pengadaan Sparepart Cutting Blade', 'tahun' => '2026'],
                ],
            ],
        ];

        foreach ($bantexData as $item) {
            $dokumenList = $item['dokumen'];
            $currentLoan = $item['current_loan'] ?? null;
            $historyList = $item['history'] ?? [];

            unset($item['dokumen'], $item['current_loan'], $item['history']);

            $bantex = Bantex::create($item);

            foreach ($dokumenList as $doc) {
                $bantex->dokumen()->create($doc);
            }

            foreach ($historyList as $hist) {
                $bantex->peminjaman()->create($hist);
            }

            if ($currentLoan) {
                $bantex->peminjaman()->create($currentLoan);
            }
        }
    }
}
