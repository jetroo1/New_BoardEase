<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $userExists = User::where('email', $request->email)->exists();
        if (!$userExists) {
            return back()->withErrors([
                'email' => 'No account found. Please register first.'
            ])->withInput();
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('dashboard');
        }

        return back()->withErrors(['email' => 'Invalid email or password.']);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role'     => 'required|in:tenant,owner',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        Auth::login($user);
        return redirect()->route('dashboard');
    }

    // ─── Google ───────────────────────────────────────────────────────────────

    public function googleRedirect()
    {
        return Socialite::driver('google')
            ->setHttpClient(new \GuzzleHttp\Client(['verify' => false]))
            ->redirect();
    }

    public function googleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')
                ->setHttpClient(new \GuzzleHttp\Client(['verify' => false]))
                ->user();

            $user = User::where('google_id', $googleUser->id)
                        ->orWhere('email', $googleUser->email)
                        ->first();

            if ($user) {
                $user->update([
                    'google_id' => $googleUser->id,
                    'avatar'    => $googleUser->avatar,
                ]);
            } else {
                return redirect()->route('login')
                    ->withErrors(['email' => 'No account found for this Google account. Please register first.']);
            }

            Auth::login($user);
            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            return redirect()->route('login')
                ->withErrors(['email' => $e->getMessage()]);
        }
    }

    // ─── Facebook ─────────────────────────────────────────────────────────────

    /**
     * Redirect for Facebook LOGIN (Sign In page).
     */
    public function facebookRedirect()
    {
        session(['facebook_intent' => 'login']);

        return Socialite::driver('facebook')
            ->setHttpClient(new \GuzzleHttp\Client(['verify' => false]))
            ->redirect();
    }

    /**
     * Redirect for Facebook REGISTER (Register page).
     */
    public function facebookRegisterRedirect()
    {
        session(['facebook_intent' => 'register']);

        return Socialite::driver('facebook')
            ->setHttpClient(new \GuzzleHttp\Client(['verify' => false]))
            ->redirect();
    }

    /**
     * Single callback for both login and register via Facebook.
     * Uses the 'facebook_intent' session key to distinguish the two flows.
     */
    public function facebookCallback(Request $request)
    {
        // Handle cancellation or missing code
        if ($request->has('error') || !$request->has('code')) {
            return redirect()->route('login')
                ->with('social_cancelled', 'Facebook sign-in was cancelled.');
        }

        try {
            $facebookUser = Socialite::driver('facebook')
                ->setHttpClient(new \GuzzleHttp\Client(['verify' => false]))
                ->user();

            $user = User::where('facebook_id', $facebookUser->id)
                        ->orWhere('email', $facebookUser->email)
                        ->first();

            if ($user) {
                // Existing user — update Facebook details and log in
                $user->update([
                    'facebook_id' => $facebookUser->id,
                    'avatar'      => $facebookUser->avatar,
                ]);

            } else {
                // No existing user found
                $intent = session('facebook_intent', 'login');

                if ($intent === 'register') {
                    // ✅ Create a brand-new account
                    $user = User::create([
                        'name'        => $facebookUser->name,
                        'email'       => $facebookUser->email,
                        'password'    => Hash::make(Str::random(24)),
                        'role'        => 'tenant', // default role for Facebook registrations
                        'facebook_id' => $facebookUser->id,
                        'avatar'      => $facebookUser->avatar,
                    ]);
                } else {
                    // Login intent but no account exists — tell them to register
                    return redirect()->route('login')
                        ->withErrors(['email' => 'No account found for this Facebook account. Please register first.']);
                }
            }

            Auth::login($user);
            session()->forget('facebook_intent');
            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('social_cancelled', 'Facebook sign-in was cancelled or an error occurred.');
        }
    }

    // ─── Logout ───────────────────────────────────────────────────────────────

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}