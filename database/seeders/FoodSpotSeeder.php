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
                'address' => 'Leof. Mesogeion 123, Athens',
                'description' => 'Cozy spot for traditional Greek dishes and grilled meats.',
                'category' => 'Tavern',
                'info_link' => 'https://maps.app.goo.gl/example1',
                'rating' => 4.7,
            ],
            [
                'name' => 'Pizzeria Napoli',
                'address' => 'Ermou 45, Athens',
                'description' => 'Authentic Italian pizzas with a Greek twist.',
                    'category' => 'Pizzeria',
                'info_link' => 'https://maps.app.goo.gl/example2',
                'rating' => 4.5,
            ],
            [
                'name' => 'Brunch & Co',
                'address' => 'Syntagma Square 10, Athens',
                'description' => 'Modern café offering delicious Greek-style brunch options.',
                'category' => 'Brunch',
                'info_link' => 'https://maps.app.goo.gl/example3',
                'rating' => 4.8,
                'category' => 'Brunch'
            ],
            [
                'name' => 'Souvlaki Street',
                'address' => 'Monastiraki 78, Athens',
                'description' => 'The best souvlaki in town, serving since 1980.',
                'category' => 'Fast Food',
                'info_link' => 'https://maps.app.goo.gl/example4',
                'rating' => 4.6,
            ],
        ];

        foreach ($foodSpots as $foodSpot) {
            FoodSpot::create($foodSpot);
        }
    }
}