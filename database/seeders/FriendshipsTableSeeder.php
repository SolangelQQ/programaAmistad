<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Friendship;
use App\Models\Buddy;
use Carbon\Carbon;

class FriendshipsTableSeeder extends Seeder
{
    public function run()
    {
        // Obtener buddies y peer buddies existentes
        $buddies = Buddy::where('type', 'buddy')->get();
        $peerBuddies = Buddy::where('type', 'peer_buddy')->get();

        // Verificar que hay suficientes registros
        if ($buddies->isEmpty() || $peerBuddies->isEmpty()) {
            $this->command->info('No hay suficientes buddies o peer buddies para crear friendships!');
            $this->command->info('Por favor ejecuta BuddiesTableSeeder primero.');
            return;
        }

        // Crear friendships de prueba
        $friendships = [];
        $statuses = ['Emparejado', 'En progreso', 'Finalizado'];
        $startDate = Carbon::now()->subMonths(3);

        // Crear 10 friendships de ejemplo
        for ($i = 0; $i < min(10, $buddies->count(), $peerBuddies->count()); $i++) {
            $endDate = rand(0, 1) ? $startDate->copy()->addMonths(rand(1, 6)) : null;
            
            $friendships[] = [
                'buddy_id' => $buddies[$i]->id,
                'peer_buddy_id' => $peerBuddies[$i]->id,
                'status' => $endDate ? 'Finalizado' : $statuses[array_rand($statuses)],
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate?->format('Y-m-d'),
                'notes' => $this->generateRandomNotes(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insertar en la base de datos
        Friendship::insert($friendships);

        $this->command->info('Friendships de prueba creadas exitosamente!');
    }

    protected function generateRandomNotes(): string
    {
        $notes = [
            'Pareja con buena conexión desde el inicio',
            'Requiere seguimiento semanal',
            'Intereses comunes: música y deportes',
            'Plan de actividades establecido',
            'Primera reunión exitosa',
            'Programar evaluación mensual',
            'Buena comunicación entre la pareja',
            'Necesitan más actividades en común',
            'Progreso constante observado',
            'Recomendación: aumentar frecuencia de encuentros'
        ];

        return $notes[array_rand($notes)];
    }
}