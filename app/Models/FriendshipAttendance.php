<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FriendshipAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'friendship_id',
        'date',
        'buddy_attended',
        'peer_buddy_attended',
        'notes'
    ];

    protected $casts = [
        'date' => 'date',
        'buddy_attended' => 'boolean',
        'peer_buddy_attended' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public static $rules = [
        'friendship_id' => 'required|exists:friendships,id',
        'date' => 'required|date',
        'buddy_attended' => 'boolean',
        'peer_buddy_attended' => 'boolean',
        'notes' => 'nullable|string|max:500'
    ];

    /**
     * Obtiene el emparejamiento relacionado
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function friendship()
    {
        return $this->belongsTo(Friendship::class);
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('date', $date);
    }

    public function scopeBuddyPresent($query)
    {
        return $query->where('buddy_attended', true);
    }

    public function scopePeerBuddyPresent($query)
    {
        return $query->where('peer_buddy_attended', true);
    }

    public function getBothAttendedAttribute()
    {
        return $this->buddy_attended && $this->peer_buddy_attended;
    }

    public function getAttendanceStatusAttribute()
    {
        if ($this->both_attended) return 'Ambos asistieron';
        if ($this->buddy_attended) return 'Solo Buddy asistió';
        if ($this->peer_buddy_attended) return 'Solo PeerBuddy asistió';
        return 'Ninguno asistió';
    }

public function attendanceRecords()
{
    return $this->hasMany(FriendshipAttendance::class)->latest('updated_at');
}

public function followUps()
{
    return $this->hasMany(FollowUp::class)->latest('created_at');
}
}