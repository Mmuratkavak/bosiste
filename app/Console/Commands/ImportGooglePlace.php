<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GooglePlacesService;
use App\Models\BusinessProfile;
use Illuminate\Support\Facades\Storage;

class ImportGooglePlace extends Command
{
    protected $signature = 'google:import-place {placeId} {--tenant=}';

    protected $description = 'Import Google Place details into BusinessProfile';

    public function handle(GooglePlacesService $service)
    {
        $placeId = $this->argument('placeId');
        $tenantId = $this->option('tenant');

        $this->info("Fetching place details for $placeId...");

        $data = $service->getPlaceDetails($placeId);
        if (! $data) {
            $this->error('Failed to fetch place details or API key missing.');
            return 1;
        }

        $profileQuery = BusinessProfile::query();
        if ($tenantId) {
            $profileQuery->where('tenant_id', $tenantId);
        }
        $profile = $profileQuery->where('google_place_id', $placeId)->first();

        if (! $profile && $tenantId) {
            $profile = BusinessProfile::firstOrNew(['tenant_id' => $tenantId]);
        }

        if (! $profile) {
            $this->error('No BusinessProfile found for this placeId (use --tenant to create if necessary).');
            return 1;
        }

        // Map fields
        $profile->google_place_id = $placeId;
        $profile->avg_rating = $data['rating'] ?? null;
        $profile->short_description = $data['formatted_address'] ?? $data['vicinity'] ?? $profile->short_description;

        if (!empty($data['geometry']['location'])) {
            $profile->latitude = $data['geometry']['location']['lat'];
            $profile->longitude = $data['geometry']['location']['lng'];
        }

        // Photos: keep references (photo_reference) in gallery array
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
    }
}
