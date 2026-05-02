<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
                'name' => 'admin',
                'email' => 'admin@example.com',
                'password' => '123456',
                'role' => 'admin',
            ],
            [
                'name' => 'photographer',
                'email' => 'photographer@example.com',
                'password' => '123456',
                'role' => 'photographer',
            ],
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => '123456',
                'role' => 'user',
            ],
            //request
            [
                'name' => 'request',
                'email' => 'request@example.com',
                'password' => '123456',
                'role' => 'Request',
            ],
            // prepare
            [
                'name' => 'prepare',
                'email' => 'prepare@example.com',
                'password' => '123456',
                'role' => 'prepare',
            ],
            //show
            [
                'name' => 'show',
                'email' => 'show@example.com',
                'password' => '123456',
                'role' => 'show',
            ],
            //handling
            [
                'name' => 'handling',
                'email' => 'handling@example.com',
                'password' => '123456',
                'role' => 'handling',
            ],
            //contract
            [
                'name' => 'contract',
                'email' => 'contract@example.com',
                'password' => '123456',
                'role' => 'contract',
            ],
          

        ];
        foreach ($users as $user) {
            $created_user = User::firstOrCreate(
                ['email' => $user['email']], // Only check by email
                [
                    'name' => $user['name'],
                    'password' => Hash::make($user['password']),
                ]
            );

            $created_user->assignRole($user['role']);
        }
    }
}
