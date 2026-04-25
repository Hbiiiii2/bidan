<?php
namespace Database\Seeders;

use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $midwife = User::where('email', 'midwife@gmail.com')->first();

        if (! $midwife) {
            return;
        }

        $dates = [
            Carbon::now()->addDay()->toDateString(),
            Carbon::now()->addDays(2)->toDateString(),
            Carbon::now()->addDays(3)->toDateString(),
        ];

        foreach ($dates as $date) {
            Schedule::updateOrCreate(
                [
                    'midwife_id' => $midwife->id,
                    'date'       => $date,
                    'start_time' => '09:00:00',
                    'end_time'   => '11:00:00',
                ],
                ['quota' => 10]
            );
        }
    }
}
