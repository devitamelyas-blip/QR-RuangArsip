<?php

namespace Database\Factories;

use App\Models\Bantex;
use App\Models\Peminjaman;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Peminjaman>
 */
class PeminjamanFactory extends Factory
{
    protected $model = Peminjaman::class;

    public function definition(): array
    {
        $borrowDate = fake()->dateTimeBetween('-30 days', 'now');

        return [
            'bantex_id' => Bantex::factory(),
            'nama_peminjam' => fake()->name(),
            'keperluan' => fake()->sentence(8),
            'tanggal_pinjam' => $borrowDate,
            'tanggal_kembali' => null,
        ];
    }

    public function returned(): static
    {
        return $this->state(function (array $attributes) {
            $pinjam = $attributes['tanggal_pinjam'] ?? now()->subDays(3);
            return [
                'tanggal_kembali' => fake()->dateTimeBetween($pinjam, 'now'),
            ];
        });
    }
}

