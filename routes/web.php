<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\FavoritesController;

Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',[AuthController::class, 'register']);

    // Google OAuth
    Route::get('/auth/google',          [AuthController::class, 'googleRedirect'])->name('auth.google');
    Route::get('/auth/google/callback', [AuthController::class, 'googleCallback']);

    // Facebook OAuth — separate routes for login vs register intent
    Route::get('/auth/facebook',          [AuthController::class, 'facebookRedirect'])->name('auth.facebook');
    Route::get('/auth/facebook/register', [AuthController::class, 'facebookRegisterRedirect'])->name('auth.facebook.register');
    Route::get('/auth/facebook/callback', [AuthController::class, 'facebookCallback']);

    Route::get('/forgot-password', fn() => view('auth.forgot-password'))->name('password.request');
});

Route::middleware('auth')->group(function () {

    Route::get('/', fn() => redirect()->route('dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Search & Listings
    Route::get('/search',              [SearchController::class, 'index'])->name('search');
    Route::get('/search/suggestions',  [SearchController::class, 'suggestions'])->name('search.suggestions');
    Route::get('/properties/create',   [PropertyController::class, 'create'])->name('properties.create');
    Route::post('/properties',         [PropertyController::class, 'store'])->name('properties.store');
    Route::get('/properties/{id}',     [PropertyController::class, 'show'])->name('property.show');
    Route::put('/properties/{id}',     [PropertyController::class, 'update'])->name('properties.update');
    Route::delete('/properties/{id}',  [PropertyController::class, 'destroy'])->name('properties.destroy');

    // Bookings
    Route::get('/bookings',                       [BookingController::class, 'index'])->name('bookings');
    Route::post('/bookings',                      [BookingController::class, 'store'])->name('bookings.store');
    Route::post('/bookings/{id}/approve',         [BookingController::class, 'approve'])->name('bookings.approve');
    Route::post('/bookings/{id}/reject',          [BookingController::class, 'reject'])->name('bookings.reject');
    Route::post('/bookings/{id}/cancel',          [BookingController::class, 'cancel'])->name('bookings.cancel');

    // Chat
    Route::get('/chat',                               [ChatController::class, 'index'])->name('chat');
    Route::get('/chat/{conversation}',                [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{conversation}/messages',      [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::get('/chat/{conversation}/messages',       [ChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('/chat/{conversation}/typing',        [ChatController::class, 'typing'])->name('chat.typing');
    Route::post('/chat/start',                        [ChatController::class, 'startOrOpen'])->name('chat.start');
    Route::post('/chat/{conversation}/signal',        [ChatController::class, 'signal'])->name('chat.signal');

    // Favorites
    Route::get('/favorites',              [FavoritesController::class, 'index'])->name('favorites');
    Route::post('/favorites/{id}/toggle', [FavoritesController::class, 'toggle'])->name('favorites.toggle');

    // Pages
    Route::get('/reviews',             [ReviewController::class, 'index'])->name('reviews');
    Route::post('/reviews',            [ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    Route::get('/notifications', fn() => view('notifications.index'))->name('notifications');

    // Settings
    Route::get('/settings',                 [SettingsController::class, 'index'])->name('settings');
    Route::patch('/settings/profile',       [SettingsController::class, 'updateProfile'])->name('settings.profile.update');
    Route::post('/settings/photo/upload',   [SettingsController::class, 'uploadPhoto'])->name('settings.photo.upload');
    Route::delete('/settings/photo',        [SettingsController::class, 'removePhoto'])->name('settings.photo.remove');
    Route::patch('/settings/notifications', [SettingsController::class, 'updateNotifications'])->name('settings.notifications.update');
    Route::patch('/settings/password',      [SettingsController::class, 'updatePassword'])->name('settings.password.update');
    Route::delete('/settings/account',      [SettingsController::class, 'deleteAccount'])->name('settings.account.delete');
    Route::patch('/settings/preferences',   [SettingsController::class, 'updatePreferences'])->name('settings.preferences.update');
    Route::post('/settings/theme',          [SettingsController::class, 'updateTheme'])->name('settings.theme.update');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});