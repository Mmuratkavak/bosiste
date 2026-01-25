<?php

namespace App\Filament\Resources\MediaFileResource\Pages;

use App\Filament\Resources\MediaFileResource;
use App\Models\MediaFile;
use App\Models\Tenant;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMediaFiles extends ListRecords
{
    protected static string $resource = MediaFileResource::class;

    protected static string $view = 'filament.resources.media-file-resource.pages.list-media-files';

    public function getCurrentTenant(): ?Tenant
    {
        /** @var User|null $user */
        $user = auth()->user();

        if (! $user) {
            return null;
        }

        $tenant = $user->tenants()->with('businessProfile')->first();

        if ($tenant) {
            return $tenant;
        }

        // Eğer kullanıcının kendi tenant'ı yoksa (örn. sadece super_admin ise),
        // sistemdeki ilk tenant'ı göster.
        return Tenant::with('businessProfile')->first();
    }

    public function getCoverImage(): ?MediaFile
    {
        $tenant = $this->getCurrentTenant();

        if (! $tenant) {
            return null;
        }

        return MediaFile::where('tenant_id', $tenant->id)
            ->where('type', 'cover')
            ->latest('created_at')
            ->first();
    }

    public function getLogoImage(): ?MediaFile
    {
        $tenant = $this->getCurrentTenant();

        if (! $tenant) {
            return null;
        }

        return MediaFile::where('tenant_id', $tenant->id)
            ->where('type', 'logo')
            ->latest('created_at')
            ->first();
    }

    public function getBusinessName(): string
    {
        $tenant = $this->getCurrentTenant();

        if (! $tenant) {
            return 'İşletme';
        }

        return $tenant->businessProfile?->name
            ?? $tenant->name
            ?? 'İşletme';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Yeni Görsel Yükle')
                ->icon('heroicon-o-cloud-arrow-up')
                ->modalHeading('Medya Yükleme')
                ->modalWidth('3xl')
                ->createAnother(false)
                ->successNotificationTitle('Yükleme Başarılı')
                ->mutateFormDataUsing(function (array $data): array {
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

                    $data['files_to_create'] = $files;
                    return $data;
                })
                ->using(function (array $data, Actions\CreateAction $action) {
                    if (empty($data['files_to_create'])) {
                        \Filament\Notifications\Notification::make()
                            ->danger()
                            ->title('Hata')
                            ->body('Lütfen en az bir görsel yükleyin.')
                            ->send();
                        
                        $action->halt();
                        return null;
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

                        // Logo ve cover için mevcut dosyayı sil
                        if (in_array($fileData['type'], ['logo', 'cover'])) {
                            $existing = \App\Models\MediaFile::where('tenant_id', $tenant->id)
                                ->where('type', $fileData['type'])
                                ->get();
                            
                            foreach ($existing as $old) {
                                // Dosyaları sunucudan sil
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

                        // Optimizasyon job'ını tetikle
                        dispatch(new \App\Jobs\OptimizeImage($mediaFile->path, 'public', $mediaFile->id));
                        
                        $created[] = $mediaFile;
                    }

                    if (!empty($created)) {
                        \Filament\Notifications\Notification::make()
                            ->success()
                            ->title('Yükleme Tamamlandı')
                            ->body(count($created) . ' adet görsel yüklendi ve optimize ediliyor.' . ($skipped > 0 ? " ({$skipped} dosya limit nedeniyle atlandı)" : ''))
                            ->send();
                    } elseif ($skipped > 0) {
                        \Filament\Notifications\Notification::make()
                            ->warning()
                            ->title('Limit Aşıldı')
                            ->body("Tüm dosyalar limit nedeniyle atlandı. Planınızı yükseltin veya mevcut dosyaları silin.")
                            ->send();
                    }

                    return $created[0] ?? null;
                }),
        ];
    }
}
