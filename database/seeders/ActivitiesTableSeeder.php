<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Activity;
use Carbon\Carbon;

class ActivitiesTableSeeder extends Seeder
{
    public function run()
    {
        $activities = [
            [
                'title' => 'Tarde de Juegos de Mesa',
                'description' => 'Una tarde divertida con juegos de mesa clásicos y modernos para todos los gustos.',
                'date' => Carbon::today()->addDays(5)->format('Y-m-d'),
                'start_time' => '15:00:00',
                'end_time' => '18:00:00',
                'location' => 'Centro Comunitario Principal',
                'type' => 'recreational',
                'status' => 'scheduled',
               
                'photos' => json_encode(['activities/game-night-1.jpg', 'activities/game-night-2.jpg']),
            ],
            [
                'title' => 'Taller de Arte Terapia',
                'description' => 'Explora tu creatividad y relájate con técnicas básicas de pintura y dibujo.',
                'date' => Carbon::today()->addDays(10)->format('Y-m-d'),
                'start_time' => '10:00:00',
                'end_time' => '12:30:00',
                'location' => 'Sala de Arte del Centro',
                'type' => 'educational',
                'status' => 'scheduled',
               
            
            ],
            [
                'title' => 'Partido de Fútbol Amistoso',
                'description' => 'Partido amistoso de fútbol para todos los niveles. ¡Trae tu espíritu deportivo!',
                'date' => Carbon::today()->addDays(3)->format('Y-m-d'),
                'start_time' => '17:00:00',
                'end_time' => '19:00:00',
                'location' => 'Cancha Deportiva Municipal',
                'type' => 'sports',
                'status' => 'in_progress',
             
                'photos' => json_encode(['activities/soccer-match.jpg']),
            ],
            [
                'title' => 'Visita al Museo Local',
                'description' => 'Recorrido guiado por la exposición temporal de arte contemporáneo.',
                'date' => Carbon::today()->addDays(7)->format('Y-m-d'),
                'start_time' => '11:00:00',
                'end_time' => '13:00:00',
                'location' => 'Museo de la Ciudad',
                'type' => 'cultural',
                'status' => 'scheduled',
                
            ],
            [
                'title' => 'Cena de Integración',
                'description' => 'Cena comunitaria para conocernos mejor en un ambiente relajado.',
                'date' => Carbon::today()->addDays(14)->format('Y-m-d'),
                'start_time' => '19:30:00',
                'end_time' => '22:00:00',
                'location' => 'Restaurante La Terraza',
                'type' => 'social',
                'status' => 'scheduled',
             
            ],
            [
                'title' => 'Taller de Cocina Saludable',
                'description' => 'Aprende a preparar comidas nutritivas y deliciosas con ingredientes locales.',
                'date' => Carbon::today()->addDays(8)->format('Y-m-d'),
                'start_time' => '16:00:00',
                'end_time' => '18:30:00',
                'location' => 'Cocina del Centro Comunitario',
                'type' => 'educational',
                'status' => 'completed',
              
                'photos' => json_encode(['activities/cooking-workshop-1.jpg', 'activities/cooking-workshop-2.jpg']),
            ],
            [
                'title' => 'Sesión de Yoga al Aire Libre',
                'description' => 'Clase de yoga para todos los niveles en el parque central.',
                'date' => Carbon::today()->addDays(6)->format('Y-m-d'),
                'start_time' => '08:00:00',
                'end_time' => '09:30:00',
                'location' => 'Parque Central',
                'type' => 'sports',
                'status' => 'scheduled',
               
            ],
        ];

        foreach ($activities as $activity) {
            Activity::create($activity);
        }

        $this->command->info('Actividades de prueba creadas exitosamente!');
    }
}