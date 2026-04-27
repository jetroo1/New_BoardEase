<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'address',
        'price',
        'room_type',
        'amenities',
        'latitude',
        'longitude',
        'image',
        'status',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function reviews()
{
    return $this->hasMany(Review::class);
}

    public function isFavoritedBy($userId)
{
    return $this->favorites()->where('user_id', $userId)->exists();
}
}
