<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Buddy;
use App\Models\Friendship;
use App\Models\FriendshipFollowUp;
use App\Models\FriendshipAttendance;

use App\Models\Role; 
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\MonthlyMonitoringReport; 

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;


class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Datos iniciales para la vista
        $initialStats = $this->getInitialStats();
        return view('reports.index', compact('initialStats'));
    }

    private function getInitialStats()
    {
        try {
            return [
                'total_members' => Buddy::count(),
                'total_activities' => Activity::count(),
                'new_friendships' => Friendship::where('created_at', '>=', now()->subMonth())->count(),
                'active_leaders' => Buddy::where('type', 'peer_buddy')
                    ->whereHas('peerBuddyFriendships', function($query) {
                        $query->where('status', 'Emparejado');
                    })->count()
            ];
        } catch (\Exception $e) {
            Log::error('Error getting initial stats: ' . $e->getMessage());
            return [
                'total_members' => 0,
                'total_activities' => 0,
                'new_friendships' => 0,
                'active_leaders' => 0
            ];
        }
    }

    /**
     * Obtener datos del reporte general
     */
    public function general(Request $request)
    {
        try {
            // Log inicial para debug
            Log::info('General report request started', [
                'params' => $request->all(),
                'user_id' => auth()->id()
            ]);

            $dateFrom = $request->input('from', now()->subMonth()->toDateString());
            $dateTo = $request->input('to', now()->toDateString());

            Log::info('Dates processed', ['from' => $dateFrom, 'to' => $dateTo]);

            // Validar fechas
            if (!$this->validateDates($dateFrom, $dateTo)) {
                Log::warning('Invalid dates provided', ['from' => $dateFrom, 'to' => $dateTo]);
                return response()->json([
                    'success' => false,
                    'error' => 'Fechas inválidas',
                    'message' => 'Las fechas proporcionadas no son válidas'
                ], 400);
            }

            // Estadísticas generales con manejo de errores
            Log::info('Getting general statistics...');
            $statistics = $this->getGeneralStatistics($dateFrom, $dateTo);
            Log::info('Statistics obtained', ['stats' => $statistics]);
            
            // Gráficos con manejo de errores
            Log::info('Getting charts data...');
            $charts = $this->getGeneralCharts($dateFrom, $dateTo);
            Log::info('Charts data obtained');

            // Datos de resumen para la tabla
            Log::info('Getting summary data...');
            $summaryData = $this->getSummaryData($dateFrom, $dateTo);
            Log::info('Summary data obtained');

            $response = [
                'success' => true,
                'statistics' => $statistics,
                'charts' => $charts,
                'summary_data' => $summaryData,
                'period' => [
                    'from' => $dateFrom,
                    'to' => $dateTo
                ]
            ];

            Log::info('General report completed successfully');
            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('Error in general report: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Error interno del servidor',
                'message' => 'No se pudieron cargar los datos del reporte general',
                'debug' => config('app.debug') ? [
                    'message' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => basename($e->getFile())
                ] : null
            ], 500);
        }
    }

    /**
     * Validar fechas
     */
    private function validateDates($dateFrom, $dateTo)
    {
        try {
            $from = Carbon::parse($dateFrom);
            $to = Carbon::parse($dateTo);
            return $from->lte($to);
        } catch (\Exception $e) {
            Log::error('Date validation error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener estadísticas generales
     */
    private function getGeneralStatistics($dateFrom, $dateTo)
    {
        try {
            Log::info('Calculating statistics...');
            
            // Verificar que las tablas existan y sean accesibles
            $totalBuddies = 0;
            $totalPeerBuddies = 0;
            
            try {
                $totalBuddies = Buddy::where('type', 'buddy')->count();
                Log::info('Total buddies: ' . $totalBuddies);
            } catch (\Exception $e) {
                Log::error('Error counting buddies: ' . $e->getMessage());
            }

            try {
                $totalPeerBuddies = Buddy::where('type', 'peer_buddy')->count();
                Log::info('Total peer buddies: ' . $totalPeerBuddies);
            } catch (\Exception $e) {
                Log::error('Error counting peer buddies: ' . $e->getMessage());
            }

            $totalFriendships = 0;
            $activeFriendships = 0;
            
            try {
                $totalFriendships = Friendship::count();
                $activeFriendships = Friendship::where('status', 'Emparejado')->count();
                Log::info('Friendships - Total: ' . $totalFriendships . ', Active: ' . $activeFriendships);
            } catch (\Exception $e) {
                Log::error('Error counting friendships: ' . $e->getMessage());
            }

            $totalActivities = 0;
            $completedActivities = 0;
            
            try {
                $totalActivities = Activity::whereBetween('date', [$dateFrom, $dateTo])->count();
                $completedActivities = Activity::whereBetween('date', [$dateFrom, $dateTo])
                                             ->where('status', 'completed')
                                             ->count();
                Log::info('Activities - Total: ' . $totalActivities . ', Completed: ' . $completedActivities);
            } catch (\Exception $e) {
                Log::error('Error counting activities: ' . $e->getMessage());
            }

            $stats = [
                'totalBuddies' => $totalBuddies,
                'totalPeerBuddies' => $totalPeerBuddies,
                'totalParticipants' => $totalBuddies + $totalPeerBuddies,
                'totalFriendships' => $totalFriendships,
                'activeFriendships' => $activeFriendships,
                'totalActivities' => $totalActivities,
                'completedActivities' => $completedActivities,
                'activityCompletionRate' => $totalActivities > 0 ? round(($completedActivities / $totalActivities) * 100, 1) : 0
            ];

            Log::info('Statistics calculated successfully', $stats);
            return $stats;

        } catch (\Exception $e) {
            Log::error('Error getting general statistics: ' . $e->getMessage());
            return [
                'totalBuddies' => 0,
                'totalPeerBuddies' => 0,
                'totalParticipants' => 0,
                'totalFriendships' => 0,
                'activeFriendships' => 0,
                'totalActivities' => 0,
                'completedActivities' => 0,
                'activityCompletionRate' => 0
            ];
        }
    }

    /**
     * Obtener datos para gráficos
     */
    private function getGeneralCharts($dateFrom, $dateTo)
    {
        try {
            Log::info('Getting charts data...');

            // Inicializar colecciones vacías por defecto
            $friendshipsByStatus = collect();
            $activitiesByType = collect();
            $disabilityDistribution = collect();
            $monthlyActivities = collect();

            // Amistades por estado
            try {
                $friendshipsByStatus = Friendship::select('status', DB::raw('count(*) as total'))
                                               ->groupBy('status')
                                               ->get()
                                               ->map(function($item) {
                                                   return [
                                                       'status' => $item->status ?? 'Sin estado',
                                                       'total' => $item->total
                                                   ];
                                               });
                Log::info('Friendships by status obtained: ' . $friendshipsByStatus->count() . ' records');
            } catch (\Exception $e) {
                Log::error('Error getting friendships by status: ' . $e->getMessage());
            }

            // Actividades por tipo en el período
            try {
                $activitiesByType = Activity::select('type', DB::raw('count(*) as total'))
                                          ->whereBetween('date', [$dateFrom, $dateTo])
                                          ->groupBy('type')
                                          ->get()
                                          ->map(function($item) {
                                              return [
                                                  'type' => $this->getActivityTypeLabel($item->type ?? 'unknown'),
                                                  'total' => $item->total
                                              ];
                                          });
                Log::info('Activities by type obtained: ' . $activitiesByType->count() . ' records');
            } catch (\Exception $e) {
                Log::error('Error getting activities by type: ' . $e->getMessage());
            }

            // Distribución de discapacidades
            try {
                $disabilityDistribution = Buddy::where('type', 'buddy')
                                              ->whereNotNull('disability')
                                              ->where('disability', '!=', '')
                                              ->select('disability', DB::raw('count(*) as total'))
                                              ->groupBy('disability')
                                              ->get()
                                              ->map(function($item) {
                                                  return [
                                                      'disability' => $item->disability ?? 'No especificada',
                                                      'total' => $item->total
                                                  ];
                                              });
                Log::info('Disability distribution obtained: ' . $disabilityDistribution->count() . ' records');
            } catch (\Exception $e) {
                Log::error('Error getting disability distribution: ' . $e->getMessage());
            }

            // Actividades por mes (últimos 6 meses)
            try {
                $monthlyActivities = Activity::select(
                                            DB::raw('YEAR(date) as year'),
                                            DB::raw('MONTH(date) as month'),
                                            DB::raw('count(*) as total')
                                        )
                                        ->where('date', '>=', now()->subMonths(6))
                                        ->groupBy('year', 'month')
                                        ->orderBy('year')
                                        ->orderBy('month')
                                        ->get()
                                        ->map(function ($item) {
                                            return [
                                                'period' => Carbon::create($item->year, $item->month)->format('M Y'),
                                                'total' => $item->total,
                                                'year' => $item->year,
                                                'month' => $item->month
                                            ];
                                        });
                Log::info('Monthly activities obtained: ' . $monthlyActivities->count() . ' records');
            } catch (\Exception $e) {
                Log::error('Error getting monthly activities: ' . $e->getMessage());
            }

            $charts = [
                'friendshipsByStatus' => $friendshipsByStatus,
                'activitiesByType' => $activitiesByType,
                'disabilityDistribution' => $disabilityDistribution,
                'monthlyActivities' => $monthlyActivities
            ];

            Log::info('Charts data processed successfully');
            return $charts;

        } catch (\Exception $e) {
            Log::error('Error getting general charts: ' . $e->getMessage());
            return [
                'friendshipsByStatus' => collect(),
                'activitiesByType' => collect(),
                'disabilityDistribution' => collect(),
                'monthlyActivities' => collect()
            ];
        }
    }

    /**
     * Obtener datos de resumen para la tabla
     */
    private function getSummaryData($dateFrom, $dateTo)
    {
        try {
            Log::info('Getting summary data...');
            
            $previousMonth = now()->subMonth();
            $currentMonth = now();

            $summaryData = [];

            // Miembros totales
            try {
                $totalMembers = Buddy::count();
                $currentMonthMembers = Buddy::whereMonth('created_at', $currentMonth->month)
                                          ->whereYear('created_at', $currentMonth->year)
                                          ->count();
                $previousMonthMembers = Buddy::whereMonth('created_at', $previousMonth->month)
                                           ->whereYear('created_at', $previousMonth->year)
                                           ->count();

                $summaryData[] = [
                    'category' => 'Miembros Totales',
                    'total' => $totalMembers,
                    'current_month' => $currentMonthMembers,
                    'change' => $this->calculatePercentageChange($previousMonthMembers, $currentMonthMembers),
                    'status' => 'bueno'
                ];
            } catch (\Exception $e) {
                Log::error('Error getting members summary: ' . $e->getMessage());
            }

            // Actividades
            try {
                $totalActivities = Activity::count();
                $currentMonthActivities = Activity::whereMonth('date', $currentMonth->month)
                                                ->whereYear('date', $currentMonth->year)
                                                ->count();
                $previousMonthActivities = Activity::whereMonth('date', $previousMonth->month)
                                                 ->whereYear('date', $previousMonth->year)
                                                 ->count();

                $summaryData[] = [
                    'category' => 'Actividades',
                    'total' => $totalActivities,
                    'current_month' => $currentMonthActivities,
                    'change' => $this->calculatePercentageChange($previousMonthActivities, $currentMonthActivities),
                    'status' => 'excelente'
                ];
            } catch (\Exception $e) {
                Log::error('Error getting activities summary: ' . $e->getMessage());
            }

            // Amistades activas
            try {
                $activeFriendships = Friendship::where('status', 'Emparejado')->count();
                $currentMonthFriendships = Friendship::where('status', 'Emparejado')
                                                   ->whereMonth('created_at', $currentMonth->month)
                                                   ->whereYear('created_at', $currentMonth->year)
                                                   ->count();
                $previousMonthFriendships = Friendship::where('status', 'Emparejado')
                                                     ->whereMonth('created_at', $previousMonth->month)
                                                     ->whereYear('created_at', $previousMonth->year)
                                                     ->count();

                $summaryData[] = [
                    'category' => 'Amistades Activas',
                    'total' => $activeFriendships,
                    'current_month' => $currentMonthFriendships,
                    'change' => $this->calculatePercentageChange($previousMonthFriendships, $currentMonthFriendships),
                    'status' => 'bueno'
                ];
            } catch (\Exception $e) {
                Log::error('Error getting friendships summary: ' . $e->getMessage());
            }

            Log::info('Summary data obtained: ' . count($summaryData) . ' categories');
            return $summaryData;

        } catch (\Exception $e) {
            Log::error('Error getting summary data: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Calcular cambio porcentual
     */
    private function calculatePercentageChange($previous, $current)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return round((($current - $previous) / $previous) * 100, 1);
    }

    /**
     * Obtener etiqueta del tipo de actividad
     */
    private function getActivityTypeLabel($type)
{
    $labels = [
        'recreational' => 'Recreativa',
        'educational' => 'Educativa',
        'cultural' => 'Cultural',
        'sports' => 'Deportiva',
        'social' => 'Social',
        'unknown' => 'No especificado'
    ];

    return $labels[$type] ?? ucfirst($type);
}

    public function actividades(Request $request)
{
    // Forzar que siempre devuelva JSON
    $request->headers->set('Accept', 'application/json');
    
    try {
        Log::info('Activities report request started', [
            'params' => $request->all(),
            'user_id' => auth()->id(),
            'url' => $request->url(),
            'method' => $request->method()
        ]);

        // Validar que sea una petición POST o GET
        if (!in_array($request->method(), ['POST', 'GET'])) {
            return response()->json([
                'success' => false,
                'error' => 'Método no permitido'
            ], 405);
        }

        $dateFrom = $request->input('from', now()->subMonth()->toDateString());
        $dateTo = $request->input('to', now()->toDateString());

        Log::info('Processing dates', ['from' => $dateFrom, 'to' => $dateTo]);

        // Validar fechas básicamente
        try {
            $fromDate = Carbon::parse($dateFrom);
            $toDate = Carbon::parse($dateTo);
            
            if ($fromDate->gt($toDate)) {
                return response()->json([
                    'success' => false,
                    'error' => 'La fecha inicial no puede ser mayor que la fecha final'
                ], 400);
            }
        } catch (\Exception $dateError) {
            Log::error('Date parsing error: ' . $dateError->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Formato de fecha inválido'
            ], 400);
        }

        // Verificar que el modelo Activity existe y la tabla es accesible
        try {
            $testQuery = Activity::query()->limit(1)->count();
            Log::info('Activity table accessible, test count: ' . $testQuery);
        } catch (\Exception $tableError) {
            Log::error('Activity table not accessible: ' . $tableError->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error de base de datos',
                'message' => 'No se puede acceder a la tabla de actividades'
            ], 500);
        }

        // Obtener estadísticas
        $statistics = $this->getActivitiesStatisticsSafe($dateFrom, $dateTo);
        Log::info('Statistics obtained', $statistics);
        
        // Obtener datos para gráficos
        $charts = $this->getActivitiesChartsSafe($dateFrom, $dateTo);
        Log::info('Charts data obtained');
        
        // Obtener lista de actividades
        $activities = $this->getActivitiesListSafe($dateFrom, $dateTo);
        Log::info('Activities list obtained: ' . count($activities) . ' items');

        $response = [
        'success' => true,
        'actividades' => [  // Cambia esta clave para que coincida con lo que espera el frontend
            'statistics' => $statistics,
            'charts' => $charts,
            'activities' => $activities
        ],
        'period' => [
            'from' => $dateFrom,
            'to' => $dateTo
        ],
        'timestamp' => now()->toISOString()
    ];

        Log::info('Activities report completed successfully');
        
        return response()->json($response, 200, [
            'Content-Type' => 'application/json'
        ]);

    } catch (\Exception $e) {
        Log::error('Critical error in activities report', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);

        // Asegurar que siempre devolvemos JSON válido
        return response()->json([
            'success' => false,
            'error' => 'Error interno del servidor',
            'message' => 'No se pudieron cargar los datos del reporte de actividades',
            'debug' => config('app.debug') ? [
                'exception' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => basename($e->getFile())
            ] : null,
            'timestamp' => now()->toISOString()
        ], 500, [
            'Content-Type' => 'application/json'
        ]);
    }
}

private function getActivitiesStatisticsSafe($dateFrom, $dateTo)
{
    try {
        $query = Activity::whereBetween('date', [$dateFrom, $dateTo]);
        
        $totalActivities = (clone $query)->count();
        $completedActivities = (clone $query)->where('status', 'completed')->count();
        $scheduledActivities = (clone $query)->where('status', 'scheduled')->count();
        $cancelledActivities = (clone $query)->where('status', 'cancelled')->count();

        return [
            'totalActivities' => $totalActivities,
            'completedActivities' => $completedActivities,
            'scheduledActivities' => $scheduledActivities,
            'cancelledActivities' => $cancelledActivities,
            'completionRate' => $totalActivities > 0 ? round(($completedActivities / $totalActivities) * 100, 1) : 0
        ];

    } catch (\Exception $e) {
        Log::error('Error in getActivitiesStatisticsSafe: ' . $e->getMessage());
        return [
            'totalActivities' => 0,
            'completedActivities' => 0,
            'scheduledActivities' => 0,
            'cancelledActivities' => 0,
            'completionRate' => 0
        ];
    }
}

private function getActivitiesChartsSafe($dateFrom, $dateTo)
{
    try {
        // Participación por tipo
        $participationByType = [];
        try {
            $participationByType = Activity::whereBetween('date', [$dateFrom, $dateTo])
                                         ->select('type', DB::raw('count(*) as total'))
                                         ->groupBy('type')
                                         ->get()
                                         ->map(function($item) {
                                             return [
                                                 'type' => $this->getActivityTypeLabel($item->type ?? 'unknown'),
                                                 'total' => (int) $item->total
                                             ];
                                         })
                                         ->toArray();
        } catch (\Exception $e) {
            Log::error('Error getting participation by type: ' . $e->getMessage());
        }

        // Comparación de estados
        $statusComparison = [];
        try {
            $statusComparison = Activity::whereBetween('date', [$dateFrom, $dateTo])
                                      ->select('status', DB::raw('count(*) as total'))
                                      ->groupBy('status')
                                      ->get()
                                      ->map(function($item) {
                                          return [
                                              'status' => $this->getActivityStatusLabel($item->status ?? 'unknown'),
                                              'total' => (int) $item->total
                                          ];
                                      })
                                      ->toArray();
        } catch (\Exception $e) {
            Log::error('Error getting status comparison: ' . $e->getMessage());
        }

        // Tendencia mensual
        $monthlyTrend = [];
        try {
            $monthlyTrend = Activity::select(
                                       DB::raw('YEAR(date) as year'),
                                       DB::raw('MONTH(date) as month'),
                                       DB::raw('count(*) as total')
                                   )
                                   ->where('date', '>=', now()->subMonths(6))
                                   ->where('date', '<=', $dateTo)
                                   ->groupBy('year', 'month')
                                   ->orderBy('year')
                                   ->orderBy('month')
                                   ->get()
                                   ->map(function ($item) {
                                       return [
                                           'period' => Carbon::create($item->year, $item->month)->format('M Y'),
                                           'total' => (int) $item->total
                                       ];
                                   })
                                   ->toArray();
        } catch (\Exception $e) {
            Log::error('Error getting monthly trend: ' . $e->getMessage());
        }

        return [
            'participationByType' => $participationByType,
            'statusComparison' => $statusComparison,
            'monthlyTrend' => $monthlyTrend
        ];

    } catch (\Exception $e) {
        Log::error('Error in getActivitiesChartsSafe: ' . $e->getMessage());
        return [
            'participationByType' => [],
            'statusComparison' => [],
            'monthlyTrend' => []
        ];
    }
}

/**
 * Versión segura de obtener lista de actividades
 */
private function getActivitiesListSafe($dateFrom, $dateTo)
{
    try {
        $activities = Activity::whereBetween('date', [$dateFrom, $dateTo])
                            ->orderBy('date', 'desc')
                            ->limit(100) // Limitar para evitar problemas de memoria
                            ->get()
                            ->map(function ($activity) {
                                return [
                                    'id' => $activity->id,
                                    'title' => $activity->title ?? 'Sin título',
                                    'date' => $activity->date,
                                    'type' => $activity->type ?? 'unknown',
                                    'status' => $activity->status ?? 'unknown',
                                    'location' => $activity->location ?? 'No especificado',
                                    'formatted_date' => Carbon::parse($activity->date)->format('d/m/Y'),
                                    'type_label' => $this->getActivityTypeLabel($activity->type ?? 'unknown'),
                                    'status_label' => $this->getActivityStatusLabel($activity->status ?? 'unknown')
                                ];
                            })
                            ->toArray();

        return $activities;

    } catch (\Exception $e) {
        Log::error('Error in getActivitiesListSafe: ' . $e->getMessage());
        return [];
    }
}

private function getActivitiesStatistics($dateFrom, $dateTo)
{
    try {
        Log::info('Getting activities statistics...');

        $totalActivities = Activity::whereBetween('date', [$dateFrom, $dateTo])->count();
        
        $completedActivities = Activity::whereBetween('date', [$dateFrom, $dateTo])
                                     ->where('status', 'completed')
                                     ->count();
        
        $scheduledActivities = Activity::whereBetween('date', [$dateFrom, $dateTo])
                                     ->where('status', 'scheduled')
                                     ->count();
        
        $cancelledActivities = Activity::whereBetween('date', [$dateFrom, $dateTo])
                                     ->where('status', 'cancelled')
                                     ->count();

        $inProgressActivities = Activity::whereBetween('date', [$dateFrom, $dateTo])
                                      ->where('status', 'in_progress')
                                      ->count();

        $statistics = [
            'totalActivities' => $totalActivities,
            'completedActivities' => $completedActivities,
            'scheduledActivities' => $scheduledActivities,
            'cancelledActivities' => $cancelledActivities,
            'inProgressActivities' => $inProgressActivities,
            'completionRate' => $totalActivities > 0 ? round(($completedActivities / $totalActivities) * 100, 1) : 0
        ];

        Log::info('Activities statistics calculated', $statistics);
        return $statistics;

    } catch (\Exception $e) {
        Log::error('Error getting activities statistics: ' . $e->getMessage());
        return [
            'totalActivities' => 0,
            'completedActivities' => 0,
            'scheduledActivities' => 0,
            'cancelledActivities' => 0,
            'inProgressActivities' => 0,
            'completionRate' => 0
        ];
    }
}

/**
 * Obtener datos para gráficos de actividades
 */
private function getActivitiesCharts($dateFrom, $dateTo)
{
    try {
        Log::info('Getting activities charts data...');

        // Participación por tipo de actividad
        $participationByType = Activity::whereBetween('date', [$dateFrom, $dateTo])
                                     ->select('type', DB::raw('count(*) as total'))
                                     ->groupBy('type')
                                     ->get()
                                     ->map(function($item) {
                                         return [
                                             'type' => $this->getActivityTypeLabel($item->type ?? 'unknown'),
                                             'total' => $item->total
                                         ];
                                     });

        // Comparación de estados
        $statusComparison = Activity::whereBetween('date', [$dateFrom, $dateTo])
                                  ->select('status', DB::raw('count(*) as total'))
                                  ->groupBy('status')
                                  ->get()
                                  ->map(function($item) {
                                      return [
                                          'status' => $this->getActivityStatusLabel($item->status ?? 'unknown'),
                                          'total' => $item->total
                                      ];
                                  });

        // Tendencia mensual (últimos 6 meses)
        $monthlyTrend = Activity::select(
                                   DB::raw('YEAR(date) as year'),
                                   DB::raw('MONTH(date) as month'),
                                   DB::raw('count(*) as total')
                               )
                               ->where('date', '>=', now()->subMonths(6))
                               ->where('date', '<=', $dateTo)
                               ->groupBy('year', 'month')
                               ->orderBy('year')
                               ->orderBy('month')
                               ->get()
                               ->map(function ($item) {
                                   return [
                                       'period' => Carbon::create($item->year, $item->month)->format('M Y'),
                                       'total' => $item->total,
                                       'year' => $item->year,
                                       'month' => $item->month
                                   ];
                               });

        $charts = [
            'participationByType' => $participationByType,
            'statusComparison' => $statusComparison,
            'monthlyTrend' => $monthlyTrend
        ];

        Log::info('Activities charts data processed successfully');
        return $charts;

    } catch (\Exception $e) {
        Log::error('Error getting activities charts: ' . $e->getMessage());
        return [
            'participationByType' => collect(),
            'statusComparison' => collect(),
            'monthlyTrend' => collect()
        ];
    }
}

/**
 * Obtener lista de actividades
 */
private function getActivitiesList($dateFrom, $dateTo)
{
    try {
        Log::info('Getting activities list...');

        $activities = Activity::whereBetween('date', [$dateFrom, $dateTo])
                            ->orderBy('date', 'desc')
                            ->get()
                            ->map(function ($activity) {
                                return [
                                    'id' => $activity->id,
                                    'title' => $activity->title ?? 'Sin título',
                                    'date' => $activity->date,
                                    'type' => $activity->type ?? 'unknown',
                                    'status' => $activity->status ?? 'unknown',
                                    'location' => $activity->location ?? 'No especificado',
                                    'description' => $activity->description ?? '',
                                    'formatted_date' => Carbon::parse($activity->date)->format('d/m/Y'),
                                    'type_label' => $this->getActivityTypeLabel($activity->type ?? 'unknown'),
                                    'status_label' => $this->getActivityStatusLabel($activity->status ?? 'unknown'),
                                    'participants_count' => $activity->participants_count ?? 0
                                ];
                            });

        Log::info('Activities list obtained: ' . $activities->count() . ' activities');
        return $activities;

    } catch (\Exception $e) {
        Log::error('Error getting activities list: ' . $e->getMessage());
        return collect();
    }
}

private function getActivityStatusLabel($status)
{
    $labels = [
        'scheduled' => 'Programada',
        'in_progress' => 'En Progreso',
        'completed' => 'Completada',
        'cancelled' => 'Cancelada',
        'postponed' => 'Pospuesta',
        'unknown' => 'Sin estado'
    ];

    return $labels[$status] ?? ucfirst($status);
}

public function amistades(Request $request)
{
    try {
        $dateFrom = $request->input('from', now()->subMonth()->toDateString());
        $dateTo = $request->input('to', now()->toDateString());

        if (!$this->validateDates($dateFrom, $dateTo)) {
            return response()->json([
                'success' => false,
                'error' => 'Fechas inválidas'
            ], 400);
        }

        // Estadísticas
        $statistics = $this->getFriendshipsStatistics($dateFrom, $dateTo);
        
        // Gráficos
        $charts = $this->getFriendshipsCharts($dateFrom, $dateTo);
        
        // Lista de amistades
        $friendships = $this->getFriendshipsList($dateFrom, $dateTo);

        return response()->json([
            'success' => true,
            'statistics' => $statistics,
            'charts' => $charts,
            'friendships' => $friendships,
            'period' => [
                'from' => $dateFrom,
                'to' => $dateTo
            ]
        ]);

    } catch (\Exception $e) {
        Log::error('Error in friendships report: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'error' => 'Error interno del servidor'
        ], 500);
    }
}

private function getFriendshipsStatistics($dateFrom, $dateTo)
{
    try {
        $totalFriendships = Friendship::count();
        $activeFriendships = Friendship::where('status', 'Emparejado')->count();
        $inactiveFriendships = Friendship::where('status', 'Inactivo')->count();
        $completedFriendships = Friendship::where('status', 'Finalizado')->count();
        
        // Calcular duración promedio
        $averageDuration = Friendship::whereNotNull('end_date')
            ->selectRaw('AVG(DATEDIFF(end_date, start_date)) as avg_duration')
            ->first()->avg_duration ?? 0;

        $newFriendships = Friendship::whereBetween('start_date', [$dateFrom, $dateTo])->count();

        return [
            'totalFriendships' => $totalFriendships,
            'activeFriendships' => $activeFriendships,
            'inactiveFriendships' => $inactiveFriendships,
            'completedFriendships' => $completedFriendships,
            'averageDuration' => round($averageDuration),
            'newFriendships' => $newFriendships
        ];

    } catch (\Exception $e) {
        Log::error('Error getting friendships statistics: ' . $e->getMessage());
        return [
            'totalFriendships' => 0,
            'activeFriendships' => 0,
            'inactiveFriendships' => 0,
            'completedFriendships' => 0,
            'averageDuration' => 0,
            'newFriendships' => 0
        ];
    }
}

private function getFriendshipsCharts($dateFrom, $dateTo)
{
    try {
        // Estado de amistades
        $friendshipStatus = Friendship::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->map(function($item) {
                return [
                    'status' => $item->status,
                    'total' => $item->total
                ];
            });

        // Nuevas amistades por mes (últimos 6 meses)
        $newFriendshipsByMonth = Friendship::select(
                DB::raw('YEAR(start_date) as year'),
                DB::raw('MONTH(start_date) as month'),
                DB::raw('count(*) as total')
            )
            ->where('start_date', '>=', now()->subMonths(6))
            ->where('start_date', '<=', $dateTo)
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'period' => Carbon::create($item->year, $item->month)->format('M Y'),
                    'total' => $item->total
                ];
            });

        return [
            'friendshipStatus' => $friendshipStatus,
            'newFriendshipsByMonth' => $newFriendshipsByMonth
        ];

    } catch (\Exception $e) {
        Log::error('Error getting friendships charts: ' . $e->getMessage());
        return [
            'friendshipStatus' => [],
            'newFriendshipsByMonth' => []
        ];
    }
}

