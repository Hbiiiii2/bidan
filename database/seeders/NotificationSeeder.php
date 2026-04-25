<?php
namespace Database\Seeders;

use App\Models\Child;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parent = User::where('email', 'udin@gmail.com')->first();
        $child = Child::where('user_id', optional($parent)->id)->first();

        if (! $parent) {
            return;
        }

        Notification::updateOrCreate(
            [
                'user_id' => $parent->id,
                'title'   => 'Pengingat Jadwal Imunisasi',
            ],
            [
                'child_id' => optional($child)->id,
                'message'  => 'Jangan lupa bawa Alya ke jadwal imunisasi sesuai reservasi.',
                'type'     => 'reminder',
                'is_read'  => false,
            ]
        );
    }
}
