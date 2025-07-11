<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sender_id',
        'title',
        'message',
        'type',
        'read_at',
        'data'
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'data' => 'array'
    ];

    /**
     * Relación con el usuario que recibe la notificación
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con el usuario que envía la notificación
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Scope para notificaciones no leídas
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope para notificaciones leídas
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Verificar si la notificación está leída
     */
    public function isRead()
    {
        return !is_null($this->read_at);
    }

    /**
     * Marcar como leída
     */
    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

    /**
     * Obtener el ícono según el tipo de notificación
     */
    public function getIconAttribute()
    {
        $icons = [
            'info' => 'information-circle',
            'success' => 'check-circle',
            'warning' => 'exclamation-triangle',
            'error' => 'x-circle'
        ];

        return $icons[$this->type] ?? 'bell';
    }

    /**
     * Obtener la clase CSS según el tipo
     */
    public function getTypeClassAttribute()
    {
        $classes = [
            'info' => 'text-blue-500',
            'success' => 'text-green-500',
            'warning' => 'text-yellow-500',
            'error' => 'text-red-500'
        ];

        return $classes[$this->type] ?? 'text-gray-500';
    }
}