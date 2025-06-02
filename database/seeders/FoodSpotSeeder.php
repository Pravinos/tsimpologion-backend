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
                'name' => 'Ρελαντί',
                'category' => 'Restaurant',
                'city' => 'Thessaloniki',
                'address' => 'Ανατολικής Θράκης, 6, 544 53',
                'description' => 'Mezedes kai kali parea stin omorfi Toumpa.',
                'info_link' => 'https://www.openstreetmap.org/?mlat=40.6135283&mlon=22.9692538#map=19/40.6135283/22.9692538',
                'rating' => 4.1,
                'phone' => '+30 210 1234567',
                'business_hours' => json_encode([
                    'mon-fri' => '12:00-23:00',
                    'sat-sun' => '13:00-00:00',
                ]),
                'social_links' => json_encode([
                    'facebook' => 'https://https://www.facebook.com/relanti.thrakis',
                    'instagram' => 'https://https://www.instagram.com/relanti_mezedopwleion/',
                    'tik tok' => 'https://www.tiktok.com/@relantimezedopwleion'
                ]),
                'price_range' => '$$',
            ],
            [
                'name' => 'Πίτσα Σάκης',
                'category' => 'Pizza',
                'city' => 'Thessaloniki',
                'address' => 'Στρατού, 26, 546 40',
                'description' => 'Pizzes kai pites olo to 24wro',
                'info_link' => 'https://www.openstreetmap.org/?mlat=40.6189426&mlon=22.9580530#map=19/40.6189426/22.9580530',
                'rating' => 4,
                'phone' => '+30 210 7654321',
                'business_hours' => json_encode([
                    'mon-sun' => '00:00-23:59',
                ]),
                'social_links' => json_encode([
                    'facebook' => 'https://facebook.com/pitsasakis',
                ]),
                'price_range' => '$',
            ],
            [
                'name' => 'Μπαχτσές',
                'category' => 'Restaurant',
                'city' => 'Thessaloniki',
                'address' => 'Βασιλίσσης Όλγας, 211, 546 46',
                'description' => 'Paradosiaki taverna sthn omorfi kalamaria',
                'info_link' => 'https://www.openstreetmap.org/?mlat=40.5973239&mlon=22.9561683#map=19/40.5973239/22.9561683',
                'rating' => 4.3,
                'phone' => '+30 210 5555555',
                'business_hours' => json_encode([
                    'mon-fri' => '12:00-23:00',
                    'sat-sun' => '13:00-23:00',
                ]),
                'social_links' => json_encode([
                    'instagram' => 'https://instagram.com/mpaxtsesthessaloniki',
                ]),
                'price_range' => '$$',
            ],
            [
                'name' => 'Ραλεντάντο',
                'category' => 'Cafe',
                'city' => 'Thessaloniki',
                'address' => 'Κωνσταντινουπόλεως, 140, 546 44',
                'description' => 'Oli h thessaloniki mia parea sto magazi mas',
                'info_link' => 'https://www.openstreetmap.org/?mlat=40.6070909&mlon=22.9631237#map=19/40.6070909/22.9631237',
                'rating' => 3.9,
                'phone' => '+30 231 304 0428',
                'business_hours' => json_encode([
                    'mon-sun' => '10:00-01:00',
                ]),
                'social_links' => json_encode([
                    'facebook' => 'https://facebook.com/ralentanto',
                ]),
                'price_range' => '$',
            ],
        ];

        foreach ($foodSpots as $foodSpot) {
            FoodSpot::create($foodSpot);
        }
    }
}