// private function getFriendshipsList($dateFrom, $dateTo)
// {
//     try {
//         return Friendship::with(['buddy', 'peerBuddy'])
//             ->whereBetween('start_date', [$dateFrom, $dateTo])
//             ->orderBy('start_date', 'desc')
//             ->get()
//             ->map(function ($friendship) {
//                 $startDate = Carbon::parse($friendship->start_date);
//                 $endDate = $friendship->end_date ? Carbon::parse($friendship->end_date) : null;
                
//                 return [
//                     'id' => $friendship->id,
//                     'buddy_name' => $friendship->buddy->first_name.' '.$friendship->buddy->last_name,
//                     'peer_buddy_name' => $friendship->peerBuddy->first_name.' '.$friendship->peerBuddy->last_name,
//                     'start_date' => $friendship->start_date,
//                     'formatted_start_date' => $startDate->format('d/m/Y'),
//                     'end_date' => $friendship->end_date,
//                     'formatted_end_date' => $endDate ? $endDate->format('d/m/Y') : 'En curso',
//                     'status' => $friendship->status,
//                     'duration_days' => $endDate ? $endDate->diffInDays($startDate) : now()->diffInDays($startDate),
//                     'notes' => $friendship->notes ?? 'Sin notas',
//                     // Datos adicionales para el modal (sin afectar la tabla)
//                     'buddy_data' => [
//                         'disability' => $friendship->buddy->disability ?? 'No especificada',
//                         'age' => $friendship->buddy->age ?? 'No especificado',
//                         'ci' => $friendship->buddy->ci ?? 'No especificado',
//                         'phone' => $friendship->buddy->phone ?? 'No especificado',
//                         'email' => $friendship->buddy->email ?? 'No especificado',
//                         'address' => $friendship->buddy->address ?? 'No especificada'
//                     ],
//                     'peer_buddy_data' => [
//                         'age' => $friendship->peerBuddy->age ?? 'No especificado',
//                         'ci' => $friendship->peerBuddy->ci ?? 'No especificado',
//                         'phone' => $friendship->peerBuddy->phone ?? 'No especificado',
//                         'email' => $friendship->peerBuddy->email ?? 'No especificado',
//                         'address' => $friendship->peerBuddy->address ?? 'No especificada'
//                     ]
//                 ];
//             });

