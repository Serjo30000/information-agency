<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Status::create([
            'order' => 0,
            'status' => 'Редактируется',
        ]);
        Status::create([
            'order' => 0,
            'status' => 'Ожидает подтверждения',
        ]);
        Status::create([
            'order' => 1,
            'status' => 'Ожидает публикации',
        ]);
        Status::create([
            'order' => 1,
            'status' => 'Опубликовано',
        ]);
        Status::create([
            'order' => 0,
            'status' => 'Снято с публикации',
        ]);
        Status::create([
            'order' => 1,
            'status' => 'Заблокировано',
        ]);
    }
}
