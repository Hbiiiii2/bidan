<?php
namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\ScheduleSeeder;
use Database\Seeders\ServiceSeeder;
use Database\Seeders\VaccineSeeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            VaccineSeeder::class,
            ServiceSeeder::class,
            ScheduleSeeder::class,
            ChildSeeder::class,
            BookingSeeder::class,
            TransactionSeeder::class,
            ImmunizationSeeder::class,
            NotificationSeeder::class,
        ]);
    }
}
