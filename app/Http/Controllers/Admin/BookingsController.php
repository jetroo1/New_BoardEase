<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;

class BookingsController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['user', 'property', 'property.owner'])
            ->latest()->paginate(20);
        return view('admin.bookings.index', compact('bookings'));
    }
}