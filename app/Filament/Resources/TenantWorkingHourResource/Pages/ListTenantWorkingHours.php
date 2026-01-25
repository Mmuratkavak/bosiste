<?php

namespace App\Filament\Resources\TenantWorkingHourResource\Pages;

use App\Filament\Resources\TenantWorkingHourResource;
use App\Models\Tenant;
use App\Models\TenantWorkingHour;
use App\Models\User;
use Filament\Resources\Pages\ListRecords;

class ListTenantWorkingHours extends ListRecords
{
    protected static string $resource = TenantWorkingHourResource::class;

    public function mount(): void
    {
        /** @var User|null $user */
        $user = auth()->user();

        if (! $user) {
            parent::mount();
            return;
        }

        // Kullanıcının ilk tenant'ını, yoksa sistemdeki ilk tenant'ı al
        $tenant = $user->tenants()->first() ?? Tenant::first();

        if ($tenant) {
            $record = TenantWorkingHour::firstOrCreate([
                'tenant_id' => $tenant->id,
            ]);

            $this->redirect(static::getResource()::getUrl('edit', ['record' => $record]));
            return;
        }

        parent::mount();
    }
}
