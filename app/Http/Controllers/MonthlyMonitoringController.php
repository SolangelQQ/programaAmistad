<?php

namespace App\Http\Controllers;

use App\Models\MonthlyMonitoringReport;
use App\Models\Friendship;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\FollowUp;
use App\Models\FriendshipFollowUp;
class MonthlyMonitoringController extends Controller
{
    /**
     * Mostrar la vista del formulario de monitoreo
     */
    public function index()
    {
        return view('monitoring.monthly');
    }

    /**
     * Obtener lista de amistades activas para el formulario
     */
    public function getFriendships(): JsonResponse
    {
        try {
            $friendships = Friendship::with(['buddy', 'peer_buddy'])
                ->where('status', 'active')
                ->get()
                ->map(function ($friendship) {
                    return [
                        'id' => $friendship->id,
                        'buddy_id' => $friendship->buddy_id,
                        'peer_buddy_id' => $friendship->peer_buddy_id,
                        'status' => $friendship->status,
                        'buddy' => [
                            'first_name' => $friendship->buddy->first_name ?? '',
                            'last_name' => $friendship->buddy->last_name ?? '',
                        ],
                        'peer_buddy' => [
                            'first_name' => $friendship->peer_buddy->first_name ?? '',
                            'last_name' => $friendship->peer_buddy->last_name ?? '',
                        ],
                    ];
                });

            return response()->json([
                'success' => true,
                'friendships' => $friendships
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener amistades: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar las amistades'
            ], 500);
        }
    }

