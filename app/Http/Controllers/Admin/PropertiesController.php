<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\User;
use App\Notifications\AdminAnnouncementNotification;
use App\Notifications\MatchingPropertyNotification;

class PropertiesController extends Controller
{
    public function index()
    {
        $properties = Property::with('owner')->withCount('bookings')->latest()->paginate(20);
        return view('admin.properties.index', compact('properties'));
    }

    public function approve($id)
    {
        $property = Property::with('owner')->findOrFail($id);
        $wasApproved = $property->is_approved;

        $property->update(['is_approved' => true]);
        $property->refresh()->load('owner');

        $property->owner?->notify(new AdminAnnouncementNotification(
            'Property approved',
            "{$property->title} is now visible to renters on BoardEase.",
            route('owner.properties.edit', $property->id),
            ['property_id' => $property->id]
        ));

        if (! $wasApproved) {
            $this->matchingTenants($property)
                ->each(fn (User $tenant) => $tenant->notify(new MatchingPropertyNotification($property)));
        }

        return back()->with('success', 'Property approved.');
    }

    public function reject($id)
    {
        $property = Property::with('owner')->findOrFail($id);
        $property->update(['is_approved' => false]);

        $property->owner?->notify(new AdminAnnouncementNotification(
            'Property hidden',
            "{$property->title} was rejected or hidden by admin. Please review the listing details.",
            route('owner.properties.edit', $property->id),
            ['property_id' => $property->id]
        ));

        return back()->with('success', 'Property rejected/hidden.');
    }

    public function destroy($id)
    {
        Property::findOrFail($id)->delete();
        return back()->with('success', 'Property deleted.');
    }

    private function matchingTenants(Property $property)
    {
        return User::where('role', 'tenant')
            ->where('id', '!=', $property->user_id)
            ->get()
            ->filter(fn (User $tenant) => $this->matchesPreferences($property, $tenant->app_preferences ?? []))
            ->filter(fn (User $tenant) => $tenant->wantsNotification('new_listings_nearby'))
            ->values();
    }

    private function matchesPreferences(Property $property, array $preferences): bool
    {
        if (empty($preferences)) {
            return true;
        }

        $minPrice = (float) ($preferences['price_min'] ?? 0);
        $maxPrice = (float) ($preferences['price_max'] ?? 0);
        $preferredRoomType = strtolower((string) ($preferences['room_type'] ?? 'any'));
        $preferredLocation = strtolower(trim((string) ($preferences['default_location'] ?? '')));

        if ($minPrice > 0 && (float) $property->price < $minPrice) {
            return false;
        }

        if ($maxPrice > 0 && (float) $property->price > $maxPrice) {
            return false;
        }

        if ($preferredRoomType !== '' && $preferredRoomType !== 'any' && strtolower((string) $property->room_type) !== $preferredRoomType) {
            return false;
        }

        if ($preferredLocation !== '' && ! str_contains(strtolower((string) $property->address), $preferredLocation)) {
            return false;
        }

        return true;
    }
}
