<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SalesUserSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            DB::table('users')->insert([
                'name' => "Sales User $i",
                'email' => "sales{$i}@example.com",
                'username' => "sales{$i}",
                'password' => Hash::make('123'),
                'role' => 'Sales',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
