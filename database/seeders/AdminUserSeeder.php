<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => '管理者ユーザー',
            'email' => 'admin@test.com',
            'password' => Hash::make('adminpassword'),
            'role' => User::ADMIN,
            'email_verified_at' => now(),
        ]);
    }
}
