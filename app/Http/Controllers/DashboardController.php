<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return match($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'owner' => redirect()->route('owner.dashboard'),
            default => $this->tenantDashboard($user),
        };
    }

    private function tenantDashboard($user)
    {
        $bookings = Booking::with('property')
            ->where('user_id', $user->id)->latest()->get();

        $confirmedBooking = $bookings->where('status', 'confirmed')->first();
        $pendingBooking   = $bookings->where('status', 'pending')->first();

        // REAL properties with real coordinates for map
        $recentProperties = Property::withAvg('reviews', 'rating')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->latest()->take(4)->get();

        $savedCount = \App\Models\Favorite::where('user_id', $user->id)->count();

        return view('dashboard.tenant', compact(
            'user', 'bookings', 'confirmedBooking',
            'pendingBooking', 'recentProperties', 'savedCount'
        ));
    }
}