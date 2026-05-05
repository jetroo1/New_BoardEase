<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Review;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PropertyController extends Controller
{
    public function index()
    {
        $properties = Property::approved()->latest()->get();
        return view('properties.index', compact('properties'));
    }

    public function create()
    {
        return view('properties.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'address'     => 'required|string',
            'price'       => 'required|numeric|min:0',
            'room_type'   => 'required|string',
            'amenities'   => 'nullable|array',
            'latitude'    => 'nullable|numeric|between:-90,90',
            'longitude'   => 'nullable|numeric|between:-180,180',
            'image'       => 'nullable|image|max:2048',
        ]);

        $data              = $request->except('image');
        $data['amenities'] = implode(',', $request->input('amenities', []));
        $data['user_id']   = Auth::id();
        $data['is_approved'] = false;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('properties', 'public');
        }

        Property::create($data);
        return redirect()->route('owner.properties')
            ->with('success', 'Property listed! Pending admin approval.');
    }

    public function show($id)
    {
        $property    = Property::with('bookings')->findOrFail($id);
        $isFavorited = Auth::check()
            ? Favorite::where('user_id', Auth::id())->where('property_id', $id)->exists()
            : false;

        $reviews   = Review::with('user')->where('property_id', $property->id)->latest()->get();
        $avgRating = $reviews->count() ? round($reviews->avg('rating'), 1) : null;

        return view('properties.show', compact('property', 'isFavorited', 'reviews', 'avgRating'));
    }

    public function update(Request $request, $id)
    {
        $property = Property::findOrFail($id);

        if ($property->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'title'       => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'address'     => 'sometimes|string',
            'price'       => 'sometimes|numeric|min:0',
            'room_type'   => 'sometimes|string',
            'amenities'   => 'nullable|array',
            'latitude'    => 'nullable|numeric|between:-90,90',
            'longitude'   => 'nullable|numeric|between:-180,180',
            'image'       => 'nullable|image|max:2048',
        ]);

        $data = $request->except('image', 'amenities');
        $data['amenities'] = implode(',', $request->input('amenities', []));
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('properties', 'public');
        }

        $property->update($data);
        return back()->with('success', 'Property updated.');
    }

    public function destroy($id)
    {
        $property = Property::findOrFail($id);

        if ($property->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $property->delete();
        return redirect()->route('owner.properties')->with('success', 'Property deleted.');
    }
}