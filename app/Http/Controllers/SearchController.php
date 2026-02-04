<?php

namespace App\Http\Controllers;

use App\Models\BusinessProfile;
use Illuminate\Http\Request;

class SearchController
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $results = BusinessProfile::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qbuilder) use ($q) {
                    $qbuilder->where('name', 'like', "%{$q}%")
                        ->orWhere('short_description', 'like', "%{$q}%")
                        ->orWhere('description', 'like', "%{$q}%")
                        ->orWhere('address', 'like', "%{$q}%")
                        ->orWhere('category', 'like', "%{$q}%");
                });
            })
            ->with('tenant')
            ->take(50)
            ->get();

        return view('search.results', [
            'query' => $q,
            'results' => $results,
        ]);
    }
}
