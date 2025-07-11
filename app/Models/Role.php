<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public static function getDescription($roleName)
    {
        $descriptions = [
            'Encargado del Programa Amistad' => 'Coordina el programa, verifica el emparejamiento y supervisa a los líderes.',
            'Coordinador de Gestión Humana' => 'Administra el personal y coordina la selección y emparejamiento.',
            'Líder de Actividades' => 'Responsable de planificar y coordinar el calendario mensual de actividades.',
            'Líder de Buddies' => 'Coordina y supervisa a los buddies asignados a los nuevos colaboradores.',
            'Líder de PeerBuddies' => 'Coordina y supervisa a los peer buddies que apoyan a colaboradores en transición.',
            'Líder de Tutores' => 'Coordina y supervisa a los tutores asignados para la formación técnica.'
        ];

        return $descriptions[$roleName] ?? 'Descripción no disponible';
    }
}