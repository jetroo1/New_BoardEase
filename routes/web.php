<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\FavoritesController;
use App\Http\Controllers\NotificationController;

Route::get('/', [LandingController::class, 'index'])->name('landing.root');
Route::get('/landing', [LandingController::class, 'index'])->name('landing');

// ── Guest ─────────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',[AuthController::class, 'register']);

    Route::get('/auth/google',            [AuthController::class, 'googleRedirect'])->name('auth.google');
    Route::get('/auth/google/register',   [AuthController::class, 'googleRegisterRedirect'])->name('auth.google.register');
    Route::get('/auth/google/callback',   [AuthController::class, 'googleCallback']);
    Route::get('/auth/facebook',          [AuthController::class, 'facebookRedirect'])->name('auth.facebook');
    Route::get('/auth/facebook/register', [AuthController::class, 'facebookRegisterRedirect'])->name('auth.facebook.register');
    Route::get('/auth/facebook/callback', [AuthController::class, 'facebookCallback']);

    Route::get('/email/verify-code', [AuthController::class, 'showEmailVerificationCode'])->name('verification.code');
    Route::post('/email/verify-code', [AuthController::class, 'verifyEmailCode'])->name('verification.code.verify');
    Route::post('/email/verify-code/resend', [AuthController::class, 'resendEmailCode'])->name('verification.code.resend');

    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendPasswordResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.store');
});

