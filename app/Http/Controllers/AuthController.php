<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function showLogin()   { return view('auth.login', ['defaultForm' => 'login']); }
    public function showRegister(){ return view('auth.login', ['defaultForm' => 'register']); }
    public function showForgotPassword() { return view('auth.forgot-password'); }
    public function showResetPassword(Request $request, string $token) { return view('auth.reset-password', compact('request', 'token')); }
    public function showVerifyEmail() { return view('auth.verify-email'); }
    public function showConfirmPassword() { return view('auth.confirm-password'); }
    public function showEmailVerificationCode(Request $request)
    {
        if (! $request->session()->has('email_verification_code')) {
            return redirect()->route('login')->withErrors(['email' => 'Please register or sign in first so we can send your verification code.']);
        }

        return view('auth.verify-code');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (!User::where('email', $request->email)->exists()) {
            return back()->withErrors(['email' => 'No account found. Please register first.'])->withInput();
        }

        $user = User::where('email', $request->email)->first();

        if (! Hash::check($request->password, $user->password)) {
            return back()->withErrors(['email' => 'Invalid email or password.'])->withInput();
        }

        try {
            $this->startEmailCodeVerification($request, $user->email, 'login', [
                'user_id' => $user->id,
            ]);
        } catch (\Throwable $e) {
            return back()->withErrors(['email' => 'We could not send the verification code. Please check your mail settings and try again.'])->withInput();
        }

        return redirect()->route('verification.code')
            ->with('status', "Code sent to {$user->email}. Open your Gmail inbox, copy the 6-digit code, then paste it here to sign in.");
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'role'     => 'nullable|in:tenant,owner',
        ]);

        try {
            $this->startEmailCodeVerification($request, $request->email, 'register', [
                'name' => $request->name,
                'password_hash' => Hash::make($request->password),
                'role' => $request->input('role', 'tenant'),
            ]);
        } catch (\Throwable $e) {
            return back()
                ->withErrors(['email' => 'We could not send the verification code. Please check your mail settings and try again.'])
                ->withInput($request->except('password', 'password_confirmation'));
        }

        return redirect()->route('verification.code')
            ->with('status', "Code sent to {$request->email}. Open your Gmail inbox, copy the 6-digit code, then paste it here to finish registration.");
    }

    public function verifyEmailCode(Request $request)
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $verification = $request->session()->get('email_verification_code');

        if (! $verification) {
            return redirect()->route('login')->withErrors(['email' => 'Your verification session expired. Please try again.']);
        }

        if (now()->timestamp > ($verification['expires_at'] ?? 0)) {
            $request->session()->forget('email_verification_code');
            return redirect()->route('login')->withErrors(['email' => 'Your verification code expired. Please request a new one.']);
        }

        if (($verification['attempts'] ?? 0) >= 5) {
            $request->session()->forget('email_verification_code');
            return redirect()->route('login')->withErrors(['email' => 'Too many incorrect code attempts. Please start again.']);
        }

        if (! Hash::check($request->code, $verification['code_hash'])) {
            $verification['attempts'] = ($verification['attempts'] ?? 0) + 1;
            $request->session()->put('email_verification_code', $verification);

            return back()->withErrors(['code' => 'The verification code is incorrect.'])->withInput();
        }

        if ($verification['purpose'] === 'register') {
            if (User::where('email', $verification['email'])->exists()) {
                $request->session()->forget('email_verification_code');
                return redirect()->route('login')->withErrors(['email' => 'An account with this email already exists. Please sign in.']);
            }

            $user = User::create([
                'name' => $verification['name'],
                'email' => $verification['email'],
                'password' => $verification['password_hash'],
                'role' => $verification['role'] ?? 'tenant',
                'email_verified_at' => now(),
            ]);

            event(new Registered($user));
        } else {
            $user = User::find($verification['user_id'] ?? null);

            if (! $user || $user->email !== $verification['email']) {
                $request->session()->forget('email_verification_code');
                return redirect()->route('login')->withErrors(['email' => 'We could not verify this account. Please try again.']);
            }

            if (! $user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();
                event(new Verified($user));
            }
        }

        $request->session()->forget('email_verification_code');

        Auth::login($user);
        $request->session()->regenerate();

        return redirect($user->redirectPath());
    }

    public function resendEmailCode(Request $request)
    {
        $verification = $request->session()->get('email_verification_code');

        if (! $verification) {
            return redirect()->route('login')->withErrors(['email' => 'Please register or sign in first so we can send your verification code.']);
        }

        if (now()->timestamp < ($verification['resend_at'] ?? 0)) {
            return back()->withErrors(['code' => 'Please wait a moment before requesting another code.']);
        }

        $payload = $verification;
        unset($payload['code_hash'], $payload['expires_at'], $payload['resend_at'], $payload['attempts'], $payload['testing_code'], $payload['email'], $payload['purpose']);

        try {
            $this->startEmailCodeVerification($request, $verification['email'], $verification['purpose'], $payload);
        } catch (\Throwable $e) {
            return back()->withErrors(['code' => 'We could not resend the verification code. Please check your mail settings and try again.']);
        }

        return back()->with('status', "New code sent to {$verification['email']}. Open your Gmail inbox and paste the latest code here.");
    }

    public function sendPasswordResetLink(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withInput($request->only('email'))->withErrors(['email' => __($status)]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withInput($request->only('email'))->withErrors(['email' => __($status)]);
    }

    public function verifyEmail(Request $request, string $id, string $hash)
    {
        if (! hash_equals($id, (string) $request->user()->getKey()) ||
            ! hash_equals($hash, sha1($request->user()->getEmailForVerification()))) {
            abort(403);
        }

        if (! $request->user()->hasVerifiedEmail()) {
            $request->user()->markEmailAsVerified();
            event(new Verified($request->user()));
        }

        return redirect()->route('dashboard', ['verified' => 1]);
    }

    public function sendVerificationNotification(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }

    public function confirmPassword(Request $request)
    {
        $request->validate(['password' => ['required']]);

        if (! Hash::check($request->password, $request->user()->password)) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        $request->session()->put('auth.password_confirmed_at', time());

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function updatePassword(Request $request)
    {
        $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('status', 'password-updated');
    }

    // ── Google ────────────────────────────────────────────────────────────────

    public function googleRedirect()
    {
        session(['google_intent' => 'login']);

        return Socialite::driver('google')
            ->setHttpClient(new \GuzzleHttp\Client(['verify' => false]))
            ->redirect();
    }

    public function googleRegisterRedirect()
    {
        session(['google_intent' => 'register']);

        return Socialite::driver('google')
            ->setHttpClient(new \GuzzleHttp\Client(['verify' => false]))
            ->redirect();
    }

    public function googleCallback()
    {
        try {
            $intent = session('google_intent', 'login');
            session()->forget('google_intent');

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
                if ($intent !== 'register') {
                    return redirect()->route('login')
                        ->withErrors(['email' => 'No Google account found in BoardEase. Please register first.']);
                }

                $user = User::create([
                    'name'          => $googleUser->name,
                    'email'         => $googleUser->email,
                    'password'      => Hash::make(Str::random(24)),
                    'role'          => 'tenant',
                    'google_id'     => $googleUser->id,
                    'avatar'        => $avatarUrl,
                    'profile_photo' => $avatarUrl,
                ]);

                event(new Registered($user));
            } else {
                $user->update([
                    'google_id'     => $googleUser->id,
                    'avatar'        => $avatarUrl,
                    'profile_photo' => $user->profile_photo ?: $avatarUrl,
                ]);
            }

            try {
                $this->startEmailCodeVerification(request(), $user->email, 'login', [
                    'user_id' => $user->id,
                ]);
            } catch (\Throwable $e) {
                return redirect()->route('login')
                    ->withErrors(['email' => 'Google confirmed your email, but BoardEase could not send the verification code. Please check mail settings and try again.']);
            }

            return redirect()->route('verification.code')
                ->with('status', "Google confirmed {$user->email}. We sent a BoardEase code to that Gmail. Paste it here to continue.");

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

        return redirect()->route('login')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, private')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }

    private function startEmailCodeVerification(Request $request, string $email, string $purpose, array $payload = []): void
    {
        $code = (string) random_int(100000, 999999);

        $verification = array_merge($payload, [
            'email' => strtolower($email),
            'purpose' => $purpose,
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes(10)->timestamp,
            'resend_at' => now()->addSeconds(45)->timestamp,
            'attempts' => 0,
        ]);

        if (app()->environment('testing')) {
            $verification['testing_code'] = $code;
        }

        $request->session()->put('email_verification_code', $verification);

        $html = view('emails.verification-code', [
            'code' => $code,
            'purpose' => $purpose,
            'expiresIn' => '10 minutes',
        ])->render();

        Mail::html($html, function ($message) use ($email) {
            $message->to($email)->subject('Your BoardEase verification code');
        });
    }
}
