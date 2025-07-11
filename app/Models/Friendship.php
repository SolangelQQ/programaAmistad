<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Friendship extends Model
{
    use HasFactory;

    protected $fillable = [
        'buddy_id',
        'peer_buddy_id',
        'buddy_leader_id',
        'peer_buddy_leader_id',
        'status',
        'start_date',
        'end_date',
        'notes'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    

    public function buddy(): BelongsTo
    {
        return $this->belongsTo(Buddy::class, 'buddy_id');
    }

    public function peerBuddy(): BelongsTo
    {
        return $this->belongsTo(Buddy::class, 'peer_buddy_id');
    }

    /**
     * Relación con el líder de buddy
     */
    public function buddyLeader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buddy_leader_id');
    }

    /**
     * Relación con el líder de peer buddy (Usuario con rol de Líder de PeerBuddies)
     */
    public function peerBuddyLeader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'peer_buddy_leader_id');
    }

    /**
     * Scope para obtener amistades con sus líderes
     */
    public function scopeWithLeaders($query)
    {
        return $query->with(['buddy', 'peerBuddy', 'buddyLeader.role', 'peerBuddyLeader.role']);
    }

    /**
     * Relación con los seguimientos (follow-ups)
     */
    public function followUps(): HasMany
    {
        return $this->hasMany(FollowUp::class);
    }

    public function monthlyReports()
    {
        return $this->hasMany(MonthlyMonitoringReport::class);
    }


    /**
     * Obtener el último seguimiento
     */
    public function latestFollowUp(): HasOne
    {
        return $this->hasOne(FollowUp::class)->latestOfMany();
    }

    /**
     * Obtener seguimientos pendientes
     */
    public function pendingFollowUps(): HasMany
    {
        return $this->hasMany(FollowUp::class)->where('status', 'pending');
    }

    /**
     * Scope para emparejamientos activos
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Emparejado');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('buddy_id', $userId)->orWhere('peer_buddy_id', $userId);
    }
    
    /**
     * Scope para emparejamientos inactivos
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'Inactivo');
    }

    /**
     * Scope para emparejamientos finalizados
     */
    public function scopeFinished($query)
    {
        return $query->where('status', 'Finalizado');
    }

    /**
     * Obtener el progreso promedio del último seguimiento
     */
    public function getLastProgressAverageAttribute()
    {
        $lastFollowUp = $this->latestFollowUp;
        return $lastFollowUp ? $lastFollowUp->average_progress : null;
    }

    /**
     * Verificar si necesita seguimiento
     */
    public function needsFollowUp()
    {
        $lastFollowUp = $this->latestFollowUp;
        
        if (!$lastFollowUp) {
            return true; // Si no tiene seguimientos, necesita uno
        }

        // Si tiene fecha de próximo seguimiento y ya pasó
        if ($lastFollowUp->next_follow_up_date && $lastFollowUp->next_follow_up_date <= now()) {
            return true;
        }

        // Si el último seguimiento fue hace más de un mes
        if ($lastFollowUp->created_at->diffInDays(now()) > 30) {
            return true;
        }

        return false;
    }

    /**
     * Obtener el estado de seguimiento
     */
    public function getFollowUpStatusAttribute()
    {
        if ($this->needsFollowUp()) {
            return 'Pendiente';
        }

        $lastFollowUp = $this->latestFollowUp;
        if (!$lastFollowUp) {
            return 'Sin seguimiento';
        }

        return 'Al día';
    }

    /**
     * Obtener la duración del emparejamiento en días
     */
    public function getDurationInDaysAttribute()
    {
        $endDate = $this->end_date ?? now();
        return $this->start_date ? $this->start_date->diffInDays($endDate) : 0;
    }

    /**
     * Verificar si el emparejamiento está activo
     */
    public function isActive()
    {
        return $this->status === 'Emparejado';
    }

    /**
     * Obtener el color de estado para la UI
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'Emparejado' => 'green',
            'Inactivo' => 'yellow',
            'Finalizado' => 'gray',
            default => 'gray'
        };
    }

    /**
     * Relación con registros de asistencia
     */
   

    public function attendanceRecords()
    {
        return $this->hasMany(FriendshipAttendance::class);
    }

    /**
     * Obtiene los registros de asistencia ordenados por fecha descendente
     */
    public function recentAttendance()
    {
        return $this->hasMany(FriendshipAttendance::class)->orderBy('date', 'desc');
    }

    /**
     * Obtiene estadísticas de asistencia
     */
    public function getAttendanceStatsAttribute()
    {
        $records = $this->attendanceRecords;
        
        if ($records->isEmpty()) {
            return null;
        }

        $total = $records->count();
        $buddyAttended = $records->where('buddy_attended', true)->count();
        $peerAttended = $records->where('peer_buddy_attended', true)->count();
        $bothAttended = $records->where('buddy_attended', true)
                              ->where('peer_buddy_attended', true)->count();

        return [
            'total_records' => $total,
            'buddy_attended' => $buddyAttended,
            'peer_attended' => $peerAttended,
            'both_attended' => $bothAttended,
            'buddy_percentage' => round(($buddyAttended / $total) * 100, 1),
            'peer_percentage' => round(($peerAttended / $total) * 100, 1),
            'both_percentage' => round(($bothAttended / $total) * 100, 1),
        ];
    }

    /**
     * Scope para emparejamientos con asistencias
     */
    public function scopeWithAttendance($query)
    {
        return $query->whereHas('attendanceRecords');
    }

    /**
     * Scope para emparejamientos sin asistencias
     */
    public function scopeWithoutAttendance($query)
    {
        return $query->whereDoesntHave('attendanceRecords');
    }

    //aca
    public function scopeByLeader($query, $leaderId)
    {
        return $query->where(function($q) use ($leaderId) {
            $q->where('buddy_leader_id', $leaderId)
              ->orWhere('peer_buddy_leader_id', $leaderId);
        });
    }

    public function scopeInPeriod($query, $dateFrom, $dateTo)
    {
        return $query->whereBetween('start_date', [$dateFrom, $dateTo]);
    }

    public function getFriendshipNameAttribute()
    {
        $buddyName = $this->buddy ? $this->buddy->name : 'N/A';
        $peerBuddyName = $this->peerBuddy ? $this->peerBuddy->name : 'N/A';
        return $buddyName . ' - ' . $peerBuddyName;
    }

    // Método helper para verificar si un usuario es líder de esta amistad
    public function isLeaderOf($userId)
    {
        return $this->buddy_leader_id == $userId || $this->peer_buddy_leader_id == $userId;
    }
}