// ── Authenticated (all roles) ─────────────────────────────────────────────────
Route::middleware(['auth', 'no_cache'])->group(function () {

    Route::get('/home', fn() => redirect()->route('dashboard'))->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/verify-email', [AuthController::class, 'showVerifyEmail'])->name('verification.notice');
    Route::get('/verify-email/{id}/{hash}', [AuthController::class, 'verifyEmail'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [AuthController::class, 'sendVerificationNotification'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('/confirm-password', [AuthController::class, 'showConfirmPassword'])->name('password.confirm');
    Route::post('/confirm-password', [AuthController::class, 'confirmPassword']);
    Route::put('/password', [AuthController::class, 'updatePassword'])->name('password.update');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Public property detail (all roles)
    Route::get('/properties/{id}', [PropertyController::class, 'show'])->name('property.show');

    // Search (all roles)
    Route::get('/search',             [SearchController::class, 'index'])->name('search');
    Route::get('/search/suggestions', [SearchController::class, 'suggestions'])->name('search.suggestions');

    // Chat (all roles)
    Route::get('/chat',                          [ChatController::class, 'index'])->name('chat');
    Route::get('/chat/{conversation}',           [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{conversation}/messages', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::get('/chat/{conversation}/messages',  [ChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('/chat/{conversation}/typing',   [ChatController::class, 'typing'])->name('chat.typing');
    Route::post('/chat/{conversation}/seen',     [ChatController::class, 'markSeen'])->name('chat.seen');
    Route::post('/chat/heartbeat',               [ChatController::class, 'heartbeat'])->name('chat.heartbeat');
    Route::post('/chat/start',                   [ChatController::class, 'startOrOpen'])->name('chat.start');
    Route::post('/chat/{conversation}/signal',   [ChatController::class, 'signal'])->name('chat.signal');

    // Settings (all roles)
    Route::get('/settings',                 [SettingsController::class, 'index'])->name('settings');
    Route::patch('/settings/profile',       [SettingsController::class, 'updateProfile'])->name('settings.profile.update');
    Route::post('/settings/photo/upload',   [SettingsController::class, 'uploadPhoto'])->name('settings.photo.upload');
    Route::delete('/settings/photo',        [SettingsController::class, 'removePhoto'])->name('settings.photo.remove');
    Route::patch('/settings/notifications', [SettingsController::class, 'updateNotifications'])->name('settings.notifications.update');
    Route::patch('/settings/password',      [SettingsController::class, 'updatePassword'])->name('settings.password.update');
    Route::delete('/settings/account',      [SettingsController::class, 'deleteAccount'])->name('settings.account.delete');
    Route::patch('/settings/preferences',   [SettingsController::class, 'updatePreferences'])->name('settings.preferences.update');
    Route::post('/settings/theme',          [SettingsController::class, 'updateTheme'])->name('settings.theme.update');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ── TENANT ────────────────────────────────────────────────────────────────
    Route::middleware('is_tenant')->group(function () {
        Route::get('/bookings',              [BookingController::class, 'index'])->name('bookings.index');
        Route::post('/bookings',             [BookingController::class, 'store'])->name('bookings.store');
        Route::post('/bookings/{id}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');

        Route::get('/favorites',              [FavoritesController::class, 'index'])->name('favorites');
        Route::post('/favorites/{id}/toggle', [FavoritesController::class, 'toggle'])->name('favorites.toggle');

        Route::get('/reviews',             [ReviewController::class, 'index'])->name('reviews');
        Route::post('/reviews',            [ReviewController::class, 'store'])->name('reviews.store');
        Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    });

    // ── OWNER ─────────────────────────────────────────────────────────────────
    Route::middleware('is_owner')->prefix('owner')->name('owner.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Owner\DashboardController::class, 'index'])->name('dashboard');

        Route::get('/properties',           [\App\Http\Controllers\Owner\PropertiesController::class, 'index'])->name('properties');
        Route::get('/properties/create',    [\App\Http\Controllers\Owner\PropertiesController::class, 'create'])->name('properties.create');
        Route::post('/properties',          [\App\Http\Controllers\Owner\PropertiesController::class, 'store'])->name('properties.store');
        Route::get('/properties/{id}/edit', [\App\Http\Controllers\Owner\PropertiesController::class, 'edit'])->name('properties.edit');
        Route::put('/properties/{id}',      [\App\Http\Controllers\Owner\PropertiesController::class, 'update'])->name('properties.update');
        Route::delete('/properties/{id}',   [\App\Http\Controllers\Owner\PropertiesController::class, 'destroy'])->name('properties.destroy');

        Route::get('/bookings',                [App\Http\Controllers\Owner\BookingsController::class, 'index'])->name('bookings');
        Route::post('/bookings/{id}/approve',  [BookingController::class, 'approve'])->name('bookings.approve');
        Route::post('/bookings/{id}/reject',   [BookingController::class, 'reject'])->name('bookings.reject');
        Route::post('/bookings/{id}/complete', [BookingController::class, 'complete'])->name('bookings.complete');
    });

    // ── ADMIN ─────────────────────────────────────────────────────────────────
    Route::middleware('is_admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

        Route::get('/users',               [\App\Http\Controllers\Admin\UsersController::class, 'index'])->name('users');
        Route::patch('/users/{user}/role', [\App\Http\Controllers\Admin\UsersController::class, 'updateRole'])->name('users.role');
        Route::delete('/users/{user}',     [\App\Http\Controllers\Admin\UsersController::class, 'destroy'])->name('users.destroy');

        Route::get('/properties',                [\App\Http\Controllers\Admin\PropertiesController::class, 'index'])->name('properties');
        Route::post('/properties/{id}/approve',  [\App\Http\Controllers\Admin\PropertiesController::class, 'approve'])->name('properties.approve');
        Route::post('/properties/{id}/reject',   [\App\Http\Controllers\Admin\PropertiesController::class, 'reject'])->name('properties.reject');
        Route::delete('/properties/{id}',        [\App\Http\Controllers\Admin\PropertiesController::class, 'destroy'])->name('properties.destroy');

        Route::get('/bookings',                [\App\Http\Controllers\Admin\BookingsController::class, 'index'])->name('bookings');
        Route::post('/bookings/{id}/approve',  [BookingController::class, 'approve'])->name('bookings.approve');
        Route::post('/bookings/{id}/reject',   [BookingController::class, 'reject'])->name('bookings.reject');
        Route::post('/bookings/{id}/complete', [BookingController::class, 'complete'])->name('bookings.complete');

        Route::post('/announcements', [NotificationController::class, 'broadcastAnnouncement'])->name('announcements.store');
    });
});
