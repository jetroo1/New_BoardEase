<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmailContract
{
    use HasFactory, MustVerifyEmailTrait, Notifiable;

    protected $appends = ['avatar_url'];

    protected $fillable = [
        'name', 'email', 'password', 'role',
        'google_id', 'facebook_id', 'avatar',
        'phone', 'profile_photo', 'theme',
        'notification_preferences', 'app_preferences', 'last_seen_at',
        'email_verified_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at'        => 'datetime',
            'password'                 => 'hashed',
            'notification_preferences' => 'array',
            'app_preferences'          => 'array',
            'last_seen_at'             => 'datetime',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────────────────

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function properties()
    {
        return $this->hasMany(Property::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // ── Role Helpers ───────────────────────────────────────────────────────────

    public function isAdmin(): bool  { return $this->role === 'admin'; }
    public function isOwner(): bool  { return $this->role === 'owner'; }
    public function isTenant(): bool { return $this->role === 'tenant'; }

    public function wantsNotification(string $key): bool
    {
        $preferences = $this->notification_preferences ?? [];

        if (! is_array($preferences) || ! array_key_exists($key, $preferences)) {
            return true;
        }

        return (bool) $preferences[$key];
    }

    public function redirectPath(): string
    {
        return match($this->role) {
            'admin' => '/admin/dashboard',
            'owner' => '/owner/dashboard',
            default => '/search',
        };
    }

    // ── Online / Avatar Helpers ────────────────────────────────────────────────

    public function isOnline(): bool
    {
        return $this->last_seen_at && $this->last_seen_at->gt(now()->subMinutes(2));
    }

    public function lastSeenText(): string
    {
        if ($this->isOnline()) return 'Online';
        if (!$this->last_seen_at) return 'Offline';
        return 'Last seen ' . $this->last_seen_at->diffForHumans();
    }

    public function getPhotoAttribute(): string
{
    if ($this->profile_photo) {
        return filter_var($this->profile_photo, FILTER_VALIDATE_URL)
            ? $this->profile_photo
            : asset('storage/' . $this->profile_photo);
    }
    if ($this->avatar) {
        // Fix Google avatar URLs — ensure they use https
        $avatar = $this->avatar;
        if (str_contains($avatar, 'googleusercontent.com')) {
            $avatar = str_replace('http://', 'https://', $avatar);
            // Strip size param and set to 200px
            $avatar = preg_replace('/=s\d+-c/', '=s200-c', $avatar);
        }
        return $avatar;
    }
    return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=2ec4a5&color=fff';
}

    public function getAvatarUrlAttribute(): ?string
{
    if ($this->profile_photo) {
        return filter_var($this->profile_photo, FILTER_VALIDATE_URL)
            ? $this->profile_photo
            : \Storage::url($this->profile_photo);
    }
    if ($this->avatar) {
        $avatar = $this->avatar;
        if (str_contains($avatar, 'googleusercontent.com')) {
            $avatar = str_replace('http://', 'https://', $avatar);
            $avatar = preg_replace('/=s\d+-c/', '=s200-c', $avatar);
        }
        return $avatar;
    }
    return null;
}
}
