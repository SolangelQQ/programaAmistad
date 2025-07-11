<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MonthlyMonitoringReport;
use App\Models\Friendship;
use App\Models\User;
use Illuminate\Http\Request;

class LiderazgoController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Obtener reportes de monitoreo con sus relaciones
            $reports = MonthlyMonitoringReport::with([
                'friendship.buddy', 
                'friendship.peer_buddy'
            ])
            ->orderBy('created_at', 'desc')
            ->get();

            // Si no hay reportes, devolver datos vacíos pero válidos
            if ($reports->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'stats' => [
                        'totalLeaders' => 0,
                        'veryActive' => 0,
                        'needsAttention' => 0,
                        'avgSatisfaction' => '0%'
                    ],
                    'leaders' => [],
                    'charts' => [
                        'participation' => [
                            'labels' => ['Muy Activo', 'Activo', 'Moderado', 'Pasivo', 'Muy Pasivo'],
                            'data' => [0, 0, 0, 0, 0]
                        ],
                        'satisfaction' => [
                            'labels' => ['Excelente', 'Bueno', 'Regular', 'Bajo', 'Muy Bajo'],
                            'data' => [0, 0, 0, 0, 0]
                        ]
                    ]
                ]);
            }

            // Calcular estadísticas
            $stats = $this->calculateStats($reports);
            
            // Preparar datos de líderes
            $leaders = $this->prepareLeadersData($reports);
            
            // Preparar datos para gráficos
            $charts = $this->prepareChartsData($reports);

            return response()->json([
                'success' => true,
                'stats' => $stats,
                'leaders' => $leaders,
                'charts' => $charts
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en LiderazgoController: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar datos: ' . $e->getMessage()
            ], 500);
        }
    }

    private function calculateStats($reports)
    {
        // Obtener amistades únicas (líderes únicos)
        $uniqueFriendships = $reports->pluck('friendship_id')->unique();
        $totalLeaders = $uniqueFriendships->count();
        
        // Contar líderes muy activos (leader_participation = muy-activo o activo)
        $veryActive = $reports->filter(function($report) {
            return in_array($report->leader_participation, ['muy-activo', 'activo']);
        })->pluck('friendship_id')->unique()->count();
        
        // Contar líderes que requieren atención
        $needsAttention = $reports->where('requires_attention', 'si')
            ->pluck('friendship_id')->unique()->count();
        
        // Calcular satisfacción promedio basada en leader_satisfaction
        $satisfactionMapping = [
            'muy-satisfecho' => 5,
            'satisfecho' => 4,
            'neutral' => 3,
            'insatisfecho' => 2,
            'muy-insatisfecho' => 1
        ];
        
        $satisfactionSum = 0;
        $satisfactionCount = 0;
        
        foreach ($reports as $report) {
            if (isset($satisfactionMapping[$report->leader_satisfaction])) {
                $satisfactionSum += $satisfactionMapping[$report->leader_satisfaction];
                $satisfactionCount++;
            }
        }
        
        $avgSatisfaction = $satisfactionCount > 0 ? 
            round(($satisfactionSum / $satisfactionCount / 5) * 100) : 0;

        return [
            'totalLeaders' => $totalLeaders,
            'veryActive' => $veryActive,
            'needsAttention' => $needsAttention,
            'avgSatisfaction' => $avgSatisfaction . '%'
        ];
    }

    private function prepareLeadersData($reports)
    {
        $leaders = [];
        
        // Agrupar por friendship_id para obtener el último reporte de cada líder
        $latestReports = $reports->groupBy('friendship_id')->map(function($group) {
            return $group->sortByDesc('created_at')->first();
        });

        foreach ($latestReports as $report) {
            $friendship = $report->friendship;
            $peerBuddy = $friendship ? $friendship->peer_buddy : null;
            
            // Determinar el nombre del líder
            if ($peerBuddy) {
                $leaderName = $peerBuddy->first_name . ' ' . $peerBuddy->last_name;
                $email = $peerBuddy->email ?? 'N/A';
            } else {
                $leaderName = $report->monitor_name ?? ('Líder #' . $report->friendship_id);
                $email = 'N/A';
            }

            $leaders[] = [
                'id' => $report->id,
                'name' => $leaderName,
                'email' => $email,
                'initials' => $this->getInitials($leaderName),
                'participation' => $report->leader_participation,
                'participation_label' => $this->getParticipationLabel($report->leader_participation),
                'satisfaction' => $report->leader_satisfaction,
                'satisfaction_label' => $this->getSatisfactionLabel($report->leader_satisfaction),
                'evaluation' => $report->general_evaluation,
                'evaluation_label' => $this->getEvaluationLabel($report->general_evaluation),
                'needs_attention' => $report->requires_attention === 'si',
                'meeting_frequency' => $report->meeting_frequency,
                'monitoring_period' => $report->monitoring_period
            ];
        }

        return $leaders;
    }

    private function prepareChartsData($reports)
    {
        // Obtener últimos reportes por friendship
        $latestReports = $reports->groupBy('friendship_id')->map(function($group) {
            return $group->sortByDesc('created_at')->first();
        });

        // Datos para gráfico de participación (basado en leader_participation)
        $participationCounts = [
            'muy-activo' => 0,
            'activo' => 0,
            'moderado' => 0,
            'pasivo' => 0,
            'muy-pasivo' => 0
        ];

        foreach ($latestReports as $report) {
            $participation = $report->leader_participation;
            if (isset($participationCounts[$participation])) {
                $participationCounts[$participation]++;
            }
        }

        // Datos para gráfico de satisfacción (basado en general_evaluation)
        $satisfactionCounts = [
            'excelente' => 0,
            'buena' => 0,
            'regular' => 0,
            'deficiente' => 0,
            'critica' => 0
        ];

        foreach ($latestReports as $report) {
            $evaluation = $report->general_evaluation;
            if (isset($satisfactionCounts[$evaluation])) {
                $satisfactionCounts[$evaluation]++;
            }
        }

        return [
            'participation' => [
                'labels' => ['Muy Activo', 'Activo', 'Moderado', 'Pasivo', 'Muy Pasivo'],
                'data' => array_values($participationCounts)
            ],
            'satisfaction' => [
                'labels' => ['Excelente', 'Buena', 'Regular', 'Deficiente', 'Crítica'],
                'data' => array_values($satisfactionCounts)
            ]
        ];
    }

    private function getInitials($name)
    {
        $words = explode(' ', trim($name));
        $initials = '';
        foreach (array_slice($words, 0, 2) as $word) {
            if (!empty($word)) {
                $initials .= strtoupper(substr($word, 0, 1));
            }
        }
        return $initials ?: 'NA';
    }

    private function getParticipationLabel($participation)
    {
        $labels = [
            'muy-activo' => 'Muy Activo',
            'activo' => 'Activo',
            'moderado' => 'Moderado',
            'pasivo' => 'Pasivo',
            'muy-pasivo' => 'Muy Pasivo'
        ];
        return $labels[$participation] ?? 'Moderado';
    }

    private function getSatisfactionLabel($satisfaction)
    {
        $labels = [
            'muy-satisfecho' => 'Muy Satisfecho',
            'satisfecho' => 'Satisfecho',
            'neutral' => 'Neutral',
            'insatisfecho' => 'Insatisfecho',
            'muy-insatisfecho' => 'Muy Insatisfecho'
        ];
        return $labels[$satisfaction] ?? 'Neutral';
    }

    private function getEvaluationLabel($evaluation)
    {
        $labels = [
            'excelente' => 'Excelente',
            'buena' => 'Buena',
            'regular' => 'Regular',
            'deficiente' => 'Deficiente',
            'critica' => 'Crítica'
        ];
        return $labels[$evaluation] ?? 'Regular';
    }

    public function exportPDF()
    {
        // Implementar exportación a PDF
        return response()->json(['message' => 'Exportación PDF en desarrollo']);
    }

    public function exportExcel()
    {
        // Implementar exportación a Excel
        return response()->json(['message' => 'Exportación Excel en desarrollo']);
    }

    public function exportWord()
    {
        // Implementar exportación a Word
        return response()->json(['message' => 'Exportación Word en desarrollo']);
    }
}