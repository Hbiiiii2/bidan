<?php
namespace Database\Seeders;

use App\Models\Vaccine;
use Illuminate\Database\Seeder;

class VaccineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vaccines = [
            ['name' => 'Hepatitis B', 'recommended_age' => '0-1 bulan', 'description' => 'Imunisasi dasar Hepatitis B.'],
            ['name' => 'BCG', 'recommended_age' => '0-2 bulan', 'description' => 'Mencegah tuberkulosis berat.'],
            ['name' => 'Polio', 'recommended_age' => '0-4 bulan', 'description' => 'Mencegah poliomyelitis.'],
            ['name' => 'DPT-HB-Hib', 'recommended_age' => '2-4 bulan', 'description' => 'Kombinasi DPT, Hepatitis B, dan Hib.'],
            ['name' => 'MR', 'recommended_age' => '9 bulan', 'description' => 'Mencegah campak dan rubella.'],
        ];

        foreach ($vaccines as $vaccine) {
            Vaccine::updateOrCreate(
                ['name' => $vaccine['name']],
                $vaccine
            );
        }
    }
}
