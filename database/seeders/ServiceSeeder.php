<?php
namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name'                 => 'Konsultasi Tumbuh Kembang',
                'tag'                  => 'konsultasi',
                'description'          => 'Konsultasi perkembangan anak sesuai usia dan kebutuhan gizi.',
                'available_date'       => now()->toDateString(),
                'available_from_date'  => now()->toDateString(),
                'available_until_date' => now()->addMonths(3)->toDateString(),
                'available_start_time' => '08:00:00',
                'available_end_time'   => '16:00:00',
                'type'                 => 'consultation',
                'price'                => 100000,
            ],
            [
                'name'                 => 'Konsultasi Menyusui',
                'tag'                  => 'laktasi',
                'description'          => 'Pendampingan ibu menyusui, teknik menyusui, dan evaluasi laktasi.',
                'available_date'       => now()->toDateString(),
                'available_from_date'  => now()->toDateString(),
                'available_until_date' => now()->addMonths(3)->toDateString(),
                'available_start_time' => '08:00:00',
                'available_end_time'   => '16:00:00',
                'type'                 => 'consultation',
                'price'                => 120000,
            ],
            [
                'name'                 => 'Imunisasi Dasar',
                'tag'                  => 'imunisasi',
                'description'          => 'Layanan imunisasi dasar sesuai jadwal kesehatan anak.',
                'available_date'       => now()->toDateString(),
                'available_from_date'  => now()->toDateString(),
                'available_until_date' => now()->addMonths(3)->toDateString(),
                'available_start_time' => '08:00:00',
                'available_end_time'   => '16:00:00',
                'type'                 => 'immunization',
                'price'                => 150000,
            ],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(
                ['name' => $service['name']],
                $service
            );
        }
    }
}
