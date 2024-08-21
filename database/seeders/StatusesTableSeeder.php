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
            'order' => 1,
            'status' => 'Редактируется',
        ]);
        Status::create([
            'order' => 2,
            'status' => 'Ожидает подтверждения',
        ]);
        Status::create([
            'order' => 3,
            'status' => 'Ожидает публикации',
        ]);
        Status::create([
            'order' => 4,
            'status' => 'Опубликовано',
        ]);
        Status::create([
            'order' => 5,
            'status' => 'Снято с публикации',
        ]);
        Status::create([
            'order' => 6,
            'status' => 'Заблокировано',
        ]);
    }
}
