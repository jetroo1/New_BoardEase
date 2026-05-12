<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = Property::query()
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->where('is_approved', true);

        // Keyword search
        if ($request->filled('q')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->q . '%')
                  ->orWhere('address', 'like', '%' . $request->q . '%');
            });
        }

        // Price filter
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Room type filter
        if ($request->filled('room_type')) {
            $query->where('room_type', $request->room_type);
        }

        // Amenities filter
        if ($request->filled('amenities')) {
            foreach ($request->amenities as $amenity) {
                $query->where('amenities', 'like', '%' . $amenity . '%');
            }
        }

        // Sorting
        match($request->sort) {
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            default      => $query->latest(),
        };

        $mapProperties = (clone $query)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->limit(100)
            ->get(['id', 'title', 'address', 'price', 'room_type', 'image', 'latitude', 'longitude', 'is_approved']);

        $properties = $query->paginate(12)->withQueryString();

        return view('search.index', compact('properties', 'mapProperties'));
    }

    public function suggestions(Request $request)
    {
        $q = $request->get('q', '');
        if (strlen($q) < 2) return response()->json([]);

        $results = Property::where('is_approved', true)
            ->where(function ($query) use ($q) {
                $query->where('title', 'like', '%' . $q . '%')
                    ->orWhere('address', 'like', '%' . $q . '%');
            })
            ->limit(6)
            ->get(['id', 'title', 'address', 'price', 'room_type', 'image']);
        return response()->json($results);
    }
}
