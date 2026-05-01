<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'google_id',
        'facebook_id',
        'avatar',
        'phone',
        'profile_photo',
        'theme',
        'notification_preferences',
        'app_preferences',
        'last_seen_at',   // NEW: for online/offline status
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'        => 'datetime',
            'password'                 => 'hashed',
            'notification_preferences' => 'array',
            'app_preferences'          => 'array',
            'last_seen_at'             => 'datetime',   // NEW
        ];
    }

    public function getPhotoAttribute(): string
    {
        if ($this->profile_photo) {
            return asset('storage/' . $this->profile_photo);
        }
        if ($this->avatar) {
            return $this->avatar;
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=2ec4a5&color=fff';
    }

    public function bookings()
{
    return $this->hasMany(\App\Models\Booking::class);
}

    /**
     * A user is considered "online" if they were active in the last 2 minutes.
     */
    public function isOnline(): bool
    {
        return $this->last_seen_at && $this->last_seen_at->gt(now()->subMinutes(2));
    }

    /**
     * Returns a human-friendly "last seen" string.
     */
    public function lastSeenText(): string
    {
        if ($this->isOnline()) return 'Online';
        if (!$this->last_seen_at) return 'Offline';
        return 'Last seen ' . $this->last_seen_at->diffForHumans();
    }
   
public function getAvatarUrlAttribute(): ?string
{
    if ($this->profile_photo) {
        // Locally uploaded — serve from storage
        return filter_var($this->profile_photo, FILTER_VALIDATE_URL)
            ? $this->profile_photo
            : \Storage::url($this->profile_photo);
    }

    if ($this->avatar) {
        // Google / Facebook OAuth URL (already a full URL)
        return $this->avatar;
    }

    return null;
}

}