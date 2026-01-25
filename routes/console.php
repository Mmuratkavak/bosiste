<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('google:import-place {placeId} {--tenant=}', function ($placeId) {
    $tenant = $this->option('tenant');
    $service = app(\App\Services\GooglePlacesService::class);

    $this->info("Fetching place details for $placeId...");
    $data = $service->getPlaceDetails($placeId);
    if (! $data) {
        $this->error('Failed to fetch place details or API key missing.');
        return 1;
    }

    $profileQuery = \App\Models\BusinessProfile::query();
    if ($tenant) {
        $profileQuery->where('tenant_id', $tenant);
    }
    $profile = $profileQuery->where('google_place_id', $placeId)->first();

    if (! $profile && $tenant) {
        $profile = \App\Models\BusinessProfile::firstOrNew(['tenant_id' => $tenant]);
    }

    if (! $profile) {
        $this->error('No BusinessProfile found for this placeId (use --tenant to create if necessary).');
        return 1;
    }

    $profile->google_place_id = $placeId;
    $profile->avg_rating = $data['rating'] ?? null;
    $profile->short_description = $data['formatted_address'] ?? $data['vicinity'] ?? $profile->short_description;

    if (!empty($data['geometry']['location'])) {
        $profile->latitude = $data['geometry']['location']['lat'];
        $profile->longitude = $data['geometry']['location']['lng'];
    }

    $gallery = $profile->gallery ?? [];
    if (!empty($data['photos'])) {
        foreach ($data['photos'] as $photo) {
            $gallery[] = ['photo_reference' => $photo['photo_reference'] ?? null, 'height' => $photo['height'] ?? null, 'width' => $photo['width'] ?? null];
        }
        $profile->gallery = $gallery;
    }

    $profile->last_synced_at = now();
    $profile->save();

    $this->info('BusinessProfile updated.');

    return 0;
})->purpose('Import a Google Place into BusinessProfile');
