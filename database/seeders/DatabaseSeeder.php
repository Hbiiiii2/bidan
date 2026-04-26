<?php
namespace Database\Seeders;

use Database\Seeders\ScheduleSeeder;
use Database\Seeders\ServiceSeeder;
use Database\Seeders\VaccineSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // RoleSeeder::class,
            // UserSeeder::class,
            // VaccineSeeder::class,
            // ServiceSeeder::class,
            // ScheduleSeeder::class,
            // ChildSeeder::class,
            // BookingSeeder::class,
            // TransactionSeeder::class,
            // ImmunizationSeeder::class,
            \Database\Seeders\ImmunizationRecallSeeder::class,
            // NotificationSeeder::class,
        ]);
    }
}
 