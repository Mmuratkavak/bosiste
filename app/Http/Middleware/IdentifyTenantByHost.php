<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Tenant;
use Illuminate\Http\Request;

/**
 * Identify tenant by host (subdomain or custom domain).
 *
 * Resolution order:
 * 1. Exact match on tenants.domain
 * 2. If host is a subdomain of the MAIN_DOMAIN, match subdomain to business_profile.slug
 */
class IdentifyTenantByHost
{
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();

        // 1) Exact custom domain match
        $tenant = Tenant::where('domain', $host)->first();

        // 2) Subdomain match against business_profile.slug
        if (! $tenant) {
            $mainDomain = config('app.main_domain') ?: env('APP_MAIN_DOMAIN');
            if ($mainDomain && str_ends_with($host, '.' . $mainDomain)) {
                $parts = explode('.', $host);
                $subdomain = $parts[0] ?? null;
                if ($subdomain) {
                    $tenant = Tenant::whereHas('businessProfile', function ($q) use ($subdomain) {
                        $q->where('slug', $subdomain);
                    })->first();
                }
            }
        }

        if ($tenant) {
            Tenant::setCurrent($tenant);
            // also attach to request for convenience
            $request->attributes->set('tenant', $tenant);
            app()->instance('tenant', $tenant);
        }

        return $next($request);
    }
}
