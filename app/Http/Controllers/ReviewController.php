<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Property;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Show reviews page.
     * - pendingReviews: completed bookings the user hasn't reviewed yet
     * - myReviews: reviews the user has already submitted
     * - allProperties: every property (for the "pick any boarding house" dropdown)
     * - summary: rating distribution
     */
    public function index()
    {
        $user = Auth::user();

        // Bookings the user completed but hasn't reviewed yet
        $pendingReviews = Booking::with('property')
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereDoesntHave('review', fn($q) => $q->where('user_id', $user->id))
            ->latest()
            ->get();

        // All reviews the user has submitted
        $myReviews = Review::with('property')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        // All properties available for voluntary review
        $allProperties = Property::orderBy('title')->get();

        // Rating summary
        $ratings = $myReviews->pluck('rating');
        $avgRating = $ratings->count() ? round($ratings->avg(), 1) : 0;
        $distribution = collect([5, 4, 3, 2, 1])
            ->mapWithKeys(fn($s) => [$s => $ratings->filter(fn($r) => $r == $s)->count()]);

        return view('reviews.index', compact(
            'pendingReviews',
            'myReviews',
            'allProperties',
            'avgRating',
            'distribution'
        ));
    }

    /**
     * Store a new review.
     */
    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'booking_id'  => 'nullable|exists:bookings,id',
            'rating'      => 'required|integer|min:1|max:5',
            'body'        => 'required|string|min:10|max:1000',
        ]);

        $user = Auth::user();

        // Prevent duplicate reviews
        $exists = Review::where('user_id', $user->id)
            ->where('property_id', $request->property_id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'You have already reviewed this boarding house.');
        }

        Review::create([
            'user_id'     => $user->id,
            'property_id' => $request->property_id,
            'booking_id'  => $request->booking_id,
            'rating'      => $request->rating,
            'body'        => $request->body,
        ]);

        return back()->with('success', 'Your review has been submitted!');
    }

    /**
     * Delete the authenticated user's own review.
     */
    public function destroy(Review $review)
    {
        if ($review->user_id !== Auth::id()) {
            abort(403);
        }

        $review->delete();

        return back()->with('success', 'Review deleted.');
    }
}
