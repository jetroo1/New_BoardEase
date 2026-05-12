<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\User;
use App\Notifications\PropertyUpdatedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PropertiesController extends Controller
{
    public function index()
    {
        $properties = Property::where('user_id', Auth::id())
            ->withCount('bookings')->latest()->get();
        return view('owner.properties.index', compact('properties'));
    }

    public function create()
    {
        return view('owner.properties.create');
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
            'photos.*'    => 'nullable|image|max:2048',
        ]);

        $data                = $request->except(['image', 'photos', 'amenities']);
        $data['amenities']   = implode(',', $request->input('amenities', []));
        $data['user_id']     = Auth::id();
        $data['is_approved'] = false;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('properties', 'public');
        }

        if ($request->hasFile('photos')) {
            $paths = [];
            foreach ($request->file('photos') as $photo) {
                $paths[] = $photo->store('properties', 'public');
            }
            $data['photos'] = json_encode(array_slice($paths, 0, 4));
        }

        Property::create($data);
        return redirect()->route('owner.properties')
            ->with('success', 'Property listed! Pending admin approval.');
    }

    public function edit($id)
    {
        $property = Property::where('user_id', Auth::id())->findOrFail($id);
        return view('owner.properties.edit', compact('property'));
    }

    public function update(Request $request, $id)
    {
        $property = Property::where('user_id', Auth::id())->findOrFail($id);

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
            'photos.*'    => 'nullable|image|max:2048',
        ]);

        $data              = $request->except(['image', 'photos', 'amenities']);
        $data['amenities'] = implode(',', $request->input('amenities', []));

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('properties', 'public');
        }

        // Merge existing kept photos + newly uploaded photos, capped at 4
        $existingPhotos = json_decode($property->photos ?? '[]', true) ?: [];
        $keepPhotos     = $request->input('keep_photos', []);
        $retained       = array_values(
            array_filter($existingPhotos, fn($p) => in_array($p, $keepPhotos))
        );

        $newPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $newPaths[] = $photo->store('properties', 'public');
            }
        }

        $merged         = array_slice(array_merge($retained, $newPaths), 0, 4);
        $data['photos'] = json_encode($merged);

        $property->update($data);
        $changedFields = collect($property->getChanges())
            ->keys()
            ->reject(fn ($field) => $field === 'updated_at')
            ->values()
            ->all();

        if ($property->is_approved && !empty($changedFields)) {
            $this->notifyInterestedTenants($property->fresh(), $changedFields);
        }

        return redirect()->route('owner.properties')->with('success', 'Property updated.');
    }

    public function destroy($id)
    {
        Property::where('user_id', Auth::id())->findOrFail($id)->delete();
        return redirect()->route('owner.properties')->with('success', 'Property deleted.');
    }

    private function notifyInterestedTenants(Property $property, array $changedFields): void
    {
        User::where('role', 'tenant')
            ->where('id', '!=', $property->user_id)
            ->where(function ($query) use ($property) {
                $query->whereHas('favorites', fn ($favorite) => $favorite->where('property_id', $property->id))
                    ->orWhereHas('bookings', fn ($booking) => $booking->where('property_id', $property->id));
            })
            ->get()
            ->filter(fn (User $tenant) => $tenant->wantsNotification('new_listings_nearby'))
            ->each(fn (User $tenant) => $tenant->notify(new PropertyUpdatedNotification($property, $changedFields)));
    }
}
