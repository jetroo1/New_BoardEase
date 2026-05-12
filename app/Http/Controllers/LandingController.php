<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\User;

class LandingController extends Controller
{
    public function index()
    {
        $featuredProperties = Property::withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->where('is_approved', true)
            ->latest()
            ->take(6)
            ->get();

        $stats = [
            'properties' => Property::where('is_approved', true)->count(),
            'renters' => User::where('role', 'tenant')->count(),
            'owners' => User::where('role', 'owner')->count(),
        ];

        return view('welcome', compact('featuredProperties', 'stats'));
    }
}
