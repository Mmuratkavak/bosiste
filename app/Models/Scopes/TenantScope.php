<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use App\Models\Tenant;

/**
 * Otomatik Tenant İzolasyonu Scope'u
 * 
 * Modele uygulandığında, tüm sorgular otomatik olarak
 * sadece mevcut tenant'ın verilerini getirir.
 */
class TenantScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Öncelik: middleware tarafından ayarlanmış current tenant
        $current = Tenant::getCurrent();
        if ($current) {
            $builder->where('tenant_id', $current->id);
            return;
        }

        // Admin panelinde tenant'ı seçmişse, o tenant'ı kullan
        $tenantId = request()->get('tenant_id');

        // Değilse, authenticated user'ın ilk tenant'ını kullan
        if (!$tenantId && auth()->check()) {
            $user = auth()->user();
            $tenantId = $user->tenants()->first()?->id;
        }

        if ($tenantId) {
            $builder->where('tenant_id', $tenantId);
        }
    }
}
