<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = ['participant_one', 'participant_two', 'last_message_at'];

    protected $casts = ['last_message_at' => 'datetime'];

    public function participantOne()
    {
        return $this->belongsTo(User::class, 'participant_one');
    }

    public function participantTwo()
    {
        return $this->belongsTo(User::class, 'participant_two');
    }

    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function getOtherParticipant(int $userId): User
    {
        return $this->participant_one === $userId
            ? $this->participantTwo
            : $this->participantOne;
    }

    public function hasParticipant(int $userId): bool
    {
        return $this->participant_one === $userId || $this->participant_two === $userId;
    }

    public function unreadCount(int $userId): int
    {
        return $this->messages()
            ->where('sender_id', '!=', $userId)
            ->whereNull('read_at')
            ->count();
    }
}