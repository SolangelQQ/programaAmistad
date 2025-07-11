<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;




class ActivityController extends Controller
{
    public function index()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        $activities = Activity::forMonth($currentYear, $currentMonth)->get();
        $upcomingActivities = Activity::upcoming()->take(5)->get();
        
        return view('activities.index', compact('activities', 'upcomingActivities', 'currentMonth', 'currentYear'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'nullable',
            'location' => 'required|string|max:255',
            'type' => 'required|in:recreational,educational,cultural,sports,social',
         
        ]);

        $activity = Activity::create($validated);

        return response()->json([
            'success' => true,
            'activity' => $activity,
            'message' => 'Actividad creada exitosamente'
        ]);
    }
    public function update(Request $request, Activity $activity)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'nullable',
            'location' => 'required|string|max:255',
            'type' => 'required|in:recreational,educational,cultural,sports,social',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
           
        ]);

        $activity->update($validated);

        return response()->json([
            'success' => true,
            'activity' => $activity,
            'message' => 'Actividad actualizada exitosamente'
        ]);
    }

    public function show(Activity $activity)
    {
        // URLs para las fotos
        $activity->photo_urls = collect($activity->photos ?? [])->map(function($photo) {
            return [
                'path' => $photo,
                'url' => asset('storage/' . $photo)
            ];
        });
        
        return response()->json($activity);
    }
    public function destroy(Activity $activity)
    {
        // Eliminar fotos asociadas
        if ($activity->photos) {
            foreach ($activity->photos as $photo) {
                Storage::delete($photo);
            }
        }

        $activity->delete();

        return response()->json([
            'success' => true,
            'message' => 'Actividad eliminada exitosamente'
        ]);
    }
    public function uploadPhotos(Request $request, Activity $activity)
    {
        $request->validate([
            'photos.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $uploadedPhotos = [];
        $existingPhotos = $activity->photos ?? [];

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('activities/photos', 'public');
                $uploadedPhotos[] = $path;
            }
        }

        $allPhotos = array_merge($existingPhotos, $uploadedPhotos);
        $activity->update(['photos' => $allPhotos]);

        return response()->json([
            'success' => true,
            'photos' => $uploadedPhotos,
            'message' => 'Fotos subidas exitosamente'
        ]);
    }
    public function deletePhoto(Request $request, Activity $activity)
    {
        $photoPath = $request->input('photo_path');
        $photos = $activity->photos ?? [];
        
        if (($key = array_search($photoPath, $photos)) !== false) {
            unset($photos[$key]);
            
            // Eliminar archivo físico
            if (Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }
            
            $activity->update(['photos' => array_values($photos)]);
            
            return response()->json([
                'success' => true,
                'message' => 'Foto eliminada exitosamente'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Foto no encontrada'
        ], 404);
    }

    public function getCalendarData(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        $type = $request->input('type');
        $status = $request->input('status');
        $dateStart = $request->input('dateStart');
        $dateEnd = $request->input('dateEnd');
        
        $query = Activity::forMonth($year, $month);
        
        if ($type) {
            $query->where('type', $type);
        }
        
        if ($status) {
            $query->where('status', $status);
        } 
        
        // Filtros de rango de fechas 
        if ($dateStart) {
            $query->where('date', '>=', $dateStart);
        }
        
        if ($dateEnd) {
            $query->where('date', '<=', $dateEnd);
        }
        
        $activities = $query->get()
            ->groupBy(function($activity) {
                return $activity->date->day;
            });

        $activitiesWithTypes = [];
        foreach ($activities as $day => $dayActivities) {
            $activitiesWithTypes[$day] = [
                'activities' => $dayActivities,
                'types' => $dayActivities->pluck('type')->unique()->toArray()
            ];
        }

        return response()->json($activitiesWithTypes);
    }

    public function getActivitiesByDate(Request $request)
    {
        $date = $request->input('date');
        $type = $request->input('type');
        $status = $request->input('status');
        
        $query = Activity::where('date', $date);
        
        // Aplicar filtros
        if ($type) {
            $query->where('type', $type);
        }
        
        if ($status) {
            $query->where('status', $status);
        } 
        
        $activities = $query->orderBy('start_time')->get();

        return response()->json($activities);
    }

    public function apiIndex(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');
        $type = $request->input('type');
        $status = $request->input('status');
        $dateStart = $request->input('dateStart');
        $dateEnd = $request->input('dateEnd');
        
        $query = Activity::query();
        
        if ($month && $year) {
            $query->whereMonth('date', $month)
                  ->whereYear('date', $year);
        }

        if ($type) {
            $query->where('type', $type);
        }

        if ($status) {
            $query->where('status', $status);
        } 

        if ($dateStart) {
            $query->where('date', '>=', $dateStart);
        }
        
        if ($dateEnd) {
            $query->where('date', '<=', $dateEnd);
        }
        
        $activities = $query->orderBy('date')
                           ->orderBy('start_time')
                           ->get();
        
        return response()->json($activities);
    }
    public function getUpcomingActivities()
    {
        
        try {
            $activities = Activity::where('date', '>=', now()->toDateString())
                
                ->orderBy('date', 'asc')
                ->orderBy('start_time', 'asc')
                ->limit(5)
                ->get()
                ->map(function ($activity) {
                    return [
                        'id' => $activity->id,
                        'title' => $activity->title,
                        'description' => $activity->description,
                        'date' => $activity->date,
                        'start_time' => $activity->start_time,
                        'end_time' => $activity->end_time,
                        'type' => $activity->type,
                        'status' => $activity->status,
                        'location' => $activity->location,

                        'formatted_date' => \Carbon\Carbon::parse($activity->date)->locale('es')->isoFormat('dddd, D [de] MMMM'),
                        'formatted_time' => \Carbon\Carbon::parse($activity->start_time)->format('H:i') . 
                                        ($activity->end_time ? ' - ' . \Carbon\Carbon::parse($activity->end_time)->format('H:i') : ''),
                    ];
                });

            return response()->json($activities);
            
        } catch (\Exception $e) {
            \Log::error('Error loading upcoming activities: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'error' => 'Error al cargar las actividades próximas',
                'message' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor'
            ], 500);
        }
    }
    public function upcoming()
    {
        $activities = Activity::where('date', '>=', now()->toDateString())
            ->orderBy('date', 'asc')
            ->orderBy('start_time', 'asc')
           // ->with(['participants']) // si tienes relaciones
            ->get()
            ->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'title' => $activity->title,
                    'description' => $activity->description,
                    'date' => $activity->date,
                    'start_time' => $activity->start_time,
                    'end_time' => $activity->end_time,
                    'location' => $activity->location,
                    'type' => $activity->type,
                    'status' => $activity->status, // Importante: incluir el status
               
                    'formatted_date' => $activity->date ? \Carbon\Carbon::parse($activity->date)->format('d/m/Y') : '',
                    'formatted_time' => $activity->start_time ? \Carbon\Carbon::parse($activity->start_time)->format('H:i') : '',
                ];
            });

        return response()->json($activities);
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
    
    // Estadísticas básicas usando datos reales
    $totalActivities = $activities->count();
    $completedActivities = $activities->where('status', 'completed')->count();
    $scheduledActivities = $activities->where('status', 'scheduled')->count();
    $cancelledActivities = $activities->where('status', 'cancelled')->count();
    $inProgressActivities = $activities->where('status', 'in_progress')->count();
    $completionRate = $totalActivities > 0 ? round(($completedActivities / $totalActivities) * 100) : 0;
    
    // Datos para gráficos
    $participationByType = $activities->groupBy('type')->map(function ($group, $type) {
        return [
            'type' => $type,
            'total' => $group->count()
        ];
    })->values();
    
    $statusComparison = $activities->groupBy('status')->map(function ($group, $status) {
        return [
            'status' => $status,
            'total' => $group->count()
        ];
    })->values();
    
    $monthlyTrend = $activities->groupBy(function ($activity) {
        return $activity->date ? $activity->date->format('Y-m') : 'Sin fecha';
    })->map(function ($group, $period) {
        return [
            'period' => $period,
            'total' => $group->count()
        ];
    })->values();
    
    // Actividades con datos formateados para la tabla
    $activitiesFormatted = $activities->map(function ($activity) {
        return [
            'id' => $activity->id,
            'title' => $activity->title,
            'description' => $activity->description,
            'date' => $activity->date,
            'formatted_date' => $activity->date ? $activity->date->format('d/m/Y') : 'Sin fecha',
            'start_time' => $activity->start_time,
            'end_time' => $activity->end_time,
            'location' => $activity->location,
            'type' => $activity->type,
            'type_label' => ucfirst($activity->type),
            'status' => $activity->status,
            'status_label' => ucfirst(str_replace('_', ' ', $activity->status)),
            'participants_count' => 0, // Si tienes relación con participantes, cambiar por $activity->participants->count()
            'duration' => $activity->duration ?? 'N/A'
        ];
    });
    
    // Actividades populares (si necesitas esta funcionalidad)
    $popularActivities = $activities->sortByDesc(function($activity) {
        // Si tienes relación con participantes, usar: return $activity->participants->count();
        return rand(1, 10); // Placeholder temporal
    })->take(3)->map(function($activity) {
        return [
            'id' => $activity->id,
            'title' => $activity->title,
            'type' => $activity->type,
            'type_label' => ucfirst($activity->type),
            'participants_count' => rand(1, 10) // Placeholder temporal
        ];
    })->values();
    
    return response()->json([
        'success' => true,
        'data' => [
            'statistics' => [
                'totalActivities' => $totalActivities,
                'completedActivities' => $completedActivities,
                'scheduledActivities' => $scheduledActivities,
                'cancelledActivities' => $cancelledActivities,
                'inProgressActivities' => $inProgressActivities,
                'completionRate' => $completionRate
            ],
            'charts' => [
                'participationByType' => $participationByType,
                'statusComparison' => $statusComparison,
                'monthlyTrend' => $monthlyTrend
            ],
            'activities' => $activitiesFormatted,
            'popular' => $popularActivities
        ]
    ]);
}


}