<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowUp extends Model
{
    use HasFactory;

    protected $table = 'friendship_follow_ups';

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

    protected $appends = [
        'average_progress',
        'progress_status',
        'progress_color'
    ];

    /**
     * Relación con el emparejamiento
     */
    public function friendship()
    {
        return $this->belongsTo(Friendship::class);
    }

    /**
     * Relación con el usuario que hizo el seguimiento
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para seguimientos pendientes
     */
    // public function scopePending($query)
    // {
    //     return $query->where('next_follow_up_date', '<=', now())
    //                 ->whereNull('completed_at');
    // }

    /**
     * Scope para un emparejamiento específico
     */
    public function scopeForFriendship($query, $friendshipId)
    {
        return $query->where('friendship_id', $friendshipId);
    }


    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeByPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopeHighProgress($query, $threshold = 4.0)
    {
        return $query->whereRaw('((buddy_progress + peer_buddy_progress + relationship_quality) / 3) >= ?', [$threshold]);
    }

    /**
     * Scope para seguimientos que requieren atención
     */
    public function scopeNeedsAttention($query, $threshold = 2.5)
    {
        return $query->whereRaw('((buddy_progress + peer_buddy_progress + relationship_quality) / 3) < ?', [$threshold]);
    }
    /**
     * Obtener el promedio de progreso
     */
    public function getAverageProgressAttribute()
    {
        return round(($this->buddy_progress + $this->peer_buddy_progress + $this->relationship_quality) / 3, 1);
    }

    /**
     * Obtener el estado del progreso en texto
     */
    public function getProgressStatusAttribute()
    {
        $average = $this->average_progress;
        
        if ($average >= 4.5) return 'Excelente';
        if ($average >= 3.5) return 'Bueno';
        if ($average >= 2.5) return 'Regular';
        if ($average >= 1.5) return 'Bajo';
        return 'Muy Bajo';
    }

    /**
     * Obtener el color del progreso para UI
     */
    public function getProgressColorAttribute()
    {
        $average = $this->average_progress;
        
        if ($average >= 4.5) return 'green';
        if ($average >= 3.5) return 'blue';
        if ($average >= 2.5) return 'yellow';
        if ($average >= 1.5) return 'orange';
        return 'red';
    }

    public function getFriendshipInfoAttribute()
    {
        if (!$this->friendship) return 'Sin información';
        
        $buddy = $this->friendship->buddy ?? null;
        $peerBuddy = $this->friendship->peer_buddy ?? null;
        
        $buddyName = $buddy ? "{$buddy->first_name} {$buddy->last_name}" : 'N/A';
        $peerBuddyName = $peerBuddy ? "{$peerBuddy->first_name} {$peerBuddy->last_name}" : 'N/A';
        
        return trim("{$buddyName} - {$peerBuddyName}");
    }

    /**
     * Obtener tendencia comparando con el seguimiento anterior
     */
    public function getProgressTrendAttribute()
    {
        $previousFollowUp = static::where('friendship_id', $this->friendship_id)
            ->where('created_at', '<', $this->created_at)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$previousFollowUp) {
            return 'stable'; // No hay datos anteriores para comparar
        }

        $currentAverage = $this->average_progress;
        $previousAverage = $previousFollowUp->average_progress;

        if ($currentAverage > $previousAverage + 0.3) {
            return 'improving';
        } elseif ($currentAverage < $previousAverage - 0.3) {
            return 'declining';
        }

        return 'stable';
    }

    /**
     * Obtener estadísticas agregadas para una amistad
     */
    public static function getStatsByFriendship($friendshipId)
    {
        $followUps = static::where('friendship_id', $friendshipId)
            ->orderBy('created_at')
            ->get();

        if ($followUps->isEmpty()) {
            return null;
        }

        return [
            'total_follow_ups' => $followUps->count(),
            'average_buddy_progress' => round($followUps->avg('buddy_progress'), 1),
            'average_peer_buddy_progress' => round($followUps->avg('peer_buddy_progress'), 1),
            'average_relationship_quality' => round($followUps->avg('relationship_quality'), 1),
            'overall_average' => round($followUps->avg(function ($followUp) {
                return ($followUp->buddy_progress + $followUp->peer_buddy_progress + $followUp->relationship_quality) / 3;
            }), 1),
            'latest_follow_up' => $followUps->last(),
            'progress_trend' => $followUps->last()->progress_trend ?? 'stable',
            'needs_attention' => $followUps->last()->average_progress < 2.5
        ];
    }

    /**
     * Obtener resumen global de todos los seguimientos
     */
    public static function getGlobalStats()
    {
        $followUps = static::all();

        if ($followUps->isEmpty()) {
            return [
                'total_follow_ups' => 0,
                'average_scores' => [
                    'buddy_progress' => 0,
                    'peer_buddy_progress' => 0,
                    'relationship_quality' => 0,
                    'overall' => 0
                ],
                'distribution' => [
                    'excelente' => 0,
                    'bueno' => 0,
                    'regular' => 0,
                    'bajo' => 0,
                    'muy_bajo' => 0
                ]
            ];
        }

        $distribution = [
            'excelente' => 0,
            'bueno' => 0,
            'regular' => 0,
            'bajo' => 0,
            'muy_bajo' => 0
        ];

        foreach ($followUps as $followUp) {
            $status = $followUp->progress_status;
            switch ($status) {
                case 'Excelente':
                    $distribution['excelente']++;
                    break;
                case 'Bueno':
                    $distribution['bueno']++;
                    break;
                case 'Regular':
                    $distribution['regular']++;
                    break;
                case 'Bajo':
                    $distribution['bajo']++;
                    break;
                case 'Muy Bajo':
                    $distribution['muy_bajo']++;
                    break;
            }
        }

        return [
            'total_follow_ups' => $followUps->count(),
            'average_scores' => [
                'buddy_progress' => round($followUps->avg('buddy_progress'), 1),
                'peer_buddy_progress' => round($followUps->avg('peer_buddy_progress'), 1),
                'relationship_quality' => round($followUps->avg('relationship_quality'), 1),
                'overall' => round($followUps->avg(function ($followUp) {
                    return ($followUp->buddy_progress + $followUp->peer_buddy_progress + $followUp->relationship_quality) / 3;
                }), 1)
            ],
            'distribution' => $distribution
        ];
    }
}