<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {

            // Real stats from DB
            $totalListings   = Property::count();
            $activeBookings  = Booking::where('status', 'confirmed')->count();
            $pendingRequests = Booking::where('status', 'pending')->count();
            $monthlyRevenue  = Booking::where('bookings.status', 'confirmed') // ← fixed
                ->join('properties', 'bookings.property_id', '=', 'properties.id')
                ->sum('properties.price');

            // Admin's own properties with their active bookings
            $listings = Property::with(['bookings' => function($q) {
                $q->where('status', 'confirmed')->with('user')->latest();
            }])->latest()->get();

            // Pending booking requests (all properties)
            $pendingBookings = Booking::with(['user', 'property'])
                ->where('status', 'pending')
                ->latest()
                ->get();

            // Recent confirmed bookings
            $confirmedBookings = Booking::with(['user', 'property'])
                ->where('status', 'confirmed')
                ->latest()
                ->take(5)
                ->get();

            return view('dashboard.admin', compact(
                'user',
                'totalListings',
                'activeBookings',
                'pendingRequests',
                'monthlyRevenue',
                'listings',
                'pendingBookings',
                'confirmedBookings'
            ));
        }

        // Tenant dashboard
$bookings = Booking::with('property')
    ->where('user_id', $user->id)
    ->latest()
    ->get();

$confirmedBooking = $bookings->where('status', 'confirmed')->first();
$pendingBooking   = $bookings->where('status', 'pending')->first();

$recentProperties = Property::withAvg('reviews', 'rating')
    ->latest()->take(4)->get();

$savedCount = \App\Models\Favorite::where('user_id', $user->id)->count();

return view('dashboard.tenant', compact(
    'user', 
    'bookings', 
    'confirmedBooking', 
    'pendingBooking', 
    'recentProperties',
    'savedCount'
));
    }
}
