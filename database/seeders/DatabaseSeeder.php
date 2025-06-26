<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Database\Seeders\FoodSpotSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\ReviewSeeder;
use Database\Seeders\ReviewLikeSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            FoodSpotSeeder::class,
            UserSeeder::class,
            ReviewSeeder::class,
            ReviewLikeSeeder::class,
        ]);
    }
}
