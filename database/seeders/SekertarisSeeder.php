<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SekertarisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Sekertaris Koperasi',
            'email' => 'sekertaris@koperasi.com',
            'password' => Hash::make('Sekertaris123'), // Password default
            'role' => 'sekertaris', // <-- Ini kuncinya
        ]);
    }
}