//     } catch (\Exception $e) {
//         Log::error('Error getting friendships list: ' . $e->getMessage());
//         return [];
//     }
// }

private function getFriendshipsList($dateFrom, $dateTo)
{
    try {
        return Friendship::with(['buddy', 'peerBuddy'])
            ->whereBetween('start_date', [$dateFrom, $dateTo])
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function ($friendship) {
                $startDate = Carbon::parse($friendship->start_date);
                $endDate = $friendship->end_date ? Carbon::parse($friendship->end_date) : null;
                
                // Construir nombres usando first_name y last_name
                $buddyName = 'N/A';
                $peerBuddyName = 'N/A';
                
                if ($friendship->buddy) {
                    $buddyName = trim(($friendship->buddy->first_name ?? '') . ' ' . ($friendship->buddy->last_name ?? ''));
                    if (empty($buddyName)) {
                        $buddyName = $friendship->buddy->name ?? 'N/A';
                    }
                }
                
                if ($friendship->peerBuddy) {
                    $peerBuddyName = trim(($friendship->peerBuddy->first_name ?? '') . ' ' . ($friendship->peerBuddy->last_name ?? ''));
                    if (empty($peerBuddyName)) {
                        $peerBuddyName = $friendship->peerBuddy->name ?? 'N/A';
                    }
                }
                
                return [
                    'id' => $friendship->id,
                    'buddy_name' => $buddyName,
                    'peer_buddy_name' => $peerBuddyName,
                    'start_date' => $friendship->start_date,
                    'formatted_start_date' => $startDate->format('d/m/Y'),
                    'end_date' => $friendship->end_date,
                    'formatted_end_date' => $endDate ? $endDate->format('d/m/Y') : 'En curso',
                    'status' => $friendship->status,
                    'duration_days' => $endDate ? $endDate->diffInDays($startDate) : now()->diffInDays($startDate),
                    'notes' => $friendship->notes ?? 'Sin notas',                    
                    'buddy_data' => [
                        'disability' => $friendship->buddy->disability ?? 'No especificada',
                        'age' => $friendship->buddy->age ?? 'No especificado',
                        'ci' => $friendship->buddy->ci ?? 'No especificado',
                        'phone' => $friendship->buddy->phone ?? 'No especificado',
                        'email' => $friendship->buddy->email ?? 'No especificado',
                        'address' => $friendship->buddy->address ?? 'No especificada'
                    ],
                    'peer_buddy_data' => [
                        'age' => $friendship->peerBuddy->age ?? 'No especificado',
                        'ci' => $friendship->peerBuddy->ci ?? 'No especificado',
                        'phone' => $friendship->peerBuddy->phone ?? 'No especificado',
                        'email' => $friendship->peerBuddy->email ?? 'No especificado',
                        'address' => $friendship->peerBuddy->address ?? 'No especificada'
                    ]
                ];
            });

    } catch (\Exception $e) {
        return [];
    }
}

// En ReportsController.php
public function saveConfig(Request $request)
{
    try {
        // Validar datos
        $validated = $request->validate([
            'export_format' => 'required|in:pdf,excel,csv',
            'include_charts' => 'boolean',
            'auto_refresh' => 'boolean'
        ]);

        // Guardar configuración (puedes usar preferencias del usuario o caché)
        // Por ejemplo, usando cache:
        Cache::put('reports_config_' . auth()->id(), $validated, now()->addDays(30));

        return response()->json([
            'success' => true,
            'message' => 'Configuración guardada correctamente'
        ]);

    } catch (\Exception $e) {
        Log::error('Error saving config: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error al guardar la configuración'
        ], 500);
    }
}

public function getReportData(Request $request)
{
    $dateFrom = $request->input('from');
    $dateTo = $request->input('to');
    
    // Query base con filtros de fecha
    $query = Activity::query();
    
    if ($dateFrom) {
        $query->where('date', '>=', $dateFrom);
    }
    
    if ($dateTo) {
        $query->where('date', '<=', $dateTo);
    }
    
    $activities = $query->get();
    
    // Estadísticas básicas
    $statistics = [
        'total_activities' => $activities->count(),
        'completed' => $activities->where('status', 'completed')->count(),
        'scheduled' => $activities->where('status', 'scheduled')->count(),
        'cancelled' => $activities->where('status', 'cancelled')->count(),
        'in_progress' => $activities->where('status', 'in_progress')->count(),
    ];
    
    // Datos para gráficos
    $typeLabels = [
        'recreational' => 'Recreativa',
        'educational' => 'Educativa', 
        'cultural' => 'Cultural',
        'sports' => 'Deportiva',
        'social' => 'Social'
    ];
    
    $statusLabels = [
        'scheduled' => 'Programada',
        'in_progress' => 'En Progreso',
        'completed' => 'Completada',
        'cancelled' => 'Cancelada'
    ];
    
    $charts = [
        'participationByType' => $activities->groupBy('type')->map(function ($group, $type) use ($typeLabels) {
            return [
                'type' => $typeLabels[$type] ?? ucfirst($type),
                'total' => $group->count()
            ];
        })->values(),
        
        'statusComparison' => $activities->groupBy('status')->map(function ($group, $status) use ($statusLabels) {
            return [
                'status' => $statusLabels[$status] ?? ucfirst($status),
                'total' => $group->count()
            ];
        })->values(),
        
        'monthlyTrend' => $activities->groupBy(function ($activity) {
            return $activity->date->format('d/m/Y');
        })->sortKeys()->map(function ($group, $date) {
            return [
                'period' => $date,
                'total' => $group->count()
            ];
        })->values()
    ];
    
    // Actividades con datos formateados para la tabla
    $activitiesFormatted = $activities->map(function ($activity) {
        return [
            'id' => $activity->id,
            'title' => $activity->title,
            'description' => $activity->description,
            'date' => $activity->date,
            'formatted_date' => $activity->date->format('d/m/Y'),
            'start_time' => $activity->start_time,
            'end_time' => $activity->end_time,
            'location' => $activity->location,
            'type' => $activity->type,
            'type_label' => ucfirst($activity->type),
            'status' => $activity->status,
            'status_label' => ucfirst(str_replace('_', ' ', $activity->status)),
        ];
    });
    
    return response()->json([
        'success' => true,
        'statistics' => $statistics,
        'charts' => $charts,
        'activities' => $activitiesFormatted
    ]);
}
public function liderazgo(Request $request)
    {
        try {
            Log::info('Iniciando carga de datos de liderazgo');
            
            // Validar fechas con manejo de errores
            try {
                $from = $request->input('from', Carbon::now()->subMonth()->format('Y-m-d'));
                $to = $request->input('to', Carbon::now()->format('Y-m-d'));
                
                // Validar formato de fechas
                Carbon::parse($from);
                Carbon::parse($to);
                
                Log::info("Rango de fechas: {$from} - {$to}");
            } catch (\Exception $e) {
                Log::error('Error parsing dates: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'error' => 'Formato de fecha inválido'
                ], 400);
            }

            // Verificar que el modelo existe
            if (!class_exists('App\Models\MonthlyMonitoringReport')) {
                Log::error('MonthlyMonitoringReport model does not exist');
                return response()->json([
                    'success' => false,
                    'error' => 'Modelo MonthlyMonitoringReport no encontrado'
                ], 500);
            }

            // Obtener datos con manejo de errores de base de datos
            try {
                $reports = MonthlyMonitoringReport::whereBetween('created_at', [$from, $to])
                    ->with(['friendship.buddy', 'friendship.peerBuddy'])
                    ->get();

                // Si no hay reportes en el período, obtener los más recientes
                if ($reports->isEmpty()) {
                    $reports = MonthlyMonitoringReport::with(['friendship.buddy', 'friendship.peerBuddy'])
                        ->latest()
                        ->take(15)
                        ->get();
                }

                Log::info("Reportes encontrados: {$reports->count()}");
            } catch (\Exception $e) {
                Log::error('Database query error: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'error' => 'Error al consultar la base de datos: ' . $e->getMessage()
                ], 500);
            }

            // Procesar datos con valores por defecto seguros
            $leadersData = [];
            $monitorNames = $reports->pluck('monitor_name')->filter()->unique();
            
            $totalParticipation = 0;
            $totalSatisfaction = 0;
            $needsAttention = 0;
            $veryActive = 0;

            foreach ($monitorNames as $monitorName) {
                if (empty($monitorName)) continue; // Skip empty names
                
                $monitorReports = $reports->where('monitor_name', $monitorName);
                
                // Calcular métricas promedio para este monitor
                $participationScore = $this->calculateAverageParticipation($monitorReports);
                $satisfactionScore = $this->calculateAverageSatisfaction($monitorReports);
                $evaluationScore = $this->calculateAverageEvaluation($monitorReports);
                
                // Contar amistades y actividades con valores seguros
                $friendshipsCount = $monitorReports->pluck('friendship_id')->filter()->unique()->count();
                $activitiesCount = $monitorReports->count();
                
                // Determinar si necesita atención
                $needsAttentionFlag = $monitorReports->where('requires_attention', 'si')->count() > 0;
                if ($needsAttentionFlag) $needsAttention++;
                
                // Contar muy activos
                if ($participationScore >= 80) $veryActive++;
                
                // Crear datos del líder con valores seguros
                $leadersData[] = [
                    'id' => crc32($monitorName), // ID único basado en el nombre
                    'name' => $monitorName,
                    'email' => strtolower(str_replace(' ', '.', $monitorName)) . '@monitor.com',
                    'initials' => $this->getInitials($monitorName),
                    'activities_count' => $activitiesCount,
                    'friendships_count' => $friendshipsCount,
                    'participation' => $this->getParticipationLevel($participationScore),
                    'participation_label' => $this->getParticipationLabel($participationScore),
                    'satisfaction' => $this->getSatisfactionLevel($satisfactionScore),
                    'satisfaction_label' => $this->getSatisfactionLabel($satisfactionScore),
                    'evaluation' => $this->getEvaluationLevel($evaluationScore),
                    'evaluation_label' => $this->getEvaluationLabel($evaluationScore),
                    'needs_attention' => $needsAttentionFlag,
                    'participation_score' => $participationScore,
                    'satisfaction_score' => $satisfactionScore,
                    'evaluation_score' => $evaluationScore,
                    'last_activity' => $monitorReports->max('created_at')?->format('Y-m-d') ?? 'N/A'
                ];
                
                $totalParticipation += $participationScore;
                $totalSatisfaction += $satisfactionScore;
            }

            // Calcular estadísticas generales
            $totalLeaders = count($leadersData);
            $avgSatisfaction = $totalLeaders > 0 ? round($totalSatisfaction / $totalLeaders) : 0;

            // Preparar datos para gráficos
            $chartData = $this->prepareChartData($leadersData);

            $responseData = [
                'stats' => [
                    'totalLeaders' => $totalLeaders,
                    'veryActive' => $veryActive,
                    'needsAttention' => $needsAttention,
                    'avgSatisfaction' => $avgSatisfaction . '%'
                ],
                'leaders' => $leadersData,
                'charts' => $chartData
            ];

            Log::info('Datos de liderazgo preparados exitosamente', [
                'total_leaders' => $totalLeaders,
                'very_active' => $veryActive,
                'needs_attention' => $needsAttention
            ]);

            return response()->json([
                'success' => true,
                'data' => $responseData
            ]);

        } catch (\Exception $e) {
            Log::error('Error en reports/liderazgo: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Error interno del servidor. Revise los logs para más detalles.'
            ], 500);
        }
    }

