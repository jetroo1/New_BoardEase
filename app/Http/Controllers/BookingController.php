<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    // Tenant: view only their own bookings
    public function index()
    {
        $user        = Auth::user();
        $allBookings = Booking::with('property')->where('user_id', $user->id)->latest()->get();
        $bookings    = $allBookings;
        $activeCount = $allBookings->filter(fn($b) => $b->isActiveStay())->count();
        $pastCount   = $allBookings->where('status', 'completed')->count();

        return view('bookings.index', compact('bookings', 'activeCount', 'pastCount'));
    }

    // Tenant: create booking → always pending
    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'check_in'    => 'required|date|after_or_equal:today',
            'duration'    => 'required|integer|min:1|max:24',
        ]);

        $property   = Property::findOrFail($request->property_id);
        $startDate  = $request->check_in;
        $endDate    = date('Y-m-d', strtotime($startDate . ' + ' . $request->duration . ' months'));
        $totalPrice = $property->price * $request->duration;

        Booking::create([
            'user_id'     => Auth::id(),
            'property_id' => $request->property_id,
            'start_date'  => $startDate,
            'end_date'    => $endDate,
            'status'      => 'pending',
            'total_price' => $totalPrice,
        ]);

        return redirect()->route('bookings.index')
            ->with('success', 'Booking submitted! The owner will review your request.');
    }

    // Owner/Admin: approve → confirmed
    public function approve($id)
    {
        $booking = Booking::with('property')->findOrFail($id);

        if ($booking->property->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'You do not own this property.');
        }

        $booking->update(['status' => 'confirmed']);
        return back()->with('success', 'Booking approved!');
    }

    // Owner/Admin: reject → cancelled
    public function reject($id)
    {
        $booking = Booking::with('property')->findOrFail($id);

        if ($booking->property->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'You do not own this property.');
        }

        $booking->update(['status' => 'cancelled']);
        return back()->with('success', 'Booking rejected.');
    }

    // Tenant: cancel their own booking
    public function cancel($id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->user_id !== Auth::id()) abort(403);

        if (!$booking->isCancellable()) {
            return back()->with('error', 'This booking cannot be cancelled.');
        }

        $booking->update(['status' => 'cancelled']);
        return back()->with('success', 'Booking cancelled.');
    }

    // Owner/Admin: mark as completed
    public function complete($id)
    {
        $booking = Booking::with('property')->findOrFail($id);

        if ($booking->property->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $booking->update(['status' => 'completed']);
        return back()->with('success', 'Booking marked as completed.');
    }
}