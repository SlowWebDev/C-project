<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Delete existing admin user if exists
        User::where('email', 'admin@admin.com')->delete();

        // Create new admin user
        User::create([
            'name' => 'SLOW',
            'email' => 'slow@slow.slow',
            'password' => Hash::make('slow'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
