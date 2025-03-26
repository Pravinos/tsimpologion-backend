<?php

namespace Database\Seeders;

use App\Models\FoodSpot;
use Illuminate\Database\Seeder;

class FoodSpotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $foodSpots = [
            [
                'name' => 'Taverna To Koutouki',
                'category' => 'Taverna',
                'city' => 'Athens',
                'address' => 'Leof. Mesogeion 123, 56777',
                'description' => 'Cozy spot for traditional Greek dishes and grilled meats.',
                'info_link' => 'https://maps.app.goo.gl/example1',
                'rating' => 4.7,
            ],
            [
                'name' => 'Pizzeria Napoli',
                'category' => 'Pizza',
                'city' => 'Athens',
                'address' => 'Ermou 45, 67888',
                'description' => 'Authentic Italian pizzas with a Greek twist.',
                'info_link' => 'https://maps.app.goo.gl/example2',
                'rating' => 4.5,
            ],
            [
                'name' => 'Brunch & Co',
                'category' => 'Brunch',
                'city' => 'Athens',
                'address' => 'Syntagma Square 10, 65555',
                'description' => 'Modern café offering delicious Greek-style brunch options.',
                'info_link' => 'https://maps.app.goo.gl/example3',
                'rating' => 4.8,
            ],
            [
                'name' => 'Souvlaki Street',
                'category' => 'Mezedopoleion',
                'city' => 'Athens',
                'address' => 'Monastiraki 78, 43333',
                'description' => 'The best souvlaki in town, serving since 1980.',
                'info_link' => 'https://maps.app.goo.gl/example4',
                'rating' => 4.6,
            ],
        ];

        foreach ($foodSpots as $foodSpot) {
            FoodSpot::create($foodSpot);
        }
    }
}