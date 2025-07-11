<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Buddy;

class BuddiesTableSeeder extends Seeder
{
    public function run()
    {
        $buddies = [
            [
                'ci' => '1234567',
                'first_name' => 'Juan',
                'last_name' => 'Pérez',
                'type' => 'buddy',
                'disability' => 'Discapacidad visual',
                'age' => 28,
                'phone' => '70012345',
                'address' => 'Calle 1 #123',
                'email' => 'juan@example.com',
                'interests' => 'Música, lectura, deportes',
                'additional_info' => 'Prefiere contacto por las mañanas'
            ],
            [
                'ci' => '7654321',
                'first_name' => 'María',
                'last_name' => 'Gómez',
                'type' => 'peer_buddy',
                'age' => 32,
                'phone' => '60054321',
                'address' => 'Avenida 2 #456',
                'email' => 'maria@example.com',
                'interests' => 'Pintura, cine',
                'additional_info' => 'Disponible fines de semana'
            ],
            // Agrega más buddies según necesites
        ];

        foreach ($buddies as $buddy) {
            Buddy::create($buddy);
        }
    }
}