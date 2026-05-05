<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $totalProperties     = Property::where('user_id', $user->id)->count();
        $pendingBookings     = Booking::forOwner($user->id)->where('status', 'pending')->count();
        $activeBookings      = Booking::forOwner($user->id)->where('status', 'confirmed')->count();
        $monthlyRevenue      = Booking::forOwner($user->id)
            ->where('bookings.status', 'confirmed')
            ->join('properties', 'bookings.property_id', '=', 'properties.id')
            ->sum('properties.price');

        $listings = Property::with(['bookings' => fn($q) => $q->where('status', 'confirmed')->with('user')->latest()])
            ->where('user_id', $user->id)->latest()->get();

        $pendingBookingsList = Booking::with(['user', 'property'])
            ->forOwner($user->id)->where('status', 'pending')->latest()->get();

        $confirmedBookings = Booking::with(['user', 'property'])
            ->forOwner($user->id)->where('status', 'confirmed')->latest()->take(5)->get();

        return view('owner.dashboard', compact(
            'user', 'totalProperties', 'pendingBookings', 'activeBookings',
            'monthlyRevenue', 'listings', 'pendingBookingsList', 'confirmedBookings'
        ));
    }
}