<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\IdentifyTenantByHost;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;

Route::middleware([IdentifyTenantByHost::class])->group(function () {
    Route::get('/', function () {
        return view('welcome');
    });

    // Public tenant pages will be added here and will have tenant resolved by host
});

// Impersonation route for super_admins to login as tenant owner (convenience for local/admin use)
Route::get('/admin/tenants/{tenant}/impersonate', function (Tenant $tenant) {
    $user = Auth::user();
    if (! $user || ! $user->hasRole('super_admin')) {
        abort(403, 'Sadece süper adminler bu işlemi yapabilir.');
    }

    // store impersonator id so we can return later
    session(['impersonator_id' => $user->id]);

    if (! $tenant->owner_id) {
        return redirect()->back()->with('error', 'Bu işletmenin sahibi atanmadı.');
    }

    Auth::loginUsingId($tenant->owner_id);

    return redirect('/admin');
})->name('admin.tenants.impersonate');

// End impersonation and return to original super_admin
Route::get('/admin/impersonation/leave', function () {
    if (! Auth::check()) {
        return redirect('/admin');
    }
    
    $impersonatorId = session()->pull('impersonator_id');
    if (! $impersonatorId) {
        return redirect('/admin');
    }

    Auth::loginUsingId($impersonatorId);
    return redirect('/admin');
})->name('admin.impersonation.leave');
