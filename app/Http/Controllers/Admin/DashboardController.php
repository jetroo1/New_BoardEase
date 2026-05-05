<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user            = Auth::user();
        $totalListings   = Property::count();
        $activeBookings  = Booking::where('status', 'confirmed')->count();
        $pendingRequests = Booking::where('status', 'pending')->count();
        $monthlyRevenue  = Booking::where('bookings.status', 'confirmed')
            ->join('properties', 'bookings.property_id', '=', 'properties.id')
            ->sum('properties.price');

        $listings = Property::with(['bookings' => fn($q) =>
            $q->where('status', 'confirmed')->with('user')->latest()
        ])->latest()->get();

        $pendingBookings = Booking::with(['user', 'property'])
            ->where('status', 'pending')->latest()->get();

        $confirmedBookings = Booking::with(['user', 'property'])
            ->where('status', 'confirmed')->latest()->take(5)->get();

        return view('dashboard.admin', compact(
            'user', 'totalListings', 'activeBookings', 'pendingRequests',
            'monthlyRevenue', 'listings', 'pendingBookings', 'confirmedBookings'
        ));
    }
}