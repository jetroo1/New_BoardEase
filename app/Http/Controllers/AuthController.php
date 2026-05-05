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
    public function showLogin()   { return view('auth.login'); }
    public function showRegister(){ return view('auth.login'); }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (!User::where('email', $request->email)->exists()) {
            return back()->withErrors(['email' => 'No account found. Please register first.'])->withInput();
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect(Auth::user()->redirectPath());
        }

        return back()->withErrors(['email' => 'Invalid email or password.']);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'role'     => 'required|in:tenant,owner',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        Auth::login($user);
        return redirect($user->redirectPath());
    }

    // ── Google ────────────────────────────────────────────────────────────────

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
                        ->orWhere('email', $googleUser->email)->first();

            $avatarUrl = $googleUser->avatar;
            if ($avatarUrl) {
                $avatarUrl = preg_replace('/=s\d+-c/', '=s200-c', $avatarUrl);
            }

            if (!$user) {
                $user = User::create([
                    'name'          => $googleUser->name,
                    'email'         => $googleUser->email,
                    'password'      => Hash::make(Str::random(24)),
                    'role'          => 'tenant',
                    'google_id'     => $googleUser->id,
                    'avatar'        => $avatarUrl,
                    'profile_photo' => $avatarUrl,
                ]);
            } else {
                $user->update([
                    'google_id'     => $googleUser->id,
                    'avatar'        => $avatarUrl,
                    'profile_photo' => $user->profile_photo ?: $avatarUrl,
                ]);
            }

            Auth::login($user);
            return redirect($user->redirectPath());

        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['email' => $e->getMessage()]);
        }
    }

    // ── Facebook ──────────────────────────────────────────────────────────────

    public function facebookRedirect()
{
    session(['facebook_intent' => 'login']);
    return Socialite::driver('facebook')
        ->setHttpClient(new \GuzzleHttp\Client(['verify' => false]))
        ->stateless()
        ->redirect();
}

public function facebookRegisterRedirect()
{
    session(['facebook_intent' => 'register']);
    return Socialite::driver('facebook')
        ->setHttpClient(new \GuzzleHttp\Client(['verify' => false]))
        ->stateless()
        ->redirect();
}

    public function facebookCallback(Request $request)
{
    // User cancelled or Facebook returned an error
    if ($request->has('error') || !$request->has('code')) {
        return redirect()->route('login')
            ->with('social_cancelled', 'Facebook sign-in was cancelled.');
    }

    // Read intent from session
    $intent = session('facebook_intent', 'login');
    session()->forget(['facebook_state', 'facebook_intent']);

    try {
        $fbUser = Socialite::driver('facebook')
            ->setHttpClient(new \GuzzleHttp\Client(['verify' => false]))
            ->stateless()
            ->user();

        $user = User::where('facebook_id', $fbUser->id)
                    ->orWhere('email', $fbUser->email)
                    ->first();

        if ($user) {
            $user->update([
                'facebook_id'   => $fbUser->id,
                'avatar'        => $fbUser->avatar,
                'profile_photo' => $user->profile_photo ?: $fbUser->avatar,
            ]);
        } else {
            if ($intent === 'register') {
                $user = User::create([
                    'name'          => $fbUser->name,
                    'email'         => $fbUser->email ?? $fbUser->id . '@facebook.com',
                    'password'      => Hash::make(Str::random(24)),
                    'role'          => 'tenant',
                    'facebook_id'   => $fbUser->id,
                    'avatar'        => $fbUser->avatar,
                    'profile_photo' => $fbUser->avatar,
                ]);
            } else {
                return redirect()->route('login')
                    ->withErrors(['email' => 'No account found. Please register first.']);
            }
        }

        Auth::login($user);
        return redirect($user->redirectPath());

    } catch (\Exception $e) {
    return redirect()->route('login')
        ->with('social_cancelled', 'Facebook sign-in cancelled.');
}
}

    // ── Logout ────────────────────────────────────────────────────────────────

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}