    /**
     * Guardar un nuevo reporte de monitoreo o actualizar uno existente
     */
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        
        try {
            // Log de datos recibidos para debug
            Log::info('Datos recibidos para monitoreo:', $request->all());

            $validatedData = $request->validate([
                'monitor_name' => 'required|string|max:255',
                'monitoring_period' => 'required|string|in:enero,febrero,marzo,abril,mayo,junio,julio,agosto,septiembre,octubre,noviembre,diciembre',
                'friendship_id' => 'required|exists:friendships,id',
                'general_evaluation' => 'required|in:excelente,buena,regular,deficiente,critica',
                'meeting_frequency' => 'required|in:semanal,quincenal,mensual,irregular',
                'progress_areas' => 'nullable|array',
                'progress_areas.*' => 'string|in:comunicacion,confianza,independencia,habilidades_sociales,autoestima,participacion,integracion,academico',
                'challenges' => 'nullable|array',
                'challenges.*' => 'string|in:tiempo_limitado,diferencias_intereses,comunicacion_dificil,falta_motivacion,barreras_transporte,diferencias_edad,resistencia_cambio,apoyo_familiar',
                'tutor_participation' => 'required|in:muy-activo,activo,moderado,pasivo,muy-pasivo',
                'leader_participation' => 'required|in:muy-activo,activo,moderado,pasivo,muy-pasivo',
                'tutor_satisfaction' => 'required|in:muy-satisfecho,satisfecho,neutral,insatisfecho,muy-insatisfecho',
                'leader_satisfaction' => 'required|in:muy-satisfecho,satisfecho,neutral,insatisfecho,muy-insatisfecho',
                'suggested_actions' => 'nullable|array',
                'suggested_actions.*' => 'string|in:aumentar_frecuencia,diversificar_actividades,capacitacion_adicional,apoyo_recursos,mediacion_conflictos,involucrar_familia,seguimiento_especializado,mantener_actual',
                'requires_attention' => 'required|in:si,no',
                'specific_observations' => 'nullable|string|max:1000',
            ]);

            // Convertir arrays a JSON si existen
            $validatedData['progress_areas'] = $validatedData['progress_areas'] ?? [];
            $validatedData['challenges'] = $validatedData['challenges'] ?? [];
            $validatedData['suggested_actions'] = $validatedData['suggested_actions'] ?? [];

            // Verificar que la amistad existe
            $friendship = Friendship::find($validatedData['friendship_id']);
            if (!$friendship) {
                throw new \Exception('La amistad seleccionada no existe');
            }

            // Buscar si ya existe un reporte para esta amistad en este período
            $existingReport = MonthlyMonitoringReport::where('friendship_id', $validatedData['friendship_id'])
                ->where('monitoring_period', $validatedData['monitoring_period'])
                ->first();

            $isUpdate = false;
            $message = '';

            if ($existingReport) {
                // Si existe, lo actualizamos
                $existingReport->update($validatedData);
                $report = $existingReport;
                $isUpdate = true;
                $message = 'Reporte de monitoreo actualizado exitosamente (se reemplazó el reporte anterior del mismo período)';
                
                Log::info("Reporte actualizado - ID: {$report->id}, Amistad: {$validatedData['friendship_id']}, Período: {$validatedData['monitoring_period']}");
            } else {
                // Si no existe, creamos uno nuevo
                $report = MonthlyMonitoringReport::create($validatedData);
                $message = 'Reporte de monitoreo guardado exitosamente';
                
                Log::info("Reporte creado - ID: {$report->id}, Amistad: {$validatedData['friendship_id']}, Período: {$validatedData['monitoring_period']}");
            }

            DB::commit();

            // Obtener resumen del reporte
            $reportSummary = [
                'id' => $report->id,
                'monitor_name' => $report->monitor_name,
                'monitoring_period' => $report->monitoring_period,
                'friendship_id' => $report->friendship_id,
                'general_evaluation' => $report->general_evaluation,
                'requires_attention' => $report->requires_attention,
                'created_at' => $report->created_at,
                'updated_at' => $report->updated_at,
            ];

            return response()->json([
                'success' => true,
                'message' => $message,
                'report' => $reportSummary,
                'action' => $isUpdate ? 'updated' : 'created'
            ], 201);

        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Error de validación en monitoreo:', [
                'errors' => $e->errors(),
                'data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al guardar/actualizar reporte de monitoreo: ' . $e->getMessage(), [
                'friendship_id' => $request->get('friendship_id'),
                'monitoring_period' => $request->get('monitoring_period'),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor al procesar el reporte: ' . $e->getMessage(),
                'debug' => config('app.debug') ? [
                    'error' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => basename($e->getFile())
                ] : null
            ], 500);
        }
    }

    /**
     * Obtener reportes de monitoreo con filtros
     */
    public function getReports(Request $request): JsonResponse
    {
        try {
            $query = MonthlyMonitoringReport::with(['friendship.buddy', 'friendship.peer_buddy']);

            // Aplicar filtros solo si los scopes existen
            if ($request->has('period') && $request->period) {
                $query->where('monitoring_period', $request->period);
            }

            if ($request->has('evaluation') && $request->evaluation) {
                $query->where('general_evaluation', $request->evaluation);
            }

            if ($request->has('monitor') && $request->monitor) {
                $query->where('monitor_name', 'LIKE', '%' . $request->monitor . '%');
            }

            if ($request->has('requires_attention') && $request->requires_attention === 'true') {
                $query->where('requires_attention', 'si');
            }

            // Ordenar por fecha de actualización descendente
            $reports = $query->orderBy('updated_at', 'desc')
                ->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'reports' => $reports->items(),
                'pagination' => [
                    'current_page' => $reports->currentPage(),
                    'last_page' => $reports->lastPage(),
                    'per_page' => $reports->perPage(),
                    'total' => $reports->total(),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener reportes: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar los reportes'
            ], 500);
        }
    }

    /**
     * Obtener un reporte específico
     */
    public function show($id): JsonResponse
    {
        try {
            $report = MonthlyMonitoringReport::with(['friendship.buddy', 'friendship.peer_buddy'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'report' => $report,
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener reporte: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Reporte no encontrado'
            ], 404);
        }
    }

    /**
     * Actualizar un reporte existente
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $report = MonthlyMonitoringReport::findOrFail($id);

            $validatedData = $request->validate([
                'monitor_name' => 'sometimes|required|string|max:255',
                'monitoring_period' => 'sometimes|required|string|in:enero,febrero,marzo,abril,mayo,junio,julio,agosto,septiembre,octubre,noviembre,diciembre',
                'friendship_id' => 'sometimes|required|exists:friendships,id',
                'general_evaluation' => 'sometimes|required|in:excelente,buena,regular,deficiente,critica',
                'meeting_frequency' => 'sometimes|required|in:semanal,quincenal,mensual,irregular',
                'progress_areas' => 'nullable|array',
                'challenges' => 'nullable|array',
                'tutor_participation' => 'sometimes|required|in:muy-activo,activo,moderado,pasivo,muy-pasivo',
                'leader_participation' => 'sometimes|required|in:muy-activo,activo,moderado,pasivo,muy-pasivo',
                'tutor_satisfaction' => 'sometimes|required|in:muy-satisfecho,satisfecho,neutral,insatisfecho,muy-insatisfecho',
                'leader_satisfaction' => 'sometimes|required|in:muy-satisfecho,satisfecho,neutral,insatisfecho,muy-insatisfecho',
                'suggested_actions' => 'nullable|array',
                'requires_attention' => 'sometimes|required|in:si,no',
                'specific_observations' => 'nullable|string|max:1000',
            ]);

            $report->update($validatedData);

            $reportSummary = [
                'id' => $report->id,
                'monitor_name' => $report->monitor_name,
                'monitoring_period' => $report->monitoring_period,
                'friendship_id' => $report->friendship_id,
                'general_evaluation' => $report->general_evaluation,
                'requires_attention' => $report->requires_attention,
                'updated_at' => $report->updated_at,
            ];

            return response()->json([
                'success' => true,
                'message' => 'Reporte actualizado exitosamente',
                'report' => $reportSummary
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error al actualizar reporte: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el reporte'
            ], 500);
        }
    }

    /**
     * Eliminar un reporte
     */
    public function destroy($id): JsonResponse
    {
        try {
            $report = MonthlyMonitoringReport::findOrFail($id);
            $report->delete();

            return response()->json([
                'success' => true,
                'message' => 'Reporte eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al eliminar reporte: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el reporte'
            ], 500);
        }
    }

    /**
     * Obtener estadísticas generales de monitoreo
     */
    public function getStatistics(): JsonResponse
    {
        try {
            $stats = [
                'total_reports' => MonthlyMonitoringReport::count(),
                'reports_requiring_attention' => MonthlyMonitoringReport::where('requires_attention', 'si')->count(),
                'by_evaluation' => MonthlyMonitoringReport::select('general_evaluation', DB::raw('count(*) as count'))
                    ->groupBy('general_evaluation')
                    ->pluck('count', 'general_evaluation'),
                'by_period' => MonthlyMonitoringReport::select('monitoring_period', DB::raw('count(*) as count'))
                    ->groupBy('monitoring_period')
                    ->pluck('count', 'monitoring_period'),
                'recent_reports' => MonthlyMonitoringReport::with(['friendship.buddy', 'friendship.peer_buddy'])
                    ->orderBy('updated_at', 'desc')
                    ->limit(5)
                    ->get()
                    ->map(function ($report) {
                        return [
                            'id' => $report->id,
                            'monitor_name' => $report->monitor_name,
                            'monitoring_period' => $report->monitoring_period,
                            'general_evaluation' => $report->general_evaluation,
                            'requires_attention' => $report->requires_attention,
                            'updated_at' => $report->updated_at,
                        ];
                    }),
            ];

            return response()->json([
                'success' => true,
                'statistics' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener estadísticas: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar las estadísticas'
            ], 500);
        }
    }

    /**
     * Verificar si existe un reporte para una amistad en un período específico
     */
    public function checkExistingReport(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'friendship_id' => 'required|exists:friendships,id',
                'monitoring_period' => 'required|string|in:enero,febrero,marzo,abril,mayo,junio,julio,agosto,septiembre,octubre,noviembre,diciembre',
            ]);

            $existingReport = MonthlyMonitoringReport::where('friendship_id', $request->friendship_id)
                ->where('monitoring_period', $request->monitoring_period)
                ->first();

            $reportSummary = null;
            if ($existingReport) {
                $reportSummary = [
                    'id' => $existingReport->id,
                    'monitor_name' => $existingReport->monitor_name,
                    'monitoring_period' => $existingReport->monitoring_period,
                    'general_evaluation' => $existingReport->general_evaluation,
                    'updated_at' => $existingReport->updated_at,
                ];
            }

            return response()->json([
                'success' => true,
                'exists' => !is_null($existingReport),
                'report' => $reportSummary,
                'message' => $existingReport 
                    ? 'Ya existe un reporte para esta amistad en este período. Si continúas, se actualizará el reporte existente.' 
                    : 'No existe reporte previo. Se creará un nuevo reporte.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al verificar reporte existente: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al verificar reportes existentes'
            ], 500);
        }
    }

