<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            ['name' => 'Encargado del Programa Amistad'],
            ['name' => 'Coordinador de Gestión Humana'],
            ['name' => 'Líder de Actividades'],
            ['name' => 'Líder de Buddies'],
            ['name' => 'Líder de PeerBuddies'],
            ['name' => 'Líder de Tutores'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}