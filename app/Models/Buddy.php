<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buddy extends Model
{
    use HasFactory;

    protected $fillable = [
        'ci',
        'first_name',
        'last_name',
        'type',
        'is_leader',
        'disability',
        'age',
        'phone',
        'address',
        'email',
        'interests',
        'additional_info'
    ];

    protected $casts = [
        'is_leader' => 'boolean',
    ];

    // En el modelo Buddy.php
public function getFullNameAttribute()
{
    return $this->first_name . ' ' . $this->last_name;
}

    /**
     * Relación con las amistades donde este buddy es el buddy principal
     */
    public function friendshipsAsBuddy(): HasMany
    {
        return $this->hasMany(Friendship::class, 'buddy_id');
    }

    /**
     * Relación con las amistades donde este buddy es el peer buddy
     */
    public function friendshipsAsPeerBuddy(): HasMany
    {
        return $this->hasMany(Friendship::class, 'peer_buddy_id');
    }

    /**
     * Relación con las amistades donde este buddy es líder de buddy
     */
    public function friendshipsAsBuddyLeader(): HasMany
    {
        return $this->hasMany(Friendship::class, 'buddy_leader_id');
    }

    /**
     * Relación con las amistades donde este buddy es líder de peer buddy
     */
    public function friendshipsAsPeerBuddyLeader(): HasMany
    {
        return $this->hasMany(Friendship::class, 'peer_buddy_leader_id');
    }

    /**
     * Scope para obtener solo líderes
     */
    public function scopeLeaders($query)
    {
        return $query->where('is_leader', true);
    }

    /**
     * Scope para obtener líderes de buddy
     */
    public function scopeBuddyLeaders($query)
    {
        return $query->where('type', 'buddy')->where('is_leader', true);
    }

    /**
     * Scope para obtener líderes de peer buddy
     */
    public function scopePeerBuddyLeaders($query)
    {
        return $query->where('type', 'peer_buddy')->where('is_leader', true);
    }

    /**
     * Verificar si el buddy es líder
     */
    public function isLeader(): bool
    {
        return $this->is_leader;
    }

    /**
     * Obtener todas las amistades relacionadas (como buddy, peer buddy o líder)
     */
    public function getAllRelatedFriendships()
    {
        return Friendship::where('buddy_id', $this->id)
            ->orWhere('peer_buddy_id', $this->id)
            ->orWhere('buddy_leader_id', $this->id)
            ->orWhere('peer_buddy_leader_id', $this->id)
            ->get();
    }


    public function scopeBuddies($query)
    {
        return $query->where('type', 'buddy');
    }

    public function scopePeerBuddies($query)
    {
        return $query->where('type', 'peer_buddy');
    }
    public function activeFriendship()
    {
        return $this->hasOne(Friendship::class, 'buddy_id')
            ->where('status', 'Emparejado')
            ->latest();
    }
}