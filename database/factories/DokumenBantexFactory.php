<?php

namespace Database\Factories;

use App\Models\Bantex;
use App\Models\DokumenBantex;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DokumenBantex>
 */
class DokumenBantexFactory extends Factory
{
    protected $model = DokumenBantex::class;

    public function definition(): array
    {
        $year = fake()->numberBetween(2022, 2026);
        $shortYear = sprintf('%02d', $year % 100);
        $randomNum = fake()->numberBetween(10000, 99999);
        $char = fake()->randomElement(['A', 'B', 'C', 'X', 'Y']);

        return [
            'bantex_id' => Bantex::factory(),
            'no_pengadaan' => "S{$shortYear}{$randomNum}{$char}",
            'nama_pengadaan' => fake()->randomElement([
                'Pengadaan Material Atap Galvalum',
                'Pengadaan Rangka Baja Ringan Truss',
                'Pengadaan Mesin Roll Forming Spandek',
                'Pengadaan Baut & Fastener Kencana',
                'Pengadaan Genset Ruang Produksi',
                'Pengadaan Laptop & IT Ruang Arsip',
                'Pengadaan AC Split Ruang Server',
                'Pengadaan Forklift Gudang Bahan Baku',
                'Pengadaan Rak Heavy Duty Ruang Arsip',
            ]),
            'tahun' => (string) $year,
        ];
    }
}
