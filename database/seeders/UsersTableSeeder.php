<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        //  obtenemos el rol de admin 
        $adminRole = Role::where('name', 'Encargado del Programa Amistad')->first();
        
        if (!$adminRole) {
            $adminRole = Role::create(['name' => 'Encargado del Programa Amistad']);
        }

        // evitar duplicados
        $adminExists = User::where('email', 'admin@gmail.com')->exists();

        // Solo creamos el usuario si no existe
        if (!$adminExists) {
            User::create([
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('password'), // bycrpt de laravel
                'role_id' => $adminRole->id,
            ]
        );
        }

    }
}