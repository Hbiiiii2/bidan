<?php
namespace Database\Seeders;

use App\Models\Child;
use App\Models\Immunization;
use App\Models\User;
use App\Models\Vaccine;
use Illuminate\Database\Seeder;

class ImmunizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $child = Child::first();
        $vaccine = Vaccine::where('name', 'Hepatitis B')->first();
        $midwife = User::where('email', 'midwife@gmail.com')->first();

        if (! $child || ! $vaccine || ! $midwife) {
            return;
        }

        Immunization::updateOrCreate(
            [
                'child_id'   => $child->id,
                'vaccine_id' => $vaccine->id,
                'midwife_id' => $midwife->id,
                'date'       => now()->toDateString(),
            ],
            [
                'notes' => 'Imunisasi awal untuk bayi, tercatat dalam jadwal layanan.',
            ]
        );
    }
}
