<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoritesController extends Controller
{
    public function index()
    {
        $favorites = Favorite::with('property')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('favorites.index', compact('favorites'));
    }

    public function toggle(Request $request, $propertyId)
    {
        $userId = Auth::id();
        $existing = Favorite::where('user_id', $userId)
                            ->where('property_id', $propertyId)
                            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['favorited' => false]);
        }

        Favorite::create(['user_id' => $userId, 'property_id' => $propertyId]);
        return response()->json(['favorited' => true]);
    }
}