public function getLiderazgoData()
{
    $reports = MonthlyMonitoringReport::with('friendship.peer_buddy')->get();
    
    return response()->json([
        'success' => true,
        'stats' => [
            'totalLeaders' => $reports->count(),
            'veryActive' => $reports->where('leader_participation', 'muy-activo')->count(),
            'needsAttention' => $reports->where('requires_attention', 'si')->count(),
            'avgSatisfaction' => '75%'
        ],
        'leaders' => $reports->map(function($report) {
            return [
                'id' => $report->id,
                'name' => $report->monitor_name,
                'email' => 'email@example.com',
                'initials' => strtoupper(substr($report->monitor_name, 0, 2)),
                'participation' => $report->leader_participation ?? 'moderado',
                'participation_label' => ucfirst($report->leader_participation ?? 'moderado'),
                'satisfaction' => $report->leader_satisfaction ?? 'neutral',
                'satisfaction_label' => ucfirst($report->leader_satisfaction ?? 'neutral'),
                'evaluation' => $report->general_evaluation ?? 'regular',
                'evaluation_label' => ucfirst($report->general_evaluation ?? 'regular'),
                'needs_attention' => $report->requires_attention === 'si'
            ];
        }),
        'charts' => [
            'participation' => [
                'labels' => ['Muy Activo', 'Activo', 'Moderado', 'Pasivo', 'Muy Pasivo'],
                'data' => [2, 3, 5, 2, 1]
            ],
            'satisfaction' => [
                'labels' => ['Muy Satisfecho', 'Satisfecho', 'Neutral', 'Insatisfecho', 'Muy Insatisfecho'],
                'data' => [4, 6, 3, 1, 1]
            ]
        ]
    ]);
}

    /**
     * Obtener total de amistades
     */
    private function getTotalFriendships()
    {
        // Si tienes tabla friendships
        if (DB::getSchemaBuilder()->hasTable('friendships')) {
            return DB::table('friendships')->count();
        }
        
        // Si usas la tabla buddies para contar amistades
        return Buddy::count();
    }

    private function getActiveFriendships()
    {
        // Si tienes tabla friendships con campo status
        if (DB::getSchemaBuilder()->hasTable('friendships') && 
            DB::getSchemaBuilder()->hasColumn('friendships', 'status')) {
            return DB::table('friendships')->where('status', 'active')->count();
        }
        
        // Si usas buddies con campo is_active
        if (DB::getSchemaBuilder()->hasColumn('buddies', 'is_active')) {
            return Buddy::where('is_active', true)->count();
        }
        
        // Fallback: todos los buddies
        return Buddy::count();
    }

