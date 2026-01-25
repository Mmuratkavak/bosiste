<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class MediaService
{
    protected string $disk = 'public';

    public function tenantBasePath(int $tenantId): string
    {
        return "tenants/{$tenantId}";
    }

    public function storeCover(int $tenantId, UploadedFile $file): string
    {
        $dir = $this->tenantBasePath($tenantId) . '/cover';
        $filename = 'cover_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs($dir, $filename, $this->disk);

        // Queue optimization job (if queue available) - here we just return path
        return $path;
    }

    public function storeGalleryItem(int $tenantId, UploadedFile $file): string
    {
        $dir = $this->tenantBasePath($tenantId) . '/gallery';
        $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs($dir, $filename, $this->disk);
        return $path;
    }

    /**
     * Download photo from Google Places Photo API by photo_reference and store it.
     * Returns stored path or null.
     */
    public function downloadGooglePhoto(string $photoReference, int $tenantId, int $maxWidth = 1600): ?string
    {
        $apiKey = config('services.google.places_key') ?: env('GOOGLE_PLACES_API_KEY');
        if (! $apiKey) {
            return null;
        }

        $url = 'https://maps.googleapis.com/maps/api/place/photo';

        // Google returns a redirect to the actual image
        $response = Http::withoutVerifying()->get($url, [
            'maxwidth' => $maxWidth,
            'photoreference' => $photoReference,
            'key' => $apiKey,
        ]);

        if (! $response->ok()) {
            return null;
        }

        // The HTTP client should follow redirects, but some clients return 302 with Location header.
        $body = $response->body();
        if (empty($body)) {
            return null;
        }

        $dir = $this->tenantBasePath($tenantId) . '/gallery';
        $filename = 'google_' . substr(sha1($photoReference . time()), 0, 12) . '.jpg';

        Storage::disk($this->disk)->put($dir . '/' . $filename, $body);

        return $dir . '/' . $filename;
    }
}
