<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'property_id',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    public function isActiveStay(): bool
    {
        return $this->status === 'confirmed'
            && $this->start_date->lte(today())
            && $this->end_date->gte(today());
    }
}