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
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'email_verified_at' => Carbon::now()
        ]);

        // Create 2 spot owners
        User::create([
            'name' => 'George Papadopoulos',
            'email' => 'george.p@example.com',
            'password' => bcrypt('password'),
            'role' => 'spot_owner',
        ]);

        User::create([
            'name' => 'Maria Nikolaou',
            'email' => 'maria.n@example.com',
            'password' => bcrypt('password'),
            'role' => 'spot_owner',
        ]);

        // Create 5 foodies
        User::create([
            'name' => 'Nikos Dimitriou',
            'email' => 'nikos.d@example.com',
            'password' => bcrypt('password'),
            'role' => 'foodie',
        ]);

        User::create([
            'name' => 'Elena Panagiotou',
            'email' => 'elena.p@example.com',
            'password' => bcrypt('password'),
            'role' => 'foodie',
        ]);

        User::create([
            'name' => 'Yannis Antoniou',
            'email' => 'yannis.a@example.com',
            'password' => bcrypt('password'),
            'role' => 'foodie',
        ]);

        User::create([
            'name' => 'Sofia Georgiou',
            'email' => 'sofia.g@example.com',
            'password' => bcrypt('password'),
            'role' => 'foodie',
        ]);

        User::create([
            'name' => 'Kostas Alexiou',
            'email' => 'kostas.a@example.com',
            'password' => bcrypt('password'),
            'role' => 'foodie',
        ]);
    }
}
