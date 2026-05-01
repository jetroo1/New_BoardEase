<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    // ← __construct() REMOVED (not supported in Laravel 11+/13)

    public function index()
    {
        $allBookings = Booking::with('property')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        $bookings    = $allBookings;
        $activeCount = $allBookings->filter(fn($b) => $b->isActiveStay())->count();
        $pastCount   = $allBookings->where('status', 'completed')->count();

        return view('bookings.index', compact('bookings', 'activeCount', 'pastCount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'check_in'    => 'required|date',
            'duration'    => 'required|integer|min:1',
        ]);

        $startDate = $request->check_in;
        $endDate   = date('Y-m-d', strtotime($startDate . ' + ' . $request->duration . ' months'));

        Booking::create([
            'user_id'     => Auth::id(),
            'property_id' => $request->property_id,
            'start_date'  => $startDate,
            'end_date'    => $endDate,
            'status'      => 'pending',
        ]);

        return redirect()->route('bookings.index')
            ->with('success', 'Booking submitted! Admin will contact you within 24 hours.');
    }

    public function approve($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update(['status' => 'confirmed']);
        return back()->with('success', 'Booking approved!');
    }

    public function reject($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update(['status' => 'rejected']);
        return back()->with('success', 'Booking rejected!');
    }

    public function cancel($id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'This booking cannot be cancelled.');
        }

        $booking->update(['status' => 'cancelled']);
        return back()->with('success', 'Booking cancelled.');
    }
}