<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class Activity extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'date',
        'start_time',
        'end_time',
        'location',
        'type',
        'status',
        'photos'
    ];
    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'photos' => 'array'
    ];
    // Accessor para formato de fecha
   public function getFormattedDateAttribute()
    {
        try {
            if ($this->date) {
                return Carbon::parse($this->date)->locale('es')->isoFormat('dddd, D [de] MMMM');
            }
            return '';
        } catch (\Exception $e) {
            return $this->date ?? '';
        }
    }
    public function getFormattedTimeAttribute()
    {
        try {
            $startTime = $this->start_time ? Carbon::parse($this->start_time)->format('H:i') : '';
            $endTime = $this->end_time ? Carbon::parse($this->end_time)->format('H:i') : '';
            
            return $startTime . ($endTime ? ' - ' . $endTime : '');
        } catch (\Exception $e) {
            return ($this->start_time ?? '') . ($this->end_time ? ' - ' . $this->end_time : '');
        }
    }
    public function scopeForMonth($query, $year, $month)
    {
        return $query->whereYear('date', $year)
                    ->whereMonth('date', $month);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', now()->toDateString())
                    ->where('status', '!=', 'cancelled')
                    ->orderBy('date', 'asc')
                    ->orderBy('start_time', 'asc');
    }

    // Relación con participantes 
    public function participants()
    {
        
        if (\Schema::hasTable('activity_participants')) {
            return $this->hasMany(ActivityParticipant::class);
        }
        
        return collect([]);
    }    protected $dates = ['date', 'created_at', 'updated_at'];

}