<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddNewRolesDefaultAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('login', 'sa')->first();

        if ($user) {
            $user->assignRole('censor', 'first_page');
        } else {
            $this->command->info('Пользователь с логином "sa" не найден.');
        }
    }
}
