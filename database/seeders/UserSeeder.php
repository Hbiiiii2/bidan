<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name'     => 'Admin',
                'email'    => 'admin@gmail.com',
                'password' => 'password',
                'role'     => 'admin',
            ],
            [
                'name'     => 'Bidan',
                'email'    => 'midwife@gmail.com',
                'password' => 'password',
                'role'     => 'midwife',
            ],
            [
                'name'     => 'Udin',
                'email'    => 'udin@gmail.com',
                'password' => 'password',
                'role'     => 'parent',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name'     => $userData['name'],
                    'role'     => $userData['role'],
                    'password' => Hash::make($userData['password']),
                ]
            );

            $user->syncRoles([$userData['role']]);
        }
    }
}
