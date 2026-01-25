<?php

namespace App\Filament\Resources\MediaFileResource\Pages;

use App\Filament\Resources\MediaFileResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateMediaFile extends CreateRecord
{
    protected static string $resource = MediaFileResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Boş dosya arraylerini temizle
        $data = array_filter($data, fn($value) => !empty($value));
        
        // Her tab için ayrı dosyaları işle
        $files = [];
        
        if (!empty($data['logo_files'])) {
            foreach ((array)$data['logo_files'] as $file) {
                $files[] = ['path' => $file, 'type' => 'logo'];
            }
        }
        if (!empty($data['cover_files'])) {
            foreach ((array)$data['cover_files'] as $file) {
                $files[] = ['path' => $file, 'type' => 'cover'];
            }
        }
        if (!empty($data['gallery_files'])) {
            foreach ((array)$data['gallery_files'] as $index => $file) {
                $files[] = ['path' => $file, 'type' => 'gallery', 'order' => $index];
            }
        }
        if (!empty($data['360_files'])) {
            foreach ((array)$data['360_files'] as $index => $file) {
                $files[] = ['path' => $file, 'type' => '360', 'order' => $index];
            }
        }

        if (empty($files)) {
            \Filament\Notifications\Notification::make()
                ->danger()
                ->title('Hata')
                ->body('Lütfen en az bir görsel yükleyin.')
                ->persistent()
                ->send();
            
            $this->halt();
        }

        // Dosyaları daha sonra oluşturmak için form verisine ekle
        $data['files_to_create'] = $files;

        return $data;
    }

    /**
     * Birden fazla MediaFile kaydını oluşturarak, Filament'e ilk kaydı döndür.
     */
    protected function handleRecordCreation(array $data): Model
    {
        if (empty($data['files_to_create'] ?? [])) {
            \Filament\Notifications\Notification::make()
                ->danger()
                ->title('Hata')
                ->body('Lütfen en az bir görsel seçip tekrar deneyin.')
                ->persistent()
                ->send();

            $this->halt();
        }

        $tenant = \App\Models\Tenant::find($data['tenant_id']);
        $created = [];
        $skipped = 0;

        foreach ($data['files_to_create'] ?? [] as $fileData) {
            // Limit kontrolü
            $currentCount = \App\Models\MediaFile::getCurrentCount($tenant, $fileData['type']);
            $limit = \App\Models\MediaFile::getUploadLimit($tenant, $fileData['type']);

            if ($currentCount >= $limit) {
                $skipped++;
                continue;
            }

            // Logo ve cover için mevcut dosyayı sil (her zaman tek kayıt kalsın)
            if (in_array($fileData['type'], ['logo', 'cover'])) {
                $existing = \App\Models\MediaFile::where('tenant_id', $tenant->id)
                    ->where('type', $fileData['type'])
                    ->get();

                foreach ($existing as $old) {
                    \Storage::disk('public')->delete($old->path);
                    \Storage::disk('public')->delete($old->thumbnail_path);
                    \Storage::disk('public')->delete($old->webp_path);
                    $old->delete();
                }
            }

            $mediaFile = \App\Models\MediaFile::create([
                'tenant_id' => $data['tenant_id'],
                'type' => $fileData['type'],
                'path' => $fileData['path'],
                'order' => $fileData['order'] ?? 0,
            ]);

            dispatch(new \App\Jobs\OptimizeImage($mediaFile->path, 'public', $mediaFile->id));

            $created[] = $mediaFile;
        }

        if ($skipped > 0) {
            \Filament\Notifications\Notification::make()
                ->warning()
                ->title('Bazı dosyalar atlandı')
                ->body("{$skipped} dosya limit nedeniyle atlandı.")
                ->send();
        }

        if (empty($created)) {
            \Filament\Notifications\Notification::make()
                ->danger()
                ->title('Yükleme yapılamadı')
                ->body('Tüm dosyalar plan limitleri nedeniyle atlandı. Lütfen bazı görselleri silin veya planınızı yükseltin.')
                ->persistent()
                ->send();

            $this->halt();
        }

        return $created[0];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Görseller başarıyla yüklendi!';
    }
}
