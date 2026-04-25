<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'parent']);
        Role::firstOrCreate(['name' => 'midwife']);
        Role::firstOrCreate(['name' => 'admin']);
    }
}
