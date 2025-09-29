<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash; // Penting untuk enkripsi password

class MasterAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('master_admin')->insert([
            [
                'username' => 'superadmin',
                'password' => Hash::make('password123'), // Ganti 'password123' dengan password yang aman
                'nama' => 'Super Administrator',
                'email' => 'superadmin@email.com',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}