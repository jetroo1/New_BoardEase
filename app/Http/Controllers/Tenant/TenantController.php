<?php
namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Property;
use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;

class TenantController extends Controller
{
    public function dashboard()
    {
        $user             = Auth::user();
        $bookings         = Booking::with('property')
                                ->where('user_id', $user->id)->latest()->get();
        $confirmedBooking = $bookings->where('status', 'confirmed')->first();
        $pendingBooking   = $bookings->where('status', 'pending')->first();
        $recentProperties = Property::withAvg('reviews', 'rating')
                                ->whereNotNull('latitude')
                                ->whereNotNull('longitude')
                                ->where('is_approved', true)
                                ->latest()->take(4)->get();
        $savedCount       = Favorite::where('user_id', $user->id)->count();

        return view('dashboard.tenant', compact(
            'user', 'bookings', 'confirmedBooking',
            'pendingBooking', 'recentProperties', 'savedCount'
        ));
    }
}