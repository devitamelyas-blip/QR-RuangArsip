<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@arsip.internal'],
            [
                'name' => 'Admin Ruang Arsip',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );
    }
}

