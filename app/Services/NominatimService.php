<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class NominatimService
{
    protected string $baseUrl = 'https://nominatim.openstreetmap.org';

    /**
     * Search for a place using Nominatim API.
     */
    public function search(string $query, array $params = []): array
    {
        $params = array_merge([
            'q' => $query,
            'format' => 'json',
            'addressdetails' => 1,
            'limit' => 5,
        ], $params);

        $response = Http::withHeaders([
            'User-Agent' => 'TsimpologionApp/1.0 (your@email.com)'
        ])->get($this->baseUrl . '/search', $params);
        $json = $response->json();
        return is_array($json) ? $json : [];
    }

    /**
     * Reverse geocode coordinates to address.
     */
    public function reverse(float $lat, float $lon, array $params = []): array
    {
        $params = array_merge([
            'lat' => $lat,
            'lon' => $lon,
            'format' => 'json',
            'addressdetails' => 1,
        ], $params);

        $response = Http::get($this->baseUrl . '/reverse', $params);
        return $response->json();
    }
}
