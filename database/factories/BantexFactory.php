<?php

namespace Database\Factories;

use App\Models\Bantex;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bantex>
 */
class BantexFactory extends Factory
{
    protected $model = Bantex::class;

    public function definition(): array
    {
        $tim = fake()->randomElement(['GP', 'AC']);
        $rak = sprintf('%02d', fake()->numberBetween(1, 10));
        $baris = sprintf('%02d', fake()->numberBetween(1, 5));
        $urutan = sprintf('%02d', fake()->numberBetween(1, 20));
        $fungsi = fake()->randomElement(['HCGA', 'PROC', 'FIN', 'ENG']);
        $subTim = fake()->randomElement(['GA', 'HR', 'PUR', 'ACC']);

        $kode = "SCM/{$tim}/R-{$rak}/{$baris}/{$fungsi}/{$subTim}/{$urutan}";

        return [
            'kode_bantex' => $kode,
            'slug' => Bantex::generateSlug($kode),
            'tim' => $tim,
            'nomor_rak' => "R-{$rak}",
            'baris' => "Baris {$baris}",
            'urutan_bantex' => "Bantex {$urutan}",
            'keterangan_lokasi' => "Rak {$rak}, Baris {$baris}, Posisi {$urutan}",
            'status' => 'tersedia',
        ];
    }

    public function dipinjam(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'dipinjam',
        ]);
    }
}

