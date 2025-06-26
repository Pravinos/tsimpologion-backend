<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create an admin user
        User::create([
            'username' => 'admin',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'phone' => '6900000000',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'email_verified_at' => Carbon::now()
        ]);

        // Create 2 spot owners
        User::create([
            'username' => 'afentiko',
            'first_name' => 'Thomas',
            'last_name' => 'Afentiko',
            'phone' => '6900000001',
            'email' => 'afentiko@example.com',
            'password' => bcrypt('password'),
            'role' => 'spot_owner',
            'email_verified_at' => Carbon::now()
        ]);

        User::create([
            'username' => 'maria.n',
            'first_name' => 'Maria',
            'last_name' => 'Nikolaou',
            'phone' => '6900000002',
            'email' => 'maria.n@example.com',
            'password' => bcrypt('password'),
            'role' => 'spot_owner',
        ]);

        // Create 5 foodies
        User::create([
            'username' => 'nikos.d',
            'first_name' => 'Nikos',
            'last_name' => 'Dimitriou',
            'phone' => '6900000003',
            'email' => 'nikos.d@example.com',
            'password' => bcrypt('password'),
            'role' => 'foodie',
        ]);

        User::create([
            'username' => 'elena.p',
            'first_name' => 'Elena',
            'last_name' => 'Panagiotou',
            'phone' => '6900000004',
            'email' => 'elena.p@example.com',
            'password' => bcrypt('password'),
            'role' => 'foodie',
        ]);

        User::create([
            'username' => 'yannis.a',
            'first_name' => 'Yannis',
            'last_name' => 'Antoniou',
            'phone' => '6900000005',
            'email' => 'yannis.a@example.com',
            'password' => bcrypt('password'),
            'role' => 'foodie',
        ]);

        User::create([
            'username' => 'sofia.g',
            'first_name' => 'Sofia',
            'last_name' => 'Georgiou',
            'phone' => '6900000006',
            'email' => 'sofia.g@example.com',
            'password' => bcrypt('password'),
            'role' => 'foodie',
        ]);

        User::create([
            'username' => 'kostas.a',
            'first_name' => 'Kostas',
            'last_name' => 'Alexiou',
            'phone' => '6900000007',
            'email' => 'kostas.a@example.com',
            'password' => bcrypt('password'),
            'role' => 'foodie',
        ]);
    }
}
