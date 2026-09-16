<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'admin112',
                'email' => 'admin11@gmail.com',
                'password' => Hash::make('admin1123'),
                'role' => 'admin',
                'no_hp' => '085624745987',
                'alamat' => 'Bandung, West Java',
            ],
            [
                'name' => 'petugas11',
                'email' => 'petugas11@gmail.com',
                'password' => Hash::make('petugas123'),
                'role' => 'petugas',
                'no_hp' => '0856224745986',
                'alamat' => 'Baleendah, Bandung',
            ],
            [
                'name' => 'richy rivano',
                'email' => 'richyrivano1@gmail.com',
                'password' => Hash::make('richyrivano1'),
                'role' => 'peminjam',
                'no_hp' => '085624745985',
                'alamat' => 'Bandung, West Java',
            ],
            [
                 'name' => 'rasya zicko',
                'email' => 'rasyazicko1@gmail.com',
                'password' => Hash::make('zicko1111'),
                'role' => 'peminjam',
                'no_hp' => '085624745983',
                'alamat' => 'Bandung, West Java',
            ],
        ];
        foreach ($users as $user) {
            User::create($user);
        }
    }
}
