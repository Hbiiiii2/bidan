<?php
namespace Database\Seeders;

use App\Models\Child;
use App\Models\User;
use Illuminate\Database\Seeder;

class ChildSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parent = User::where('email', 'udin@gmail.com')->first();

        if (! $parent) {
            return;
        }

        $children = [
            [
                'name'       => 'Alya',
                'birth_date' => '2023-10-10',
                'gender'     => 'female',
            ],
        ];

        foreach ($children as $childData) {
            Child::updateOrCreate(
                [
                    'user_id' => $parent->id,
                    'name'    => $childData['name'],
                ],
                array_merge($childData, ['user_id' => $parent->id])
            );
        }
    }
}
