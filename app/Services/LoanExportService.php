<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LoanExportService
{
    public function exportXlsx(Collection $records, array $filters = [], int $dueDays = 7): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Riwayat Peminjaman SCM');

        $spreadsheet->getProperties()
            ->setCreator('Pelita Air SCM')
            ->setLastModifiedBy('Pelita Air SCM')
            ->setTitle('Riwayat Peminjaman Dokumen SCM')
            ->setSubject('Audit Log Peminjaman Berkas Arsip');

        $sheet->setCellValue('A1', 'PT PELITA AIR SERVICE');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->getColor()->setRGB('0A2540');

        $sheet->setCellValue('A2', 'DIVISI SUPPLY CHAIN MANAGEMENT (SCM) — RUANG ARSIP DOKUMEN PENGADAAN');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(10)->getColor()->setRGB('1E3A8A');

        $sheet->setCellValue('A3', 'REKAPITULASI & AUDIT LOG SIRKULASI PEMINJAMAN BERKAS FISIK');
        $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(9)->getColor()->setRGB('475569');

        $exportTime = Carbon::now()->translatedFormat('d F Y, H:i') . ' WIB';
        $filterSummary = $this->buildFilterSummary($filters);
        $sheet->setCellValue('A4', "Dicetak pada: {$exportTime} | Total: {$records->count()} transaksi | {$filterSummary}");
        $sheet->getStyle('A4')->getFont()->setSize(9)->setItalic(true)->getColor()->setRGB('64748B');

        $headers = [
            'A' => ['title' => 'No', 'width' => 6],
            'B' => ['title' => 'Kode Bantex', 'width' => 28],
            'C' => ['title' => 'Tim SCM', 'width' => 12],
            'D' => ['title' => 'Lokasi Rak Fisik', 'width' => 24],
            'E' => ['title' => 'Daftar Berkas / Pengadaan', 'width' => 38],
            'F' => ['title' => 'Nama Peminjam', 'width' => 24],
            'G' => ['title' => 'Keperluan Peminjaman', 'width' => 36],
            'H' => ['title' => 'Waktu Pinjam', 'width' => 20],
            'I' => ['title' => 'Waktu Kembali', 'width' => 20],
            'J' => ['title' => 'Durasi (Hari)', 'width' => 14],
            'K' => ['title' => 'Status Sirkulasi', 'width' => 18],
            'L' => ['title' => 'Keterangan Kepatuhan', 'width' => 22],
        ];

        $headerRow = 6;
        $sheet->getRowDimension($headerRow)->setRowHeight(28);

        foreach ($headers as $col => $meta) {
            $cell = $col . $headerRow;
            $sheet->setCellValue($cell, $meta['title']);
            $sheet->getColumnDimension($col)->setWidth($meta['width']);
        }

        $sheet->getStyle("A{$headerRow}:L{$headerRow}")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 10,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0A2540'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '081F36'],
                ],
            ],
        ]);

        $currentRow = 7;
        $no = 1;

        foreach ($records as $item) {
            $isOverdue = $item->is_overdue;
            $isReturned = ! empty($item->tanggal_kembali);

            $statusText = $isReturned ? 'Sudah Kembali' : 'Sedang Dipinjam';
            $overdueText = $isOverdue ? "Terlambat (> {$dueDays} hari)" : 'Tepat Waktu';

            $dokumenList = $item->bantex?->dokumen?->map(function ($d) {
                return $d->no_pengadaan ? "{$d->no_pengadaan} - {$d->nama_pengadaan}" : $d->nama_pengadaan;
            })->filter()->implode("\n") ?: ($item->bantex?->keterangan_lokasi ?? '-');

            $pinjamFormatted = $item->tanggal_pinjam ? $item->tanggal_pinjam->format('d/m/Y H:i') : '-';
            $kembaliFormatted = $item->tanggal_kembali ? $item->tanggal_kembali->format('d/m/Y H:i') : 'Belum Kembali';

            $sheet->setCellValue("A{$currentRow}", $no++);
            $sheet->setCellValue("B{$currentRow}", $item->bantex?->kode_bantex ?? '-');
            $sheet->setCellValue("C{$currentRow}", $item->bantex?->tim ?? '-');
            $sheet->setCellValue("D{$currentRow}", $item->bantex ? "{$item->bantex->nomor_rak}, {$item->bantex->baris}, {$item->bantex->urutan_bantex}" : '-');
            $sheet->setCellValue("E{$currentRow}", $dokumenList);
            $sheet->setCellValue("F{$currentRow}", $item->nama_peminjam);
            $sheet->setCellValue("G{$currentRow}", $item->keperluan);
            $sheet->setCellValue("H{$currentRow}", $pinjamFormatted);
            $sheet->setCellValue("I{$currentRow}", $kembaliFormatted);
            $sheet->setCellValue("J{$currentRow}", (int) $item->lama_pinjam_hari);
            $sheet->setCellValue("K{$currentRow}", $statusText);
            $sheet->setCellValue("L{$currentRow}", $overdueText);

            $rowBg = ($currentRow % 2 === 0) ? 'F8FAFC' : 'FFFFFF';
            $sheet->getStyle("A{$currentRow}:L{$currentRow}")->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $rowBg],
                ],
                'font' => [
                    'size' => 9.5,
                    'color' => ['rgb' => '1E293B'],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'E2E8F0'],
                    ],
                ],
            ]);

            $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$currentRow}")->getFont()->setBold(true);
            $sheet->getStyle("C{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("E{$currentRow}")->getAlignment()->setWrapText(true);
            $sheet->getStyle("F{$currentRow}")->getFont()->setBold(true);
            $sheet->getStyle("G{$currentRow}")->getAlignment()->setWrapText(true);
            $sheet->getStyle("H{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("I{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("J{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("K{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("L{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            if ($isOverdue) {
                $sheet->getStyle("K{$currentRow}:L{$currentRow}")->getFont()->setBold(true)->getColor()->setRGB('B91C1C');
                $sheet->getStyle("K{$currentRow}:L{$currentRow}")->getFill()->getStartColor()->setRGB('FEF2F2');
            } elseif ($isReturned) {
                $sheet->getStyle("K{$currentRow}")->getFont()->setBold(true)->getColor()->setRGB('047857');
            } else {
                $sheet->getStyle("K{$currentRow}")->getFont()->setBold(true)->getColor()->setRGB('B45309');
            }

            $currentRow++;
        }

        $sheet->freezePane('A7');
        $sheet->setAutoFilter("A{$headerRow}:L{$headerRow}");

        $filename = 'Laporan_Riwayat_Peminjaman_SCM_' . date('Y-m-d_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0, no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
        ]);
    }

    public function exportCsv(Collection $records, int $dueDays = 7): StreamedResponse
    {
        $filename = 'riwayat_peminjaman_arsip_' . date('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($records, $dueDays) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'No',
                'Kode Bantex',
                'Tim SCM',
                'Lokasi Rak',
                'Nama Peminjam',
                'Keperluan',
                'Tanggal Pinjam',
                'Tanggal Kembali',
                'Lama Pinjam (Hari)',
                'Status Sirkulasi',
                'Keterangan Terlambat',
            ]);

            $no = 1;
            foreach ($records as $item) {
                $statusText = $item->tanggal_kembali ? 'Sudah Kembali' : 'Sedang Dipinjam';
                $overdueText = $item->is_overdue ? "Terlambat (> {$dueDays} hari)" : '-';

                fputcsv($handle, [
                    $no++,
                    $item->bantex?->kode_bantex ?? '-',
                    $item->bantex?->tim ?? '-',
                    $item->bantex ? "{$item->bantex->nomor_rak}, {$item->bantex->baris}, {$item->bantex->urutan_bantex}" : '-',
                    $item->nama_peminjam,
                    $item->keperluan,
                    $item->tanggal_pinjam?->format('d/m/Y H:i') ?? '-',
                    $item->tanggal_kembali?->format('d/m/Y H:i') ?? '-',
                    $item->lama_pinjam_hari,
                    $statusText,
                    $overdueText,
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    protected function buildFilterSummary(array $filters): string
    {
        $parts = [];
        if (! empty($filters['search'])) {
            $parts[] = "Kata Kunci: \"{$filters['search']}\"";
        }
        if (! empty($filters['status']) && $filters['status'] !== 'semua') {
            $parts[] = "Status: " . ucfirst($filters['status']);
        }
        if (! empty($filters['date_from']) || ! empty($filters['date_to'])) {
            $from = $filters['date_from'] ?: 'Awal';
            $to = $filters['date_to'] ?: 'Sekarang';
            $parts[] = "Periode: {$from} s/d {$to}";
        }
        return empty($parts) ? 'Semua Riwayat Transaksi' : implode(' | ', $parts);
    }
}
