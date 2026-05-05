<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class BookingsController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['user', 'property'])
            ->forOwner(Auth::id())->latest()->get();
        return view('owner.bookings.index', compact('bookings'));
    }
}