// Métodos auxiliares para calcular promedios desde los reportes
// Métodos auxiliares para calcular promedios desde los reportes
    private function calculateAverageParticipation($reports)
    {
        if ($reports->isEmpty()) return 50;
        
        $scores = $reports->map(function($report) {
            return $this->mapParticipationToScore($report->leader_participation ?? 'moderado');
        })->filter(); // Remove null values
        
        return $scores->isEmpty() ? 50 : round($scores->avg());
    }

private function calculateAverageSatisfaction($reports)
    {
        if ($reports->isEmpty()) return 50;
        
        $scores = $reports->map(function($report) {
            return $this->mapSatisfactionToScore($report->leader_satisfaction ?? 'neutral');
        })->filter(); // Remove null values
        
        return $scores->isEmpty() ? 50 : round($scores->avg());
    }

private function calculateAverageEvaluation($reports)
    {
        if ($reports->isEmpty()) return 50;
        
        $scores = $reports->map(function($report) {
            return $this->mapEvaluationToScore($report->general_evaluation ?? 'regular');
        })->filter(); // Remove null values
        
        return $scores->isEmpty() ? 50 : round($scores->avg());
    }

private function mapParticipationToScore($participation)
    {
        $mapping = [
            'muy-activo' => 85,
            'activo' => 70,
            'moderado' => 55,
            'pasivo' => 25,
            'muy-pasivo' => 10
        ];
        
        return $mapping[$participation] ?? 50;
    }

private function mapSatisfactionToScore($satisfaction)
    {
        $mapping = [
            'muy-satisfecho' => 90,
            'satisfecho' => 70,
            'neutral' => 45,
            'insatisfecho' => 25,
            'muy-insatisfecho' => 10
        ];
        
        return $mapping[$satisfaction] ?? 50;
    }

private function mapEvaluationToScore($evaluation)
    {
        $mapping = [
            'excelente' => 88,
            'buena' => 63,
            'regular' => 35,
            'deficiente' => 20,
            'critica' => 10
        ];
        
        return $mapping[$evaluation] ?? 50;
    }

private function getInitials($name)
    {
        if (empty($name)) return 'XX';
        
        $words = explode(' ', trim($name));
        $initials = '';
        
        foreach ($words as $word) {
            if (strlen($word) > 0) {
                $initials .= strtoupper($word[0]);
            }
            if (strlen($initials) >= 2) break;
        }
        
        return $initials ?: 'XX';
    }

private function getParticipationLevel($score)
    {
        if ($score >= 80) return 'muy-activo';
        if ($score >= 60) return 'activo';
        if ($score >= 40) return 'moderado';
        if ($score >= 20) return 'pasivo';
        return 'muy-pasivo';
    }

    private function getParticipationLabel($score)
    {
        if ($score >= 80) return 'Muy Activo';
        if ($score >= 60) return 'Activo';
        if ($score >= 40) return 'Moderado';
        if ($score >= 20) return 'Pasivo';
        return 'Muy Pasivo';
    }

private function getSatisfactionLevel($score)
    {
        if ($score >= 80) return 'muy-satisfecho';
        if ($score >= 60) return 'satisfecho';
        if ($score >= 40) return 'neutral';
        if ($score >= 20) return 'insatisfecho';
        return 'muy-insatisfecho';
    }

    private function getSatisfactionLabel($score)
    {
        if ($score >= 80) return 'Muy Satisfecho';
        if ($score >= 60) return 'Satisfecho';
        if ($score >= 40) return 'Neutral';
        if ($score >= 20) return 'Insatisfecho';
        return 'Muy Insatisfecho';
    }

private function getEvaluationLevel($score)
    {
        if ($score >= 85) return 'excelente';
        if ($score >= 65) return 'buena';
        if ($score >= 45) return 'regular';
        if ($score >= 25) return 'deficiente';
        return 'critica';
    }

    private function getEvaluationLabel($score)
    {
        if ($score >= 85) return 'Excelente';
        if ($score >= 65) return 'Buena';
        if ($score >= 45) return 'Regular';
        if ($score >= 25) return 'Deficiente';
        return 'Crítica';
    }

private function prepareChartData($leadersData)
    {
        // Contar niveles de participación
        $participationCounts = [
            'muy-activo' => 0,
            'activo' => 0,
            'moderado' => 0,
            'pasivo' => 0,
            'muy-pasivo' => 0
        ];
        
        // Contar niveles de satisfacción
        $satisfactionCounts = [
            'muy-satisfecho' => 0,
            'satisfecho' => 0,
            'neutral' => 0,
            'insatisfecho' => 0,
            'muy-insatisfecho' => 0
        ];

        foreach ($leadersData as $leader) {
            if (isset($participationCounts[$leader['participation']])) {
                $participationCounts[$leader['participation']]++;
            }
            if (isset($satisfactionCounts[$leader['satisfaction']])) {
                $satisfactionCounts[$leader['satisfaction']]++;
            }
        }

        return [
            'participation' => [
                'labels' => ['Muy Activo', 'Activo', 'Moderado', 'Pasivo', 'Muy Pasivo'],
                'data' => array_values($participationCounts)
            ],
            'satisfaction' => [
                'labels' => ['Muy Satisfecho', 'Satisfecho', 'Neutral', 'Insatisfecho', 'Muy Insatisfecho'],
                'data' => array_values($satisfactionCounts)
            ]
        ];
    }

// Reemplaza el inicio del método export con esta validación mejorada:
public function export(Request $request)
{
    try {
        $validated = $request->validate([
            'type' => 'required|in:general,actividades,amistades,liderazgo',
            'format' => 'required|in:pdf,excel,csv',
            'period_type' => 'required|in:annual', // Solo anual
            'year' => 'required|integer|min:2020|max:' . (date('Y') + 1), // Año válido
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from'
        ]);

        Log::info('Iniciando exportación de reporte', $validated);

        $reportType = $validated['type'];
        $format = $validated['format'];
        
        // Configurar timezone de Bolivia
        Carbon::setLocale('es');
        $timezone = 'America/La_Paz';
        
        // Solo manejar período anual
        $year = $validated['year'];
        $dateFrom = Carbon::create($year, 1, 1, 0, 0, 0, $timezone)->startOfYear();
        $dateTo = Carbon::create($year, 12, 31, 23, 59, 59, $timezone)->endOfYear();

        Log::info("Rango de fechas calculado: {$dateFrom} a {$dateTo}");

        // Obtener datos según el tipo de reporte
        switch ($reportType) {
            case 'general':
                $data = $this->getGeneralReportData($dateFrom, $dateTo);
                break;
            case 'actividades':
                $data = $this->getActivitiesReportData($dateFrom, $dateTo);
                break;
            case 'amistades':
                $data = $this->getFriendshipsReportData($dateFrom, $dateTo);
                break;
            case 'liderazgo':
                $data = $this->getLeadershipReportData($dateFrom, $dateTo);
                break;
            default:
                throw new \Exception('Tipo de reporte no válido');
        }

        // Validar que tenemos datos
        if (empty($data) || !isset($data['title'])) {
            Log::warning('Datos de reporte vacíos o inválidos', ['data' => $data]);
            throw new \Exception('No se pudieron obtener los datos del reporte');
        }

        Log::info('Datos del reporte obtenidos correctamente', [
            'type' => $reportType,
            'has_statistics' => isset($data['statistics']),
            'statistics_count' => isset($data['statistics']) ? count($data['statistics']) : 0
        ]);

        // Exportar según el formato
        switch ($format) {
            case 'pdf':
                return $this->exportToPDF($data, $reportType, $dateFrom, $dateTo);
            case 'excel':
                return $this->exportToExcel($data, $reportType, $dateFrom, $dateTo);
            case 'csv':
                return $this->exportToCSV($data, $reportType, $dateFrom, $dateTo);
            default:
                throw new \Exception('Formato de exportación no válido');
        }

    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error('Error de validación en exportación', ['errors' => $e->errors()]);
        return response()->json([
            'success' => false,
            'error' => 'Datos de entrada inválidos',
            'validation_errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        Log::error('Error en exportación', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json([
            'success' => false,
            'error' => 'Error al exportar el reporte: ' . $e->getMessage()
        ], 500);
    }
}

   private function calculateDateRange($validated, $timezone = 'America/La_Paz')
{
    $now = Carbon::now($timezone);
    $periodType = $validated['period_type'];
    $year = $validated['year'];

    switch ($periodType) {
        case 'monthly':
            $month = $validated['month'] ?? $now->month;
            $dateFrom = Carbon::create($year, $month, 1, 0, 0, 0, $timezone)->startOfMonth();
            $dateTo = Carbon::create($year, $month, 1, 0, 0, 0, $timezone)->endOfMonth();
            break;
            
        case 'annual':
            $dateFrom = Carbon::create($year, 1, 1, 0, 0, 0, $timezone)->startOfYear();
            $dateTo = Carbon::create($year, 12, 31, 23, 59, 59, $timezone)->endOfYear();
            break;
            
        case 'custom':
            if (!isset($validated['from']) || !isset($validated['to'])) {
                throw new \Exception('Fechas de inicio y fin requeridas para período personalizado');
            }
            $dateFrom = Carbon::parse($validated['from'], $timezone)->startOfDay();
            $dateTo = Carbon::parse($validated['to'], $timezone)->endOfDay();
            break;
            
        default:
            throw new \Exception('Tipo de período no válido');
    }

    return [$dateFrom, $dateTo];
} 
   private function generatePlainTextPDFHTML($data, $reportType, $dateFrom, $dateTo)
    {
        $content = $this->generatePlainTextContent($data, $reportType);
        
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>' . htmlspecialchars($data['title']) . '</title>
            <style>
                body { 
                    font-family: "Courier New", monospace; 
                    margin: 20px; 
                    color: #000;
                    line-height: 1.4;
                    font-size: 12px;
                    white-space: pre-wrap;
                }
            </style>
        </head>
        <body>' . htmlspecialchars($content) . '</body></html>';
        
        return $html;
    }
   private function exportToPDF($data, $reportType, $dateFrom, $dateTo)
    {
        try {
            if (!app()->bound('dompdf.wrapper')) {
                throw new \Exception('DomPDF no está configurado correctamente');
            }
            
            $pdf = app('dompdf.wrapper');
            $html = $this->generatePlainTextPDFHTML($data, $reportType, $dateFrom, $dateTo);
            $pdf->loadHTML($html);
            $pdf->setPaper('A4', 'portrait');
            
            $filename = $this->generateFileName($reportType, 'pdf', $dateFrom, $dateTo);
            
            return $pdf->download($filename);
        } catch (\Exception $e) {
            Log::error('Error exportando PDF: ' . $e->getMessage());
            throw new \Exception('Error al generar PDF: ' . $e->getMessage());
        }
    }

//    private function exportToExcel($data, $reportType, $dateFrom, $dateTo)
// {
//     try {
//         $filename = $this->generateFileName($reportType, 'xlsx', $dateFrom, $dateTo);
//         $content = $this->generatePlainTextContent($data, $reportType);
        
//         // Crear archivo temporal
//         $tempFile = tempnam(sys_get_temp_dir(), 'report_');
//         $handle = fopen($tempFile, 'w');
        
//         // Escribir BOM para UTF-8
//         fwrite($handle, "\xEF\xBB\xBF");
        
//         // Escribir contenido
//         fwrite($handle, $content);
        
//         fclose($handle);
        
//         // Retornar respuesta de descarga
//         return response()->download($tempFile, $filename, [
//             'Content-Type' => 'application/vnd.ms-excel',
//             'Content-Disposition' => 'attachment; filename="' . $filename . '"'
//         ])->deleteFileAfterSend(true);
        
//     } catch (\Exception $e) {
//         Log::error('Error exportando Excel: ' . $e->getMessage());
//         throw new \Exception('Error al generar Excel: ' . $e->getMessage());
//     }
// }

private function exportToExcel($data, $reportType, $dateFrom, $dateTo)
{
    try {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Configurar propiedades del documento
        $spreadsheet->getProperties()
            ->setCreator('Sistema de Gestión - Programa de Amistad')
            ->setTitle($data['title'])
            ->setSubject('Reporte ' . ucfirst($reportType))
            ->setDescription('Reporte generado automáticamente');

        $currentRow = 1;
        
        // Título principal
        $sheet->setCellValue('A' . $currentRow, $data['title']);
        $sheet->mergeCells('A' . $currentRow . ':G' . $currentRow);
        $this->styleHeader($sheet, 'A' . $currentRow . ':G' . $currentRow);
        $currentRow += 2;

        // Información del período
        $sheet->setCellValue('A' . $currentRow, 'Período: ' . $data['period']);
        $sheet->setCellValue('A' . ($currentRow + 1), 'Generado: ' . $data['generated_at']);
        $currentRow += 3;

        // Estadísticas
        if (isset($data['statistics']) && !empty($data['statistics'])) {
            $sheet->setCellValue('A' . $currentRow, 'ESTADÍSTICAS GENERALES');
            $this->styleSubHeader($sheet, 'A' . $currentRow . ':B' . $currentRow);
            $currentRow++;

            foreach ($data['statistics'] as $key => $value) {
                $label = $this->formatStatisticLabel($key);
                $sheet->setCellValue('A' . $currentRow, $label);
                $sheet->setCellValue('B' . $currentRow, $value);
                $currentRow++;
            }
            $currentRow++;
        }

        // Contenido específico según tipo de reporte
        switch ($reportType) {
            case 'general':
                $currentRow = $this->addGeneralExcelContent($sheet, $data, $currentRow);
                break;
            case 'actividades':
                $currentRow = $this->addActivitiesExcelContent($sheet, $data, $currentRow);
                break;
            case 'amistades':
                $currentRow = $this->addFriendshipsExcelContent($sheet, $data, $currentRow);
                break;
            case 'liderazgo':
                $currentRow = $this->addLeadershipExcelContent($sheet, $data, $currentRow);
                break;
        }

        // Ajustar ancho de columnas
        $this->autoSizeColumns($sheet);

        // Generar archivo
        $filename = $this->generateFileName($reportType, 'xlsx', $dateFrom, $dateTo);
        $tempFile = tempnam(sys_get_temp_dir(), 'excel_report_');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ])->deleteFileAfterSend(true);

    } catch (\Exception $e) {
        Log::error('Error exportando Excel: ' . $e->getMessage());
        throw new \Exception('Error al generar Excel: ' . $e->getMessage());
    }
}

