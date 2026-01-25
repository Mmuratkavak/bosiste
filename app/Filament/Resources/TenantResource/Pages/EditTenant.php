<?php

namespace App\Filament\Resources\TenantResource\Pages;

use App\Filament\Resources\TenantResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Services\GooglePlacesService;
use App\Services\MediaService;
use App\Jobs\OptimizeImage;
use Filament\Notifications\Notification as FilamentNotification;

class EditTenant extends EditRecord
{
    protected static string $resource = TenantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('importFromGoogle')
                ->label("Google'dan İçe Aktar")
                ->action(function () {
                    $profile = $this->record->businessProfile;
                    if (! $profile || ! $profile->google_place_id) {
                        FilamentNotification::make()
                            ->title('Hata')
                            ->body('Bu işletme için bir Google Place ID bulunmuyor.')
                            ->danger()
                            ->send();
                        return;
                    }

                    $service = app(GooglePlacesService::class);
                    $mediaService = app(MediaService::class);

                    $data = $service->getPlaceDetails($profile->google_place_id);
                    if (! $data) {
                        FilamentNotification::make()
                            ->title('Hata')
                            ->body('Google verisi alınamadı. API anahtarı veya bağlantıyı kontrol edin.')
                            ->danger()
                            ->send();
                        return;
                    }

                    $profile->avg_rating = $data['rating'] ?? $profile->avg_rating;
                    $profile->short_description = $data['formatted_address'] ?? $profile->short_description;
                    if (!empty($data['geometry']['location'])) {
                        $profile->latitude = $data['geometry']['location']['lat'];
                        $profile->longitude = $data['geometry']['location']['lng'];
                    }

                    $gallery = $profile->gallery ?? [];
                    if (! empty($data['photos'])) {
                        foreach ($data['photos'] as $photo) {
                            if (! empty($photo['photo_reference'])) {
                                $path = $mediaService->downloadGooglePhoto($photo['photo_reference'], $this->record->id);
                                if ($path) {
                                    $gallery[] = $path;
                                    dispatch(new OptimizeImage($path));
                                }
                            }
                        }
                    }

                    $profile->gallery = $gallery;
                    $profile->last_synced_at = now();
                    $profile->save();

                    FilamentNotification::make()
                        ->title('Başarılı')
                        ->body('Google verileri indirildi ve optimize işlemine gönderildi.')
                        ->success()
                        ->send();
                })
                ->color('primary'),

            Actions\DeleteAction::make()
                ->label('Sil'),
        ];
    }
}
