<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FriendshipFollowUp extends Model
{
    use HasFactory;

    protected $fillable = [
        'friendship_id',
        'user_id',
        'buddy_progress',
        'peer_buddy_progress',
        'relationship_quality',
        'goals_achieved',
        'challenges_faced',
    ];

    protected $casts = [
        'buddy_progress' => 'integer',
        'peer_buddy_progress' => 'integer',
        'relationship_quality' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function friendship()
    {
        return $this->belongsTo(Friendship::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // public function getAverageProgressAttribute()
    // {
    //     return ($this->buddy_progress + $this->peer_buddy_progress + $this->relationship_quality) / 3;
    // }
    public function getAverageProgressAttribute()
    {
        $scores = array_filter([
            $this->buddy_progress,
            $this->peer_buddy_progress,
            $this->relationship_quality
        ]);
        
        return count($scores) > 0 ? round(array_sum($scores) / count($scores), 1) : 0;
    }

    public function scopeByFriendship($query, $friendshipId)
    {
        return $query->where('friendship_id', $friendshipId);
    }

    public function scopeInPeriod($query, $from, $to)
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    /**
     * Scope para seguimientos de una amistad específica
     */
    public function scopeForFriendship($query, $friendshipId)
    {
        return $query->where('friendship_id', $friendshipId);
    }
    public function scopeRecent($query, $limit = 10)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }
}