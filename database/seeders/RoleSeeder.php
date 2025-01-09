<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::factory()->create(['name' => 'admin', 'guard_name' => 'web']);
        Role::factory()->create(['name' => 'editor', 'guard_name' => 'web']);
        Role::factory()->create(['name' => 'user', 'guard_name' => 'web']);
    }
}
