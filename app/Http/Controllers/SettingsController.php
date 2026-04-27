<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    public function index()
    {
        return view('settings.index', ['user' => Auth::user()]);
    }

    // ── Profile ──────────────────────────────────────────────
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $user->name  = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->save();

        return back()->with('success_profile', 'Profile updated successfully.');
    }

    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = Auth::user();

        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $path = $request->file('photo')->store('profile-photos', 'public');
        $user->profile_photo = $path;
        $user->save();

        return back()->with('success_profile', 'Photo updated successfully.');
    }

    public function removePhoto()
    {
        $user = Auth::user();

        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
            $user->profile_photo = null;
            $user->save();
        }

        return back()->with('success_profile', 'Photo removed.');
    }

    // ── Notifications ─────────────────────────────────────────
    public function updateNotifications(Request $request)
    {
        $user = Auth::user();

        $prefs = [
            'booking_confirmations' => $request->boolean('booking_confirmations'),
            'move_out_reminders'    => $request->boolean('move_out_reminders'),
            'new_messages'          => $request->boolean('new_messages'),
            'new_listings_nearby'   => $request->boolean('new_listings_nearby'),
            'payment_receipts'      => $request->boolean('payment_receipts'),
            'review_reminders'      => $request->boolean('review_reminders'),
        ];

        $user->notification_preferences = $prefs;
        $user->save();

        return back()->with('success_notifications', 'Notification preferences saved.');
    }

    // ── Security ──────────────────────────────────────────────
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.'])->with('tab', 'security');
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success_security', 'Password updated successfully.');
    }

    public function deleteAccount(Request $request)
    {
        $request->validate([
            'confirm_delete' => ['required', 'in:DELETE'],
        ]);

        $user = Auth::user();
        Auth::logout();
        $user->delete();

        return redirect('/login')->with('status', 'Your account has been deleted.');
    }

    // ── Preferences ───────────────────────────────────────────
    public function updatePreferences(Request $request)
    {
        $request->validate([
            'default_location' => ['nullable', 'string', 'max:100'],
            'price_min'        => ['nullable', 'numeric', 'min:0'],
            'price_max'        => ['nullable', 'numeric', 'min:0'],
            'room_type'        => ['nullable', 'string'],
        ]);

        $user = Auth::user();

        $user->app_preferences = [
            'default_location' => $request->default_location,
            'price_min'        => $request->price_min ?? 0,
            'price_max'        => $request->price_max ?? 10000,
            'room_type'        => $request->room_type ?? 'any',
        ];

        $user->save();

        return back()->with('success_preferences', 'Preferences saved.');
    }

    // ── Theme ─────────────────────────────────────────────────
    public function updateTheme(Request $request)
    {
        $request->validate(['theme' => ['required', 'in:light,dark']]);

        $user = Auth::user();
        $user->theme = $request->theme;
        $user->save();

        return response()->json(['theme' => $user->theme]);
    }
}