/**
 * Obtener breakdown de evaluaciones de reportes mensuales
 */
private function getEvaluationBreakdown()
    {
        $evaluations = MonthlyMonitoringReport::select('general_evaluation')
            ->whereNotNull('general_evaluation')
            ->groupBy('general_evaluation')
            ->selectRaw('general_evaluation, COUNT(*) as count')
            ->pluck('count', 'general_evaluation')
            ->toArray();

        // Asegurar que todas las categorías estén presentes
        return [
            'excelente' => $evaluations['excelente'] ?? 0,
            'buena' => $evaluations['buena'] ?? 0,
            'regular' => $evaluations['regular'] ?? 0,
            'deficiente' => $evaluations['deficiente'] ?? 0,
            'critica' => $evaluations['critica'] ?? 0
        ];
    }


/**
 * Obtener breakdown de progreso de seguimientos
 */
private function getProgressBreakdown()
    {
        $followUps = FriendshipFollowUp::select('buddy_progress', 'peer_buddy_progress', 'relationship_quality')
            ->get();

        $progressBreakdown = [
            'excelente' => 0,
            'bueno' => 0,
            'regular' => 0,
            'bajo' => 0,
            'muy_bajo' => 0
        ];

        foreach ($followUps as $followUp) {
            $average = ($followUp->buddy_progress + $followUp->peer_buddy_progress + $followUp->relationship_quality) / 3;
            
            if ($average >= 4.5) {
                $progressBreakdown['excelente']++;
            } elseif ($average >= 3.5) {
                $progressBreakdown['bueno']++;
            } elseif ($average >= 2.5) {
                $progressBreakdown['regular']++;
            } elseif ($average >= 1.5) {
                $progressBreakdown['bajo']++;
            } else {
                $progressBreakdown['muy_bajo']++;
            }
        }

        return $progressBreakdown;
    }