private function styleHeader($sheet, $range)
{
    $sheet->getStyle($range)->applyFromArray([
        'font' => [
            'bold' => true,
            'size' => 16,
            'color' => ['rgb' => 'FFFFFF']
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => '007BFF']
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER
        ]
    ]);
}

private function styleSubHeader($sheet, $range)
{
    $sheet->getStyle($range)->applyFromArray([
        'font' => [
            'bold' => true,
            'size' => 12,
            'color' => ['rgb' => '000000']
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'E3F2FD']
        ]
    ]);
}

private function styleTableHeader($sheet, $range)
{
    $sheet->getStyle($range)->applyFromArray([
        'font' => [
            'bold' => true,
            'color' => ['rgb' => 'FFFFFF']
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => '28A745']
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['rgb' => '000000']
            ]
        ]
    ]);
}

private function addGeneralExcelContent($sheet, $data, $currentRow)
{
    // Resumen de actividades
    if (isset($data['activities']) && count($data['activities']) > 0) {
        $sheet->setCellValue('A' . $currentRow, 'RESUMEN DE ACTIVIDADES RECIENTES');
        $this->styleSubHeader($sheet, 'A' . $currentRow . ':E' . $currentRow);
        $currentRow++;

        // Headers
        $headers = ['Título', 'Fecha', 'Tipo', 'Estado', 'Ubicación'];
        foreach ($headers as $index => $header) {
            $column = chr(65 + $index); // A, B, C, D, E
            $sheet->setCellValue($column . $currentRow, $header);
        }
        $this->styleTableHeader($sheet, 'A' . $currentRow . ':E' . $currentRow);
        $currentRow++;

        // Datos (máximo 10 actividades)
        foreach (array_slice($data['activities'], 0, 10) as $activity) {
            $sheet->setCellValue('A' . $currentRow, $activity['title'] ?? 'Sin título');
            $sheet->setCellValue('B' . $currentRow, $activity['formatted_date'] ?? 'Sin fecha');
            $sheet->setCellValue('C' . $currentRow, $activity['type_label'] ?? 'Sin tipo');
            $sheet->setCellValue('D' . $currentRow, $activity['status_label'] ?? 'Sin estado');
            $sheet->setCellValue('E' . $currentRow, $activity['location'] ?? 'Sin ubicación');
            $currentRow++;
        }
        $currentRow += 2;
    }

    // Resumen de amistades
    if (isset($data['friendships']) && count($data['friendships']) > 0) {
        $sheet->setCellValue('A' . $currentRow, 'RESUMEN DE AMISTADES RECIENTES');
        $this->styleSubHeader($sheet, 'A' . $currentRow . ':F' . $currentRow);
        $currentRow++;

        // Headers
        $headers = ['Buddy', 'Peer Buddy', 'Fecha Inicio', 'Estado', 'Líder 1', 'Líder 2'];
        foreach ($headers as $index => $header) {
            $column = chr(65 + $index);
            $sheet->setCellValue($column . $currentRow, $header);
        }
        $this->styleTableHeader($sheet, 'A' . $currentRow . ':F' . $currentRow);
        $currentRow++;

        // Datos (máximo 10 amistades)
        foreach (array_slice($data['friendships'], 0, 10) as $friendship) {
            $sheet->setCellValue('A' . $currentRow, $friendship['buddy_name'] ?? 'N/A');
            $sheet->setCellValue('B' . $currentRow, $friendship['peer_buddy_name'] ?? 'N/A');
            $sheet->setCellValue('C' . $currentRow, $friendship['formatted_start_date'] ?? 'Sin fecha');
            $sheet->setCellValue('D' . $currentRow, $friendship['status'] ?? 'Sin estado');
            $sheet->setCellValue('E' . $currentRow, $friendship['leader1_name'] ?? 'N/A');
            $sheet->setCellValue('F' . $currentRow, $friendship['leader2_name'] ?? 'N/A');
            $currentRow++;
        }
    }

    return $currentRow;
}

private function addActivitiesExcelContent($sheet, $data, $currentRow)
{
    if (isset($data['activities']) && count($data['activities']) > 0) {
        $sheet->setCellValue('A' . $currentRow, 'LISTADO DETALLADO DE ACTIVIDADES');
        $this->styleSubHeader($sheet, 'A' . $currentRow . ':G' . $currentRow);
        $currentRow++;

        // Headers
        $headers = ['#', 'Título', 'Fecha', 'Tipo', 'Estado', 'Ubicación', 'Descripción'];
        foreach ($headers as $index => $header) {
            $column = chr(65 + $index);
            $sheet->setCellValue($column . $currentRow, $header);
        }
        $this->styleTableHeader($sheet, 'A' . $currentRow . ':G' . $currentRow);
        $currentRow++;

        // Datos
        foreach ($data['activities'] as $index => $activity) {
            $sheet->setCellValue('A' . $currentRow, $index + 1);
            $sheet->setCellValue('B' . $currentRow, $activity['title'] ?? 'Sin título');
            $sheet->setCellValue('C' . $currentRow, $activity['formatted_date'] ?? 'Sin fecha');
            $sheet->setCellValue('D' . $currentRow, $activity['type_label'] ?? 'Sin tipo');
            $sheet->setCellValue('E' . $currentRow, $activity['status_label'] ?? 'Sin estado');
            $sheet->setCellValue('F' . $currentRow, $activity['location'] ?? 'Sin ubicación');
            $sheet->setCellValue('G' . $currentRow, $activity['description'] ?? 'Sin descripción');
            $currentRow++;
        }
    }

    return $currentRow;
}

private function addFriendshipsExcelContent($sheet, $data, $currentRow)
{
    if (isset($data['friendships']) && count($data['friendships']) > 0) {
        $sheet->setCellValue('A' . $currentRow, 'LISTADO DETALLADO DE AMISTADES');
        $this->styleSubHeader($sheet, 'A' . $currentRow . ':H' . $currentRow);
        $currentRow++;

        // Headers
        $headers = ['#', 'Buddy', 'Peer Buddy', 'Fecha Inicio', 'Estado', 'Duración (días)', 'Líder 1', 'Líder 2'];
        foreach ($headers as $index => $header) {
            $column = chr(65 + $index);
            $sheet->setCellValue($column . $currentRow, $header);
        }
        $this->styleTableHeader($sheet, 'A' . $currentRow . ':H' . $currentRow);
        $currentRow++;

        // Datos
        foreach ($data['friendships'] as $index => $friendship) {
            $sheet->setCellValue('A' . $currentRow, $index + 1);
            $sheet->setCellValue('B' . $currentRow, $friendship['buddy_name'] ?? 'N/A');
            $sheet->setCellValue('C' . $currentRow, $friendship['peer_buddy_name'] ?? 'N/A');
            $sheet->setCellValue('D' . $currentRow, $friendship['formatted_start_date'] ?? 'Sin fecha');
            $sheet->setCellValue('E' . $currentRow, $this->getFriendshipStatusLabel($friendship['status'] ?? ''));
            $sheet->setCellValue('F' . $currentRow, $friendship['duration_days'] ?? 0);
            $sheet->setCellValue('G' . $currentRow, $friendship['leader1_name'] ?? 'N/A');
            $sheet->setCellValue('H' . $currentRow, $friendship['leader2_name'] ?? 'N/A');
            $currentRow++;
        }
    }

    // Evaluaciones si existen
    if (isset($data['evaluations']) && count($data['evaluations']) > 0) {
        $currentRow += 2;
        $sheet->setCellValue('A' . $currentRow, 'EVALUACIONES REALIZADAS');
        $this->styleSubHeader($sheet, 'A' . $currentRow . ':D' . $currentRow);
        $currentRow++;

        $headers = ['Amistad', 'Fecha', 'Puntuación', 'Comentarios'];
        foreach ($headers as $index => $header) {
            $column = chr(65 + $index);
            $sheet->setCellValue($column . $currentRow, $header);
        }
        $this->styleTableHeader($sheet, 'A' . $currentRow . ':D' . $currentRow);
        $currentRow++;

        foreach ($data['evaluations'] as $evaluation) {
            $sheet->setCellValue('A' . $currentRow, $evaluation['friendship_name'] ?? 'N/A');
            $sheet->setCellValue('B' . $currentRow, $evaluation['date'] ?? 'Sin fecha');
            $sheet->setCellValue('C' . $currentRow, $evaluation['score'] ?? 'N/A');
            $sheet->setCellValue('D' . $currentRow, $evaluation['comments'] ?? 'Sin comentarios');
            $currentRow++;
        }
    }

    return $currentRow;
}

private function addLeadershipExcelContent($sheet, $data, $currentRow)
{
    // Seguimientos
    if (isset($data['followups']) && count($data['followups']) > 0) {
        $sheet->setCellValue('A' . $currentRow, 'LISTADO DE SEGUIMIENTOS');
        $this->styleSubHeader($sheet, 'A' . $currentRow . ':I' . $currentRow);
        $currentRow++;

        $headers = ['#', 'Amistad', 'Fecha', 'Progreso Buddy', 'Progreso Peer', 'Calidad Relación', 'Promedio', 'Logros', 'Desafíos'];
        foreach ($headers as $index => $header) {
            $column = chr(65 + $index);
            $sheet->setCellValue($column . $currentRow, $header);
        }
        $this->styleTableHeader($sheet, 'A' . $currentRow . ':I' . $currentRow);
        $currentRow++;

        foreach ($data['followups'] as $index => $followup) {
            $sheet->setCellValue('A' . $currentRow, $index + 1);
            $sheet->setCellValue('B' . $currentRow, $followup['friendship_name'] ?? 'N/A');
            $sheet->setCellValue('C' . $currentRow, $followup['date'] ?? 'N/A');
            $sheet->setCellValue('D' . $currentRow, $followup['buddy_progress'] ?? 'N/A');
            $sheet->setCellValue('E' . $currentRow, $followup['peer_buddy_progress'] ?? 'N/A');
            $sheet->setCellValue('F' . $currentRow, $followup['relationship_quality'] ?? 'N/A');
            $sheet->setCellValue('G' . $currentRow, $followup['average_progress'] ?? 0);
            $sheet->setCellValue('H' . $currentRow, $followup['goals_achieved'] ?? 'Sin especificar');
            $sheet->setCellValue('I' . $currentRow, $followup['challenges_faced'] ?? 'Sin especificar');
            $currentRow++;
        }
        $currentRow += 2;
    }

    // Reportes mensuales
    if (isset($data['monthly_reports']) && count($data['monthly_reports']) > 0) {
        $sheet->setCellValue('A' . $currentRow, 'REPORTES MENSUALES');
        $this->styleSubHeader($sheet, 'A' . $currentRow . ':F' . $currentRow);
        $currentRow++;

        $headers = ['Monitor', 'Período', 'Amistad', 'Evaluación General', 'Frecuencia Reuniones', 'Requiere Atención'];
        foreach ($headers as $index => $header) {
            $column = chr(65 + $index);
            $sheet->setCellValue($column . $currentRow, $header);
        }
        $this->styleTableHeader($sheet, 'A' . $currentRow . ':F' . $currentRow);
        $currentRow++;

        foreach ($data['monthly_reports'] as $report) {
            $sheet->setCellValue('A' . $currentRow, $report['monitor_name'] ?? 'N/A');
            $sheet->setCellValue('B' . $currentRow, $report['monitoring_period'] ?? 'N/A');
            $sheet->setCellValue('C' . $currentRow, $report['friendship_name'] ?? 'N/A');
            $sheet->setCellValue('D' . $currentRow, $report['general_evaluation'] ?? 'N/A');
            $sheet->setCellValue('E' . $currentRow, $report['meeting_frequency'] ?? 'N/A');
            $sheet->setCellValue('F' . $currentRow, $report['requires_attention'] ?? 'N/A');
            $currentRow++;
        }
        $currentRow += 2;
    }

    // Asistencia
    if (isset($data['attendance']) && count($data['attendance']) > 0) {
        $sheet->setCellValue('A' . $currentRow, 'REGISTRO DE ASISTENCIA');
        $this->styleSubHeader($sheet, 'A' . $currentRow . ':E' . $currentRow);
        $currentRow++;

        $headers = ['Amistad', 'Fecha', 'Buddy Asistió', 'Peer Buddy Asistió', 'Notas'];
        foreach ($headers as $index => $header) {
            $column = chr(65 + $index);
            $sheet->setCellValue($column . $currentRow, $header);
        }
        $this->styleTableHeader($sheet, 'A' . $currentRow . ':E' . $currentRow);
        $currentRow++;

        foreach ($data['attendance'] as $attend) {
            $sheet->setCellValue('A' . $currentRow, $attend['friendship_name'] ?? 'N/A');
            $sheet->setCellValue('B' . $currentRow, $attend['date'] ?? 'N/A');
            $sheet->setCellValue('C' . $currentRow, $attend['buddy_attended'] ? 'Sí' : 'No');
            $sheet->setCellValue('D' . $currentRow, $attend['peer_buddy_attended'] ? 'Sí' : 'No');
            $sheet->setCellValue('E' . $currentRow, $attend['notes'] ?? 'Sin notas');
            $currentRow++;
        }
    }

    return $currentRow;
}

