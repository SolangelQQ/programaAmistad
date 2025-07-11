<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MonthlyMonitoringReport;
use App\Models\Friendship;
use App\Models\User;

class MonthlyMonitoringReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $participationLevels = ['muy-activo', 'activo', 'moderado', 'pasivo', 'muy-pasivo'];
        $satisfactionLevels = ['muy-satisfecho', 'satisfecho', 'neutral', 'insatisfecho', 'muy-insatisfecho'];
        $evaluationLevels = ['excelente', 'buena', 'regular', 'deficiente', 'critica'];
        $frequencies = ['semanal', 'quincenal', 'mensual', 'irregular'];
        $periods = ['2024-01', '2024-02', '2024-03', '2024-04', '2024-05', '2024-06'];

        // Verificar si existen amistades, si no, usar null
        $friendshipIds = Friendship::pluck('id')->toArray();
        
        // Si no hay amistades, crear algunas para prueba
        if (empty($friendshipIds)) {
            // Verificar si hay usuarios para crear amistades
            $userIds = User::pluck('id')->toArray();
            
            if (count($userIds) >= 2) {
                // Crear algunas amistades de prueba
                for ($j = 0; $j < min(5, floor(count($userIds) / 2)); $j++) {
                    $buddyId = $userIds[array_rand($userIds)];
                    $peerBuddyId = $userIds[array_rand($userIds)];
                    
                    // Asegurar que no sean el mismo usuario
                    while ($buddyId === $peerBuddyId && count($userIds) > 1) {
                        $peerBuddyId = $userIds[array_rand($userIds)];
                    }
                    
                    $friendship = Friendship::create([
                        'buddy_id' => $buddyId,
                        'peer_buddy_id' => $peerBuddyId,
                        'status' => 'active',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    
                    $friendshipIds[] = $friendship->id;
                }
            }
        }

        // Crear reportes de prueba
        for ($i = 1; $i <= 15; $i++) {
            // Usar un friendship_id aleatorio si existe, sino null
            $friendshipId = !empty($friendshipIds) ? $friendshipIds[array_rand($friendshipIds)] : null;
            
            MonthlyMonitoringReport::create([
                'monitor_name' => 'Monitor ' . $i,
                'monitoring_period' => $periods[array_rand($periods)],
                'friendship_id' => $friendshipId,
                'general_evaluation' => $evaluationLevels[array_rand($evaluationLevels)],
                'meeting_frequency' => $frequencies[array_rand($frequencies)],
                'progress_areas' => [
                    'Comunicación mejorada',
                    'Mayor participación en actividades',
                    'Desarrollo de habilidades sociales'
                ],
                'challenges' => [
                    'Dificultades de horario',
                    'Falta de motivación ocasional',
                    'Barreras de comunicación'
                ],
                'tutor_participation' => $participationLevels[array_rand($participationLevels)],
                'leader_participation' => $participationLevels[array_rand($participationLevels)],
                'tutor_satisfaction' => $satisfactionLevels[array_rand($satisfactionLevels)],
                'leader_satisfaction' => $satisfactionLevels[array_rand($satisfactionLevels)],
                'suggested_actions' => [
                    'Programar más reuniones',
                    'Implementar actividades dinámicas',
                    'Mejorar la comunicación'
                ],
                'requires_attention' => rand(0, 1) ? 'si' : 'no',
                'specific_observations' => 'Observaciones específicas para el reporte ' . $i . '. El líder muestra ' . 
                    ($i % 2 ? 'buen' : 'regular') . ' desempeño en las actividades programadas.'
            ]);
        }
    }
}