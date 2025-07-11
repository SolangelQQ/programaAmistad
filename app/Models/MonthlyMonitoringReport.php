<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyMonitoringReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'monitor_name',
        'monitoring_period',
        'friendship_id',
        'general_evaluation',
        'meeting_frequency',
        'progress_areas',
        'challenges',
        'tutor_participation',
        'leader_participation',
        'tutor_satisfaction',
        'leader_satisfaction',
        'suggested_actions',
        'requires_attention',
        'specific_observations',
    ];

    protected $casts = [
        'progress_areas' => 'array',
        'challenges' => 'array',
        'suggested_actions' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function friendship()
    {
        return $this->belongsTo(Friendship::class);
    }
    public function scopeByPeriod($query, $from, $to)
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    public function scopeRequiresAttention($query)
    {
        return $query->where('requires_attention', 'si');
    }

    public function scopeByEvaluation($query, $evaluation)
    {
        return $query->where('general_evaluation', $evaluation);
    }
    public function scopeByMonitor($query, $monitorName)
    {
        return $query->where('monitor_name', $monitorName);
    }

    public function scopeRequiringAttention($query)
    {
        return $query->where('requires_attention', 'si');
    }

    public function scopeActiveLeaders($query)
    {
        return $query->whereIn('leader_participation', ['muy-activo', 'activo']);
    }
    public function scopeSatisfiedLeaders($query)
    {
        return $query->whereIn('leader_satisfaction', ['muy-satisfecho', 'satisfecho']);
    }
    public function getSummary()
{
    return [
        'id' => $this->id,
        'monitor_name' => $this->monitor_name,
        'monitoring_period' => $this->monitoring_period,
        'friendship_id' => $this->friendship_id,
        'general_evaluation' => $this->general_evaluation,
        'meeting_frequency' => $this->meeting_frequency,
        'tutor_participation' => $this->tutor_participation,
        'leader_participation' => $this->leader_participation,
        'tutor_satisfaction' => $this->tutor_satisfaction,
        'leader_satisfaction' => $this->leader_satisfaction,
        'requires_attention' => $this->requires_attention,
        'progress_areas_count' => is_array($this->progress_areas) ? count($this->progress_areas) : 0,
        'challenges_count' => is_array($this->challenges) ? count($this->challenges) : 0,
        'suggested_actions_count' => is_array($this->suggested_actions) ? count($this->suggested_actions) : 0,
        'has_observations' => !empty($this->specific_observations),
        'created_at' => $this->created_at,
        'updated_at' => $this->updated_at,
        'friendship' => $this->friendship ? [
            'id' => $this->friendship->id,
            'buddy_name' => optional($this->friendship->buddy)->name ?? 'N/A', // Corregido
            'peer_buddy_name' => optional($this->friendship->peerBuddy)->name ?? 'N/A', // Corregido
            'status' => $this->friendship->status
        ] : null
    ];
}
    public function getStatistics()
    {
        return [
            'general_info' => [
                'monitor' => $this->monitor_name,
                'period' => $this->monitoring_period,
                'evaluation' => $this->general_evaluation,
                'frequency' => $this->meeting_frequency,
                'requires_attention' => $this->requires_attention === 'si'
            ],
            'participation' => [
                'tutor' => $this->tutor_participation,
                'leader' => $this->leader_participation
            ],
            'satisfaction' => [
                'tutor' => $this->tutor_satisfaction,
                'leader' => $this->leader_satisfaction
            ],
            'progress_areas' => $this->progress_areas ?? [],
            'challenges' => $this->challenges ?? [],
            'suggested_actions' => $this->suggested_actions ?? [],
            'has_specific_observations' => !empty($this->specific_observations),
            'timestamps' => [
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at
            ]
        ];
    }
    public function getLeaderNameAttribute()
    {
        if ($this->friendship && $this->friendship->peer_buddy) {
            return $this->friendship->peer_buddy->first_name . ' ' . $this->friendship->peer_buddy->last_name;
        }
        return $this->monitor_name ?? 'N/A';
    }
    public function getLeaderInitialsAttribute()
    {
        $name = $this->getLeaderNameAttribute();
        $words = explode(' ', $name);
        $initials = '';
        foreach (array_slice($words, 0, 2) as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
        return $initials ?: 'NA';
    }
}