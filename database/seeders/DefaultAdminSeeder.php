<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DefaultAdminSeeder extends Seeder
{
    public function run()
    {
        $user = User::create([
            'login' => 'sa',
            'password' => Hash::make('Kuko1000'),
            'fio' => 'Первый пользователь системы',
            'phone' => '89378031770',
        ]);

        $user->assignRole('guest', 'editor', 'deleter', 'admin', 'super_admin');
    }
}
