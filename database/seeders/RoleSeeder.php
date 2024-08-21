<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create(['name' => 'guest']);
        Role::create(['name' => 'editor']);
        Role::create(['name' => 'deleter']);
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'super_admin']);
    }
}
