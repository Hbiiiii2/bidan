<?php

namespace Database\Seeders;

use App\Models\Child;
use App\Models\Immunization;
use App\Models\User;
use App\Models\Vaccine;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ImmunizationRecallSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parent = User::where('email', '15240133@bsi.ac.id')->first();
        $child = Child::where('user_id', optional($parent)->id)->first();

        if (! $parent || ! $child) {
            return;
        }

        $now = now()->startOfDay();

        $seedPlan = [
            [
                'vaccine' => 'Hepatitis B',
                'date' => $now->copy()->subDays(30),
                'notes' => 'Seed data untuk recall Hepatitis B dosis berikutnya.',
            ],
            [
                'vaccine' => 'DPT-HB-Hib',
                'date' => $now->copy()->subDays(30),
                'notes' => 'Seed data untuk recall DPT-HB-Hib dosis berikutnya.',
            ],
            [
                'vaccine' => 'Polio',
                'date' => $now->copy()->subDays(60),
                'notes' => 'Seed data untuk recall Polio dosis berikutnya.',
            ],
            [
                'vaccine' => 'MR',
                'date' => $now->copy()->subDays(180),
                'notes' => 'Seed data untuk recall MR booster.',
            ],
            [
                'vaccine' => 'BCG',
                'date' => $now->copy()->subDays(7),
                'notes' => 'BCG tidak punya recall, ini hanya data dasar.',
            ],
        ];

        foreach ($seedPlan as $item) {
            $vaccine = Vaccine::where('name', $item['vaccine'])->first();

            if (! $vaccine) {
                continue;
            }

            Immunization::updateOrCreate(
                [
                    'child_id' => $child->id,
                    'vaccine_id' => $vaccine->id,
                    'midwife_id' => User::where('email', 'midwife@gmail.com')->value('id'),
                    'date' => $item['date']->toDateString(),
                ],
                [
                    'notes' => $item['notes'],
                    'immunized_at' => Carbon::parse($item['date']->toDateString() . ' 09:00:00'),
                ]
            );
        }
    }
}