/**
 * Obtener datos de dashboard para liderazgo
 */
public function getDashboardData(): JsonResponse
{
    try {
        // Obtener reportes mensuales recientes
        $recentReports = MonthlyMonitoringReport::with([
            'friendship.buddy', 
            'friendship.peer_buddy'
        ])->orderBy('updated_at', 'desc')
          ->limit(10)
          ->get();

        // Obtener seguimientos recientes
        $recentFollowUps = FollowUp::with([
            'friendship.buddy', 
            'friendship.peer_buddy', 
            'user'
        ])->orderBy('created_at', 'desc')
          ->limit(10)
          ->get();

        // Obtener amistades que requieren atención
        $friendshipsNeedingAttention = MonthlyMonitoringReport::with([
            'friendship.buddy', 
            'friendship.peer_buddy'
        ])->where('requires_attention', 'si')
          ->orderBy('updated_at', 'desc')
          ->limit(5)
          ->get();

        // Estadísticas rápidas
        $quickStats = [
            'total_amistades' => Friendship::count(),
            'amistades_activas' => Friendship::where('status', 'active')->count(),
            'reportes_mes_actual' => MonthlyMonitoringReport::whereMonth('created_at', now()->month)->count(),
            'seguimientos_semana_actual' => FollowUp::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'requieren_atencion' => MonthlyMonitoringReport::where('requires_attention', 'si')->count()
        ];

        return response()->json([
            'success' => true,
            'dashboard' => [
                'recent_reports' => $recentReports,
                'recent_follow_ups' => $recentFollowUps,
                'friendships_needing_attention' => $friendshipsNeedingAttention,
                'quick_stats' => $quickStats
            ]
        ]);

    } catch (\Exception $e) {
        Log::error('Error al obtener datos del dashboard: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Error al cargar el dashboard'
        ], 500);
    }
}

/**
 * Obtener resumen de progreso por amistad
 */
public function getProgressSummary(): JsonResponse
{
    try {
        $friendships = Friendship::with(['buddy', 'peer_buddy'])
            ->where('status', 'active')
            ->get();

        $summaryData = [];

        foreach ($friendships as $friendship) {
            // Obtener último reporte mensual
            $latestReport = MonthlyMonitoringReport::where('friendship_id', $friendship->id)
                ->orderBy('updated_at', 'desc')
                ->first();

            // Obtener últimos seguimientos
            $recentFollowUps = FollowUp::where('friendship_id', $friendship->id)
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();

            $averageProgress = 0;
            if ($recentFollowUps->isNotEmpty()) {
                $totalProgress = $recentFollowUps->sum(function ($followUp) {
                    return ($followUp->buddy_progress + $followUp->peer_buddy_progress + $followUp->relationship_quality) / 3;
                });
                $averageProgress = round($totalProgress / $recentFollowUps->count(), 1);
            }

            $summaryData[] = [
                'friendship_id' => $friendship->id,
                'buddy_name' => $friendship->buddy->first_name . ' ' . $friendship->buddy->last_name,
                'peer_buddy_name' => $friendship->peer_buddy->first_name . ' ' . $friendship->peer_buddy->last_name,
                'latest_report' => $latestReport ? [
                    'id' => $latestReport->id,
                    'monitoring_period' => $latestReport->monitoring_period,
                    'general_evaluation' => $latestReport->general_evaluation,
                    'requires_attention' => $latestReport->requires_attention,
                    'updated_at' => $latestReport->updated_at
                ] : null,
                'average_progress' => $averageProgress,
                'progress_status' => $this->getProgressStatusText($averageProgress),
                'follow_ups_count' => FollowUp::where('friendship_id', $friendship->id)->count(),
                'reports_count' => MonthlyMonitoringReport::where('friendship_id', $friendship->id)->count()
            ];
        }

        return response()->json([
            'success' => true,
            'progress_summary' => $summaryData
        ]);

    } catch (\Exception $e) {
        Log::error('Error al obtener resumen de progreso: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Error al cargar el resumen de progreso'
        ], 500);
    }
}

/**
 * Obtener texto del estado de progreso
 */
private function getProgressStatusText($average)
{
    if ($average >= 4.5) return 'Excelente';
    if ($average >= 3.5) return 'Bueno';
    if ($average >= 2.5) return 'Regular';
    if ($average >= 1.5) return 'Bajo';
    return 'Muy Bajo';
}



    
}