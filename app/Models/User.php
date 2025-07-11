<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'city',
        'role_id',
        'google_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'settings' => 'array',
        ];
    }
    public function setPassqoedAttribute($value){
        $this->attributes['password'] = bcrypt($value);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    public function roles()
    {
        return collect([$this->role])->filter();
    }

    public function getRoleNameAttribute()
    {
        return $this->role ? $this->role->name : null;
    }
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

public function friendshipsAsLeader()
{
    return $this->hasMany(Friendship::class, 'buddy_leader_id');
}

public function friendshipsAsPeer()
{
    return $this->hasMany(Friendship::class, 'peer_buddy_leader_id');
}

    public function friendships(): HasMany
    {
        return $this->hasMany(Friendship::class);
    }

    public function allFriendships()
    {
        return Friendship::where('buddy_id', $this->id)
                        ->orWhere('peer_buddy_id', $this->id);
    }

    public function relatedReports()
    {
        return MonthlyMonitoringReport::whereHas('friendship', function($query) {
            $query->where('buddy_id', $this->id)
                  ->orWhere('peer_buddy_id', $this->id);
        });
    }

    public function getInitialsAttribute()
    {
        if (empty($this->name)) return 'XX';
        
        $words = explode(' ', trim($this->name));
        $initials = '';
        
        foreach ($words as $word) {
            if (strlen($word) > 0) {
                $initials .= strtoupper($word[0]);
            }
            if (strlen($initials) >= 2) break;
        }
        
        return $initials ?: 'XX';
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function sentNotifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'sender_id');
    }

    public function getUnreadNotificationsCountAttribute(): int
    {
        return $this->notifications()->whereNull('read_at')->count();
    }

    public function hasUnreadNotifications(): bool
    {
        return $this->notifications()->whereNull('read_at')->exists();
    }
}