private function autoSizeColumns($sheet)
{
    $highestColumn = $sheet->getHighestColumn();
    $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
    
    for ($col = 1; $col <= $highestColumnIndex; $col++) {
        $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
        $sheet->getColumnDimension($column)->setAutoSize(true);
    }
}
private function exportToCSV($data, $reportType, $dateFrom, $dateTo)
{
    try {
        $filename = $this->generateFileName($reportType, 'csv', $dateFrom, $dateTo);
        $content = $this->generatePlainTextContent($data, $reportType);
        
        $headers = [
            'Content-Type' => 'text/plain; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];

        $callback = function() use ($content) {
            $file = fopen('php://output', 'w');
            
            // Agregar BOM para UTF-8
            fwrite($file, "\xEF\xBB\xBF");
            
            // Escribir contenido
            fwrite($file, $content);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    } catch (\Exception $e) {
        Log::error('Error exportando CSV: ' . $e->getMessage());
        throw new \Exception('Error al generar CSV: ' . $e->getMessage());
    }
}

    private function generateFileName($reportType, $format, $dateFrom, $dateTo)
    {
        $typeNames = [
            'general' => 'Reporte_General',
            'actividades' => 'Reporte_Actividades',
            'amistades' => 'Reporte_Amistades',
            'liderazgo' => 'Reporte_Liderazgo'
        ];
        
        $typeName = $typeNames[$reportType] ?? 'Reporte';
        $dateRange = $dateFrom->format('Y-m-d') . '_a_' . $dateTo->format('Y-m-d');
        
        return $typeName . '_' . $dateRange . '.' . $format;
    }


    private function getGeneralReportData($dateFrom, $dateTo)
    {
        try {
            $totalActivities = Activity::whereBetween('date', [$dateFrom, $dateTo])->count();
            $totalFriendships = Friendship::whereBetween('start_date', [$dateFrom, $dateTo])->count();
            $activeFriendships = Friendship::where('status', 'active')
                ->whereBetween('start_date', [$dateFrom, $dateTo])
                ->count();
            $totalUsers = User::count();
            
            return [
                'title' => 'REPORTE GENERAL DEL PROGRAMA DE AMISTAD',
                'period' => $dateFrom->format('d/m/Y') . ' - ' . $dateTo->format('d/m/Y'),
                'generated_at' => Carbon::now('America/La_Paz')->format('d/m/Y H:i:s'),
                'statistics' => [
                    'total_actividades' => $totalActivities,
                    'total_amistades' => $totalFriendships,
                    'amistades_activas' => $activeFriendships,
                    'total_usuarios' => $totalUsers,
                    'tasa_exito' => $totalFriendships > 0 ? round(($activeFriendships / $totalFriendships) * 100, 2) : 0
                ],
                'activities' => $this->getActivitiesList($dateFrom, $dateTo)->toArray(),
                'friendships' => $this->getFriendshipsList($dateFrom, $dateTo)->toArray()
            ];
        } catch (\Exception $e) {
            Log::error('Error obteniendo datos del reporte general: ' . $e->getMessage());
            throw $e;
        }
    }

   private function getActivitiesReportData($dateFrom, $dateTo)
    {
        try {
            $activities = $this->getActivitiesList($dateFrom, $dateTo);
            $totalActivities = $activities->count();
            $completedActivities = $activities->where('status', 'completed')->count();
            $pendingActivities = $activities->where('status', 'pending')->count();
            
            return [
                'title' => 'REPORTE DE ACTIVIDADES',
                'period' => $dateFrom->format('d/m/Y') . ' - ' . $dateTo->format('d/m/Y'),
                'generated_at' => Carbon::now('America/La_Paz')->format('d/m/Y H:i:s'),
                'statistics' => [
                    'total_actividades' => $totalActivities,
                    'actividades_completadas' => $completedActivities,
                    'actividades_pendientes' => $pendingActivities,
                    'porcentaje_completadas' => $totalActivities > 0 ? round(($completedActivities / $totalActivities) * 100, 2) : 0
                ],
                'activities' => $activities->toArray()
            ];
        } catch (\Exception $e) {
            Log::error('Error obteniendo datos del reporte de actividades: ' . $e->getMessage());
            throw $e;
        }
    }
    private function getFriendshipsReportData($dateFrom, $dateTo)
    {
        try {
            $friendships = $this->getFriendshipsList($dateFrom, $dateTo);
            $totalFriendships = $friendships->count();
            $activeFriendships = $friendships->where('status', 'active')->count();
            $completedFriendships = $friendships->where('status', 'completed')->count();
            
            return [
                'title' => 'REPORTE DE AMISTADES',
                'period' => $dateFrom->format('d/m/Y') . ' - ' . $dateTo->format('d/m/Y'),
                'generated_at' => Carbon::now('America/La_Paz')->format('d/m/Y H:i:s'),
                'statistics' => [
                    'total_amistades' => $totalFriendships,
                    'amistades_activas' => $activeFriendships,
                    'amistades_completadas' => $completedFriendships,
                    'porcentaje_exitosas' => $totalFriendships > 0 ? round(($completedFriendships / $totalFriendships) * 100, 2) : 0
                ],
                'friendships' => $friendships->toArray(),
                'evaluations' => $this->getEvaluationsList($dateFrom, $dateTo)->toArray()
            ];
        } catch (\Exception $e) {
            Log::error('Error obteniendo datos del reporte de amistades: ' . $e->getMessage());
            throw $e;
        }
    }

   // Agregar este método temporal en tu ReportsController para debuggear
private function debugLeadershipReport($dateFrom, $dateTo)
{
    $debug = [];
    
    try {
        // 1. Verificar si existe la tabla roles
        $debug['step_1'] = 'Verificando tabla roles...';
        $rolesExist = \Schema::hasTable('roles');
        $debug['roles_table_exists'] = $rolesExist;
        
        if (!$rolesExist) {
            $debug['error'] = 'La tabla roles no existe';
            return $debug;
        }
        
        // 2. Verificar roles disponibles
        $debug['step_2'] = 'Obteniendo roles...';
        $roles = \DB::table('roles')->get();
        $debug['available_roles'] = $roles->pluck('name')->toArray();
        
        // 3. Intentar obtener usuarios con rol específico
        $debug['step_3'] = 'Buscando usuarios con rol leader...';
        
        // Primero intentar con 'leader'
        $leadersCount1 = User::whereHas('role', function($query) {
            $query->where('name', 'leader');
        })->count();
        $debug['leaders_count_leader'] = $leadersCount1;
        
        // Luego intentar con 'Leader' (mayúscula)
        $leadersCount2 = User::whereHas('role', function($query) {
            $query->where('name', 'Leader');
        })->count();
        $debug['leaders_count_Leader'] = $leadersCount2;
        
        // Intentar con 'líder'
        $leadersCount3 = User::whereHas('role', function($query) {
            $query->where('name', 'líder');
        })->count();
        $debug['leaders_count_lider'] = $leadersCount3;
        
        // 4. Verificar usuarios sin rol
        $debug['step_4'] = 'Verificando usuarios sin rol...';
        $usersWithoutRole = User::whereNull('role_id')->count();
        $debug['users_without_role'] = $usersWithoutRole;
        
        // 5. Verificar total de usuarios
        $debug['total_users'] = User::count();
        
        // 6. Verificar si el método byLeader existe en Friendship
        $debug['step_5'] = 'Verificando scope byLeader...';
        try {
            $testFriendship = Friendship::byLeader(1)->toSql();
            $debug['byLeader_scope_works'] = true;
            $debug['byLeader_sql'] = $testFriendship;
        } catch (\Exception $e) {
            $debug['byLeader_scope_works'] = false;
            $debug['byLeader_error'] = $e->getMessage();
        }
        
        // 7. Verificar tablas relacionadas
        $debug['step_6'] = 'Verificando tablas...';
        $debug['tables_exist'] = [
            'users' => \Schema::hasTable('users'),
            'roles' => \Schema::hasTable('roles'),
            'friendships' => \Schema::hasTable('friendships'),
            'friendship_follow_ups' => \Schema::hasTable('friendship_follow_ups'),
            'monthly_monitoring_reports' => \Schema::hasTable('monthly_monitoring_reports'),
            'friendship_attendances' => \Schema::hasTable('friendship_attendances'),
        ];
        
        // 8. Verificar estructura de la tabla friendships
        $debug['step_7'] = 'Verificando columnas de friendships...';
        if (\Schema::hasTable('friendships')) {
            $friendshipColumns = \Schema::getColumnListing('friendships');
            $debug['friendship_columns'] = $friendshipColumns;
            $debug['has_buddy_leader_id'] = in_array('buddy_leader_id', $friendshipColumns);
            $debug['has_peer_buddy_leader_id'] = in_array('peer_buddy_leader_id', $friendshipColumns);
        }
        
        // 9. Contar registros básicos
        $debug['step_8'] = 'Contando registros...';
        $debug['counts'] = [
            'friendships_total' => Friendship::count(),
            'friendship_follow_ups_total' => FriendshipFollowUp::count(),
            'monthly_reports_total' => MonthlyMonitoringReport::count(),
            'attendance_total' => FriendshipAttendance::count(),
        ];
        
        $debug['success'] = true;
        
    } catch (\Exception $e) {
        $debug['error'] = $e->getMessage();
        $debug['trace'] = $e->getTraceAsString();
        $debug['success'] = false;
    }
    
    return $debug;
}

private function getLeadershipReportData($dateFrom, $dateTo)
{
    try {
        // Buscar líderes usando role_id directamente
        $leaders = collect([]);
        $totalLeaders = 0;
        
        // Primero intentar encontrar el rol de líder
        $leaderRole = null;
        $roleNames = ['leader', 'Leader', 'líder', 'Líder', 'lider', 'LEADER'];
        
        foreach ($roleNames as $roleName) {
            try {
                $role = \App\Models\Role::where('name', $roleName)->first();
                if ($role) {
                    $leaderRole = $role;
                    break;
                }
            } catch (\Exception $e) {
                continue;
            }
        }
        
        // Si encontramos el rol, buscar usuarios con ese role_id
        if ($leaderRole) {
            try {
                $leaders = User::where('role_id', $leaderRole->id)
                    ->with(['role'])
                    ->get();
                $totalLeaders = $leaders->count();
            } catch (\Exception $e) {
                // Si falla, intentar con la relación
                $leaders = User::whereHas('role', function($query) use ($leaderRole) {
                    $query->where('id', $leaderRole->id);
                })->with(['role'])->get();
                $totalLeaders = $leaders->count();
            }
        }
        
        // Si no encontramos líderes, intentar búsqueda alternativa
        if ($totalLeaders === 0) {
            try {
                $leaders = User::where(function($query) {
                    $query->where('name', 'LIKE', '%leader%')
                          ->orWhere('name', 'LIKE', '%líder%')
                          ->orWhere('email', 'LIKE', '%leader%');
                })->with(['role'])->get();
                
                $totalLeaders = $leaders->count();
            } catch (\Exception $e) {
                // Última opción: buscar por role_id común de líderes
                try {
                    // Asumir que los líderes tienen role_id = 2 o 3 (ajustar según tu BD)
                    $leaders = User::whereIn('role_id', [2, 3, 4])
                        ->with(['role'])
                        ->get();
                    $totalLeaders = $leaders->count();
                } catch (\Exception $e) {
                    $totalLeaders = 0;
                }
            }
        }
        
        $leadersData = collect([]);
        
        if ($totalLeaders > 0) {
            $leadersData = $leaders->map(function($leader) use ($dateFrom, $dateTo) {
                try {
                    $activeFriendships = 0;
                    try {
                        $activeFriendships = Friendship::where(function($query) use ($leader) {
                            $query->where('buddy_leader_id', $leader->id)
                                  ->orWhere('peer_buddy_leader_id', $leader->id);
                        })->where('status', 'Emparejado')->count();
                    } catch (\Exception $e) {
                        $activeFriendships = 0;
                    }
                    
                    $seguimientos = 0;
                    try {
                        $friendshipIds = Friendship::where(function($query) use ($leader) {
                            $query->where('buddy_leader_id', $leader->id)
                                  ->orWhere('peer_buddy_leader_id', $leader->id);
                        })->pluck('id');
                        
                        if ($friendshipIds->count() > 0) {
                            $seguimientos = \App\Models\FriendshipFollowUp::whereIn('friendship_id', $friendshipIds)
                                ->whereBetween('created_at', [$dateFrom, $dateTo])
                                ->count();
                        }
                    } catch (\Exception $e) {
                        $seguimientos = 0;
                    }
                    
                    $reportesMensuales = 0;
                    try {
                        $friendshipIds = Friendship::where(function($query) use ($leader) {
                            $query->where('buddy_leader_id', $leader->id)
                                  ->orWhere('peer_buddy_leader_id', $leader->id);
                        })->pluck('id');
                        
                        if ($friendshipIds->count() > 0) {
                            $reportesMensuales = \App\Models\MonthlyMonitoringReport::whereIn('friendship_id', $friendshipIds)
                                ->whereBetween('created_at', [$dateFrom, $dateTo])
                                ->count();
                        }
                    } catch (\Exception $e) {
                        $reportesMensuales = 0;
                    }
                    
                    return [
                        'nombre' => $leader->name ?? 'N/A',
                        'email' => $leader->email ?? 'N/A',
                        'rol' => optional($leader->role)->name ?? 'N/A',
                        'ciudad' => $leader->city ?? 'N/A',
                        'amistades_activas' => $activeFriendships,
                        'seguimientos_realizados' => $seguimientos,
                        'reportes_mensuales' => $reportesMensuales,
                        'estado' => $activeFriendships > 0 ? 'Activo' : 'Inactivo'
                    ];
                    
                } catch (\Exception $e) {
                    return [
                        'nombre' => $leader->name ?? 'Error',
                        'email' => $leader->email ?? 'Error',
                        'rol' => 'Error',
                        'ciudad' => 'Error',
                        'amistades_activas' => 0,
                        'seguimientos_realizados' => 0,
                        'reportes_mensuales' => 0,
                        'estado' => 'Error'
                    ];
                }
            });
        }
        
        // Obtener datos de seguimientos con nombres corregidos
        $followupsData = collect([]);
        try {
            $followupsData = $this->getFollowUpsList($dateFrom, $dateTo);
        } catch (\Exception $e) {
            // Manejo de error
        }
        
        // Obtener datos de reportes mensuales con nombres corregidos
        $monthlyReportsData = collect([]);
        try {
            $monthlyReportsData = $this->getMonthlyReportsList($dateFrom, $dateTo);
        } catch (\Exception $e) {
            // Manejo de error
        }
        
        // Obtener datos de asistencia con nombres corregidos
        $attendanceData = collect([]);
        try {
            $attendanceData = $this->getAttendanceList($dateFrom, $dateTo);
        } catch (\Exception $e) {
            // Manejo de error
        }
        
        $lideres_activos = $leadersData->where('estado', 'Activo')->count();
        $porcentaje_activos = $totalLeaders > 0 ? round(($lideres_activos / $totalLeaders) * 100, 1) : 0;
        
        $result = [
            'title' => 'REPORTE DE LIDERAZGO',
            'period' => $dateFrom->format('d/m/Y') . ' - ' . $dateTo->format('d/m/Y'),
            'generated_at' => Carbon::now('America/La_Paz')->format('d/m/Y H:i:s'),
            'statistics' => [
                // 'total_lideres' => $totalLeaders,
                // 'lideres_activos' => $lideres_activos,
                // 'porcentaje_activos' => $porcentaje_activos,
                'total_seguimientos' => $followupsData->count(),
                'total_reportes_mensuales' => $monthlyReportsData->count(),
                'total_asistencias' => $attendanceData->count()
            ],
            'leaders' => $leadersData->toArray(),
            'followups' => $followupsData->toArray(),
            'monthly_reports' => $monthlyReportsData->toArray(),
            'attendance' => $attendanceData->toArray()
        ];
        
        return $result;
        
    } catch (\Exception $e) {
        return [
            'title' => 'REPORTE DE LIDERAZGO (ERROR)',
            'period' => $dateFrom->format('d/m/Y') . ' - ' . $dateTo->format('d/m/Y'),
            'generated_at' => Carbon::now('America/La_Paz')->format('d/m/Y H:i:s'),
            'error' => $e->getMessage(),
            'statistics' => [
                'total_lideres' => 0,
                'lideres_activos' => 0,
                'porcentaje_activos' => 0,
                'total_seguimientos' => 0,
                'total_reportes_mensuales' => 0,
                'total_asistencias' => 0
            ],
            'leaders' => [],
            'followups' => [],
            'monthly_reports' => [],
            'attendance' => []
        ];
    }
}

    private function getAttendanceList($dateFrom, $dateTo)
{
    try {
        return \App\Models\FriendshipAttendance::whereBetween('created_at', [$dateFrom, $dateTo])
            ->with(['friendship.buddy', 'friendship.peerBuddy'])
            ->get()
            ->map(function($attendance) {
                try {
                    $friendship = $attendance->friendship;
                    $buddyName = 'N/A';
                    $peerBuddyName = 'N/A';
                    
                    if ($friendship) {
                        // Usar first_name y last_name en lugar de name
                        if ($friendship->buddy) {
                            $buddyName = trim(($friendship->buddy->first_name ?? '') . ' ' . ($friendship->buddy->last_name ?? ''));
                            if (empty($buddyName)) {
                                $buddyName = $friendship->buddy->name ?? 'N/A';
                            }
                        }
                        
                        if ($friendship->peerBuddy) {
                            $peerBuddyName = trim(($friendship->peerBuddy->first_name ?? '') . ' ' . ($friendship->peerBuddy->last_name ?? ''));
                            if (empty($peerBuddyName)) {
                                $peerBuddyName = $friendship->peerBuddy->name ?? 'N/A';
                            }
                        }
                    }
                    
                    return [
                        'friendship_name' => $buddyName . ' - ' . $peerBuddyName,
                        'date' => $attendance->date ? Carbon::parse($attendance->date)->format('d/m/Y') : 'N/A',
                        'buddy_attended' => $attendance->buddy_attended ?? false,
                        'peer_buddy_attended' => $attendance->peer_buddy_attended ?? false,
                        'notes' => $attendance->notes ?? 'Sin notas'
                    ];
                } catch (\Exception $e) {
                    return [
                        'friendship_name' => 'Error',
                        'date' => 'Error',
                        'buddy_attended' => false,
                        'peer_buddy_attended' => false,
                        'notes' => 'Error'
                    ];
                }
            });
    } catch (\Exception $e) {
        return collect([]);
    }
}

private function calculateAverageProgress($followup)
{
    $scores = array_filter([
        $followup->buddy_progress,
        $followup->peer_buddy_progress,
        $followup->relationship_quality
    ]);
    
    return count($scores) > 0 ? round(array_sum($scores) / count($scores), 1) : 0;
}
    private function getFriendshipStatusLabel($status)
    {
        $labels = [
            'active' => 'Activa',
            'completed' => 'Completada',
            'paused' => 'Pausada',
            'cancelled' => 'Cancelada'
        ];
        
        return $labels[$status] ?? ucfirst($status);
    }


private function generatePDFHTML($data, $reportType, $dateFrom, $dateTo)
    {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>' . htmlspecialchars($data['title']) . '</title>
            <style>
                body { 
                    font-family: Arial, sans-serif; 
                    margin: 20px; 
                    color: #333;
                }
                .header { 
                    text-align: center; 
                    margin-bottom: 30px; 
                    border-bottom: 2px solid #007bff;
                    padding-bottom: 20px;
                }
                .period { 
                    color: #666; 
                    margin-bottom: 20px; 
                    font-style: italic;
                }
                .statistics { 
                    margin-bottom: 30px; 
                }
                .stat-item { 
                    display: inline-block; 
                    margin: 10px; 
                    padding: 15px; 
                    border: 1px solid #ddd; 
                    border-radius: 5px;
                    background-color: #f8f9fa;
                }
                table { 
                    width: 100%; 
                    border-collapse: collapse; 
                    margin-bottom: 20px; 
                }
                th, td { 
                    border: 1px solid #ddd; 
                    padding: 12px; 
                    text-align: left; 
                }
                th { 
                    background-color: #007bff; 
                    color: white;
                    font-weight: bold;
                }
                .footer { 
                    margin-top: 30px; 
                    text-align: center; 
                    color: #666; 
                    font-size: 12px; 
                    border-top: 1px solid #ddd;
                    padding-top: 20px;
                }
                h1 { color: #007bff; }
                h2 { color: #333; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>' . htmlspecialchars($data['title']) . '</h1>
                <div class="period">Período: ' . $dateFrom->format('d/m/Y') . ' - ' . $dateTo->format('d/m/Y') . '</div>
            </div>
            
            <div class="statistics">
                <h2>Estadísticas Generales</h2>';
        
        // Agregar estadísticas
        if (isset($data['statistics'])) {
            foreach ($data['statistics'] as $key => $value) {
                $label = str_replace('_', ' ', ucfirst($key));
                $html .= '<div class="stat-item"><strong>' . htmlspecialchars($label) . ':</strong> ' . htmlspecialchars($value) . '</div>';
            }
        }
        
        $html .= '</div>';
        
        // Agregar tabla de datos si existe
        if (isset($data['activities']) && count($data['activities']) > 0) {
            $html .= '<h2>Listado de Actividades</h2><table><tr><th>Título</th><th>Fecha</th><th>Tipo</th><th>Estado</th><th>Ubicación</th></tr>';
            foreach ($data['activities'] as $activity) {
                $html .= '<tr>
                    <td>' . htmlspecialchars($activity['title'] ?? '') . '</td>
                    <td>' . htmlspecialchars($activity['formatted_date'] ?? '') . '</td>
                    <td>' . htmlspecialchars($activity['type_label'] ?? '') . '</td>
                    <td>' . htmlspecialchars($activity['status_label'] ?? '') . '</td>
                    <td>' . htmlspecialchars($activity['location'] ?? '') . '</td>
                </tr>';
            }
            $html .= '</table>';
        }
        
        if (isset($data['friendships']) && count($data['friendships']) > 0) {
            $html .= '<h2>Listado de Amistades</h2><table><tr><th>Buddy</th><th>Peer Buddy</th><th>Fecha Inicio</th><th>Estado</th><th>Líder 1</th><th>Líder 2</th></tr>';
            foreach ($data['friendships'] as $friendship) {
                $html .= '<tr>
                    <td>' . htmlspecialchars($friendship['buddy_name'] ?? '') . '</td>
                    <td>' . htmlspecialchars($friendship['peer_buddy_name'] ?? '') . '</td>
                    <td>' . htmlspecialchars($friendship['formatted_start_date'] ?? '') . '</td>
                    <td>' . htmlspecialchars($friendship['status'] ?? '') . '</td>
                    <td>' . htmlspecialchars($friendship['leader1_name'] ?? '') . '</td>
                    <td>' . htmlspecialchars($friendship['leader2_name'] ?? '') . '</td>
                </tr>';
            }
            $html .= '</table>';
        }
        
        $html .= '
            <div class="footer">
                <p>Generado el ' . now()->format('d/m/Y H:i:s') . '</p>
                <p>Sistema de Gestión - Programa de Amistad</p>
            </div>
        </body>
        </html>';
        
        return $html;
    }
    private function generatePlainTextContent($data, $reportType)
    {
        $lines = [];
        
        // Encabezado del reporte
        $lines[] = str_repeat('=', 80);
        $lines[] = strtoupper($data['title']);
        $lines[] = str_repeat('=', 80);
        $lines[] = 'PERÍODO: ' . $data['period'];
        $lines[] = 'GENERADO: ' . $data['generated_at'];
        $lines[] = str_repeat('=', 80);
        $lines[] = '';
        
        // Estadísticas generales
        if (isset($data['statistics']) && !empty($data['statistics'])) {
            $lines[] = 'ESTADÍSTICAS GENERALES:';
            $lines[] = str_repeat('-', 40);
            
            foreach ($data['statistics'] as $key => $value) {
                $label = $this->formatStatisticLabel($key);
                $lines[] = sprintf('%-30s: %s', $label, $value);
            }
            $lines[] = str_repeat('-', 40);
            $lines[] = '';
        }
        
        // Contenido específico según el tipo de reporte
        switch ($reportType) {
            case 'general':
                $lines = array_merge($lines, $this->generateGeneralReportContent($data));
                break;
            case 'actividades':
                $lines = array_merge($lines, $this->generateActivitiesReportContent($data));
                break;
            case 'amistades':
                $lines = array_merge($lines, $this->generateFriendshipsReportContent($data));
                break;
            case 'liderazgo':
                $lines = array_merge($lines, $this->generateLeadershipReportContent($data));
                break;
        }
        
        // Pie del reporte
        $lines[] = '';
        $lines[] = str_repeat('=', 80);
        $lines[] = 'FIN DEL REPORTE';
        $lines[] = str_repeat('=', 80);
        
        return implode("\n", $lines);
    }

    private function generateGeneralReportContent($data)
    {
        $lines = [];
        
        // Resumen de actividades
        if (isset($data['activities']) && count($data['activities']) > 0) {
            $lines[] = 'RESUMEN DE ACTIVIDADES RECIENTES:';
            $lines[] = str_repeat('-', 40);
            
            foreach (array_slice($data['activities'], 0, 5) as $activity) {
                $lines[] = sprintf('• %s (%s)', 
                    $activity['title'] ?? 'Sin título',
                    $activity['formatted_date'] ?? 'Sin fecha'
                );
            }
            $lines[] = '';
        }
        
        // Resumen de amistades
        if (isset($data['friendships']) && count($data['friendships']) > 0) {
            $lines[] = 'RESUMEN DE AMISTADES RECIENTES:';
            $lines[] = str_repeat('-', 40);
            
            foreach (array_slice($data['friendships'], 0, 5) as $friendship) {
                $lines[] = sprintf('• %s - %s (%s)', 
                    $friendship['buddy_name'] ?? 'N/A',
                    $friendship['peer_buddy_name'] ?? 'N/A',
                    $friendship['formatted_start_date'] ?? 'Sin fecha'
                );
            }
            $lines[] = '';
        }
        
        return $lines;
    }

    private function generateActivitiesReportContent($data)
    {
        $lines = [];
        
        if (isset($data['activities']) && count($data['activities']) > 0) {
            $lines[] = 'LISTADO DETALLADO DE ACTIVIDADES:';
            $lines[] = str_repeat('-', 80);
            
            foreach ($data['activities'] as $index => $activity) {
                $lines[] = sprintf('%d. ACTIVIDAD:', $index + 1);
                $lines[] = sprintf('   Título: %s', $activity['title'] ?? 'Sin título');
                $lines[] = sprintf('   Fecha: %s', $activity['formatted_date'] ?? 'Sin fecha');
                $lines[] = sprintf('   Tipo: %s', $activity['type_label'] ?? 'Sin tipo');
                $lines[] = sprintf('   Estado: %s', $activity['status_label'] ?? 'Sin estado');
                $lines[] = sprintf('   Ubicación: %s', $activity['location'] ?? 'Sin ubicación');
                if (!empty($activity['description'])) {
                    $lines[] = sprintf('   Descripción: %s', $activity['description']);
                }
                // $lines[] = sprintf('   Participantes: %d', $activity['participants_count'] ?? 0);
                $lines[] = str_repeat('.', 40);
                $lines[] = '';
            }
        } else {
            $lines[] = 'No se encontraron actividades en el período seleccionado.';
            $lines[] = '';
        }
        
        return $lines;
    }

    private function generateFriendshipsReportContent($data)
    {
        $lines = [];
        
        if (isset($data['friendships']) && count($data['friendships']) > 0) {
            $lines[] = 'LISTADO DETALLADO DE AMISTADES:';
            $lines[] = str_repeat('-', 80);
            
            foreach ($data['friendships'] as $index => $friendship) {
                $lines[] = sprintf('%d. AMISTAD:', $index + 1);
                $lines[] = sprintf('   Buddy: %s', $friendship['buddy_name'] ?? 'N/A');
                $lines[] = sprintf('   Peer Buddy: %s', $friendship['peer_buddy_name'] ?? 'N/A');
                $lines[] = sprintf('   Fecha de Inicio: %s', $friendship['formatted_start_date'] ?? 'Sin fecha');
                $lines[] = sprintf('   Estado: %s', $this->getFriendshipStatusLabel($friendship['status'] ?? ''));
                $lines[] = sprintf('   Duración (días): %d', $friendship['duration_days'] ?? 0);
                
                if (!empty($friendship['notes'])) {
                    $lines[] = sprintf('   Notas: %s', $friendship['notes']);
                }
                
                // Información adicional del buddy
                if (isset($friendship['buddy_data'])) {
                    $lines[] = '   --- INFORMACIÓN DEL BUDDY ---';
                    $lines[] = sprintf('   Discapacidad: %s', $friendship['buddy_data']['disability'] ?? 'No especificada');
                    $lines[] = sprintf('   Edad: %s', $friendship['buddy_data']['age'] ?? 'No especificada');
                    $lines[] = sprintf('   Teléfono: %s', $friendship['buddy_data']['phone'] ?? 'No especificado');
                }
                
                // Información adicional del peer buddy
                if (isset($friendship['peer_buddy_data'])) {
                    $lines[] = '   --- INFORMACIÓN DEL PEER BUDDY ---';
                    $lines[] = sprintf('   Edad: %s', $friendship['peer_buddy_data']['age'] ?? 'No especificada');
                    $lines[] = sprintf('   Teléfono: %s', $friendship['peer_buddy_data']['phone'] ?? 'No especificado');
                }
                
                $lines[] = str_repeat('.', 40);
                $lines[] = '';
            }
        } else {
            $lines[] = 'No se encontraron amistades en el período seleccionado.';
            $lines[] = '';
        }
        
        // Agregar evaluaciones si existen
        if (isset($data['evaluations']) && count($data['evaluations']) > 0) {
            $lines[] = 'EVALUACIONES REALIZADAS:';
            $lines[] = str_repeat('-', 40);
            
            foreach ($data['evaluations'] as $evaluation) {
                $lines[] = sprintf('• %s - Puntuación: %s (%s)', 
                    $evaluation['friendship_name'] ?? 'N/A',
                    $evaluation['score'] ?? 'N/A',
                    $evaluation['date'] ?? 'Sin fecha'
                );
            }
            $lines[] = '';
        }
        
        return $lines;
    }

    private function generateLeadershipReportContent($data)
{
    $lines = [];
    
    
    
    // Listado de seguimientos
    if (isset($data['followups']) && count($data['followups']) > 0) {
        $lines[] = '';
        $lines[] = 'LISTADO DE SEGUIMIENTOS:';
        $lines[] = str_repeat('-', 80);
        
        foreach ($data['followups'] as $index => $followup) {
            $lines[] = sprintf('%d. SEGUIMIENTO:', $index + 1);
            $lines[] = sprintf('   Amistad: %s', $followup['friendship_name'] ?? 'N/A');
            $lines[] = sprintf('   Fecha: %s', $followup['date'] ?? 'N/A');
            $lines[] = sprintf('   Progreso Buddy: %s/10', $followup['buddy_progress'] ?? 'N/A');
            $lines[] = sprintf('   Progreso Peer Buddy: %s/10', $followup['peer_buddy_progress'] ?? 'N/A');
            $lines[] = sprintf('   Calidad Relación: %s/10', $followup['relationship_quality'] ?? 'N/A');
            $lines[] = sprintf('   Promedio: %.1f/10', $followup['average_progress'] ?? 0);
            $lines[] = sprintf('   Logros: %s', $followup['goals_achieved'] ?? 'Sin especificar');
            $lines[] = sprintf('   Desafíos: %s', $followup['challenges_faced'] ?? 'Sin especificar');
            $lines[] = str_repeat('.', 40);
            $lines[] = '';
        }
    } else {
        $lines[] = '';
        $lines[] = 'No se encontraron seguimientos registrados en el período.';
        $lines[] = '';
    }
    
    // Listado de reportes mensuales
    if (isset($data['monthly_reports']) && count($data['monthly_reports']) > 0) {
        $lines[] = '';
        $lines[] = 'LISTADO DE REPORTES MENSUALES:';
        $lines[] = str_repeat('-', 80);
        
        foreach ($data['monthly_reports'] as $index => $report) {
            $lines[] = sprintf('%d. REPORTE MENSUAL:', $index + 1);
            $lines[] = sprintf('   Monitor: %s', $report['monitor_name'] ?? 'N/A');
            $lines[] = sprintf('   Período: %s', $report['monitoring_period'] ?? 'N/A');
            $lines[] = sprintf('   Amistad: %s', $report['friendship_name'] ?? 'N/A');
            $lines[] = sprintf('   Evaluación General: %s', $report['general_evaluation'] ?? 'N/A');
            $lines[] = sprintf('   Frecuencia Reuniones: %s', $report['meeting_frequency'] ?? 'N/A');
            $lines[] = sprintf('   Participación Tutor: %s', $report['tutor_participation'] ?? 'N/A');
            $lines[] = sprintf('   Participación Líder: %s', $report['leader_participation'] ?? 'N/A');
            $lines[] = sprintf('   Satisfacción Tutor: %s', $report['tutor_satisfaction'] ?? 'N/A');
            $lines[] = sprintf('   Satisfacción Líder: %s', $report['leader_satisfaction'] ?? 'N/A');
            $lines[] = sprintf('   Requiere Atención: %s', $report['requires_attention'] ?? 'N/A');
            $lines[] = str_repeat('.', 40);
            $lines[] = '';
        }
    } else {
        $lines[] = '';
        $lines[] = 'No se encontraron reportes mensuales en el período.';
        $lines[] = '';
    }
    
    // Listado de asistencia
    if (isset($data['attendance']) && count($data['attendance']) > 0) {
        $lines[] = '';
        $lines[] = 'LISTADO DE ASISTENCIA:';
        $lines[] = str_repeat('-', 80);
        
        foreach ($data['attendance'] as $index => $attend) {
            $lines[] = sprintf('%d. ASISTENCIA:', $index + 1);
            $lines[] = sprintf('   Amistad: %s', $attend['friendship_name'] ?? 'N/A');
            $lines[] = sprintf('   Fecha: %s', $attend['date'] ?? 'N/A');
            $lines[] = sprintf('   Buddy asistió: %s', $attend['buddy_attended'] ? 'Sí' : 'No');
            $lines[] = sprintf('   Peer Buddy asistió: %s', $attend['peer_buddy_attended'] ? 'Sí' : 'No');
            $lines[] = sprintf('   Notas: %s', $attend['notes'] ?? 'Sin notas');
            $lines[] = str_repeat('.', 40);
            $lines[] = '';
        }
    } else {
        $lines[] = '';
        $lines[] = 'No se encontraron registros de asistencia en el período.';
        $lines[] = '';
    }
    
    return $lines;
}

    private function formatStatisticLabel($key)
    {
        $labels = [
            'total_actividades' => 'Total de Actividades',
            'total_amistades' => 'Total de Amistades',
            'amistades_activas' => 'Amistades Activas',
            'total_usuarios' => 'Total de Usuarios',
            'tasa_exito' => 'Tasa de Éxito (%)',
            'actividades_completadas' => 'Actividades Completadas',
            'actividades_pendientes' => 'Actividades Pendientes',
            'porcentaje_completadas' => 'Porcentaje Completadas (%)',
            'amistades_completadas' => 'Amistades Completadas',
            'porcentaje_exitosas' => 'Porcentaje Exitosas (%)',
            'total_lideres' => 'Total de Líderes',
            'lideres_activos' => 'Líderes Activos',
            'porcentaje_activos' => 'Porcentaje Activos (%)'
        ];
        
        return $labels[$key] ?? ucfirst(str_replace('_', ' ', $key));
    }

    private function getEvaluationsList($dateFrom, $dateTo)
{
    try {
        // Verificar si existe el modelo FriendshipEvaluation
        if (!class_exists('App\\Models\\FriendshipEvaluation')) {
            return collect([]); // Retorna colección vacía si no existe
        }
        
        return \App\Models\FriendshipEvaluation::whereBetween('created_at', [$dateFrom, $dateTo])
            ->with(['friendship.buddy', 'friendship.peerBuddy'])
            ->get()
            ->map(function($evaluation) {
                return [
                    'friendship_name' => $evaluation->friendship ? 
                        ($evaluation->friendship->buddy->name ?? 'N/A') . ' - ' . ($evaluation->friendship->peerBuddy->name ?? 'N/A') :
                        'N/A',
                    'date' => Carbon::parse($evaluation->created_at)->format('d/m/Y'),
                    'score' => $evaluation->score ?? 'N/A',
                    'comments' => $evaluation->comments ?? 'Sin comentarios'
                ];
            });
    } catch (\Exception $e) {
        Log::error('Error obteniendo evaluaciones: ' . $e->getMessage());
        return collect([]);
    }
}

private function getFollowUpsList($dateFrom, $dateTo)
{
    try {
        return \App\Models\FriendshipFollowUp::whereBetween('created_at', [$dateFrom, $dateTo])
            ->with(['friendship.buddy', 'friendship.peerBuddy'])
            ->get()
            ->map(function($followup) {
                try {
                    $friendship = $followup->friendship;
                    $buddyName = 'N/A';
                    $peerBuddyName = 'N/A';
                    
                    if ($friendship) {
                        // Usar first_name y last_name en lugar de name
                        if ($friendship->buddy) {
                            $buddyName = trim(($friendship->buddy->first_name ?? '') . ' ' . ($friendship->buddy->last_name ?? ''));
                            if (empty($buddyName)) {
                                $buddyName = $friendship->buddy->name ?? 'N/A';
                            }
                        }
                        
                        if ($friendship->peerBuddy) {
                            $peerBuddyName = trim(($friendship->peerBuddy->first_name ?? '') . ' ' . ($friendship->peerBuddy->last_name ?? ''));
                            if (empty($peerBuddyName)) {
                                $peerBuddyName = $friendship->peerBuddy->name ?? 'N/A';
                            }
                        }
                    }
                    
                    return [
                        'friendship_name' => $buddyName . ' - ' . $peerBuddyName,
                        'date' => $followup->created_at ? Carbon::parse($followup->created_at)->format('d/m/Y') : 'N/A',
                        'buddy_progress' => $followup->buddy_progress ?? 'N/A',
                        'peer_buddy_progress' => $followup->peer_buddy_progress ?? 'N/A',
                        'relationship_quality' => $followup->relationship_quality ?? 'N/A',
                        'average_progress' => $this->calculateAverageProgress($followup),
                        'goals_achieved' => $followup->goals_achieved ?? 'Sin especificar',
                        'challenges_faced' => $followup->challenges_faced ?? 'Sin especificar',
                        'notes' => $followup->notes ?? 'Sin notas'
                    ];
                } catch (\Exception $e) {
                    return [
                        'friendship_name' => 'Error',
                        'date' => 'Error',
                        'buddy_progress' => 'Error',
                        'peer_buddy_progress' => 'Error',
                        'relationship_quality' => 'Error',
                        'average_progress' => 0,
                        'goals_achieved' => 'Error',
                        'challenges_faced' => 'Error',
                        'notes' => 'Error'
                    ];
                }
            });
    } catch (\Exception $e) {
        return collect([]);
    }
}


private function getMonthlyReportsList($dateFrom, $dateTo)
{
    try {
        return \App\Models\MonthlyMonitoringReport::whereBetween('created_at', [$dateFrom, $dateTo])
            ->with(['friendship.buddy', 'friendship.peerBuddy'])
            ->get()
            ->map(function($report) {
                try {
                    $friendship = $report->friendship;
                    $buddyName = 'N/A';
                    $peerBuddyName = 'N/A';
                    
                    if ($friendship) {
                        // Usar first_name y last_name en lugar de name
                        if ($friendship->buddy) {
                            $buddyName = trim(($friendship->buddy->first_name ?? '') . ' ' . ($friendship->buddy->last_name ?? ''));
                            if (empty($buddyName)) {
                                $buddyName = $friendship->buddy->name ?? 'N/A';
                            }
                        }
                        
                        if ($friendship->peerBuddy) {
                            $peerBuddyName = trim(($friendship->peerBuddy->first_name ?? '') . ' ' . ($friendship->peerBuddy->last_name ?? ''));
                            if (empty($peerBuddyName)) {
                                $peerBuddyName = $friendship->peerBuddy->name ?? 'N/A';
                            }
                        }
                    }
                    
                    return [
                        'monitor_name' => $report->monitor_name ?? 'N/A',
                        'monitoring_period' => $report->monitoring_period ?? 'N/A',
                        'friendship_name' => $buddyName . ' - ' . $peerBuddyName,
                        'date' => $report->created_at ? Carbon::parse($report->created_at)->format('d/m/Y') : 'N/A',
                        'general_evaluation' => $report->general_evaluation ?? 'N/A',
                        'meeting_frequency' => $report->meeting_frequency ?? 'N/A',
                        'tutor_participation' => $report->tutor_participation ?? 'N/A',
                        'leader_participation' => $report->leader_participation ?? 'N/A',
                        'tutor_satisfaction' => $report->tutor_satisfaction ?? 'N/A',
                        'leader_satisfaction' => $report->leader_satisfaction ?? 'N/A',
                        'requires_attention' => $report->requires_attention ?? 'N/A',
                        'specific_observations' => $report->specific_observations ?? 'Sin observaciones'
                    ];
                } catch (\Exception $e) {
                    return [
                        'monitor_name' => 'Error',
                        'monitoring_period' => 'Error',
                        'friendship_name' => 'Error',
                        'date' => 'Error',
                        'general_evaluation' => 'Error',
                        'meeting_frequency' => 'Error',
                        'tutor_participation' => 'Error',
                        'leader_participation' => 'Error',
                        'tutor_satisfaction' => 'Error',
                        'leader_satisfaction' => 'Error',
                        'requires_attention' => 'Error',
                        'specific_observations' => 'Error'
                    ];
                }
            });
    } catch (\Exception $e) {
        return collect([]);
    }
}
}