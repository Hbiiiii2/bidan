<?php
namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Child;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parent = User::where('email', 'udin@gmail.com')->first();
        $child = Child::where('user_id', optional($parent)->id)->first();
        $service = Service::where('tag', 'imunisasi')->orWhere('type', 'immunization')->first();
        $schedule = Schedule::first();

        if (! $parent || ! $child || ! $service || ! $schedule) {
            return;
        }

        Booking::updateOrCreate(
            [
                'user_id'     => $parent->id,
                'child_id'    => $child->id,
                'service_id'  => $service->id,
                'schedule_id' => $schedule->id,
            ],
            [
                'status' => 'paid',
                'notes'  => 'Booking untuk imunisasi dasar sesuai jadwal.',
            ]
        );
    }
}
