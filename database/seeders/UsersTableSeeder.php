<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Enums\UserRoleEnum;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
            User::create([
                'first_name' => 'Med',
                'last_name' => 'Ism',
                'email' => 'mamiotban@gmail.com',
                'phone' => '77204014',
                'role' => UserRoleEnum::Administrateur,
                'first_connection' => 0,
                'password' => Hash::make('momima456'),
            ]);

            
    }
}
