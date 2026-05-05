<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id', 'property_id',
        'start_date', 'end_date',
        'status', 'total_price',
    ];

    protected $casts = [
        'start_date'  => 'date',
        'end_date'    => 'date',
        'total_price' => 'decimal:2',
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

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeForTenant($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForOwner($query, $userId)
    {
        return $query->whereHas('property', fn($q) => $q->where('user_id', $userId));
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function isActiveStay(): bool
    {
        return $this->status === 'confirmed'
            && $this->start_date->lte(today())
            && $this->end_date->gte(today());
    }

    public function isCancellable(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    public function isPending(): bool   { return $this->status === 'pending'; }
    public function isConfirmed(): bool { return $this->status === 'confirmed'; }
    public function isCompleted(): bool { return $this->status === 'completed'; }
    public function isCancelled(): bool { return $this->status === 'cancelled'; }
}