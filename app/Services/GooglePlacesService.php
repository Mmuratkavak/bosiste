<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GooglePlacesService
{
    protected ?string $apiKey = null;

    public function __construct()
    {
        $this->apiKey = config('services.google.places_key') ?: env('GOOGLE_PLACES_API_KEY');
    }

    /**
     * Get Place Details from Google Places API
     * Returns null on failure.
     */
    public function getPlaceDetails(string $placeId): ?array
    {
        if (! $this->apiKey) {
            return null;
        }

        $url = 'https://maps.googleapis.com/maps/api/place/details/json';

        $response = Http::timeout(10)->get($url, [
            'place_id' => $placeId,
            'key' => $this->apiKey,
            'fields' => 'name,rating,reviews,geometry,photos,formatted_address,formatted_phone_number,website',
        ]);

        if (! $response->ok()) {
            return null;
        }

        $body = $response->json();
        if (($body['status'] ?? '') !== 'OK') {
            return null;
        }

        return $body['result'] ?? null;
    }
}
