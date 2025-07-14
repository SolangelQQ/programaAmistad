<?php

namespace App\Http\Controllers;

use App\Models\Friendship;
use App\Models\User;
use App\Models\Buddy;
use App\Models\FollowUp; // Agregar import faltante
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Agregar import faltante
use Carbon\Carbon; // Agregar import faltante
use Illuminate\Support\Facades\DB;
use App\Models\FriendshipAttendance;

class FriendshipController extends Controller
{
    public function index()
    {
        $friendships = Friendship::with('buddy', 'peerBuddy')
            ->paginate(10);
        
        // Obtener buddies y peerbuddies disponibles para el formulario de creación
        $availableBuddies = Buddy::where('type', 'buddy')->orderBy('first_name')->get();
        $availablePeerBuddies = Buddy::where('type', 'peer_buddy')->orderBy('first_name')->get();
        
        // CORREGIDO: Obtener líderes desde la tabla users con roles específicos
        $availableBuddyLeaders = User::whereHas('role', function($query) {
            $query->where('name', 'Líder de Buddies');
        })->orderBy('name')->get();
            
        $availablePeerBuddyLeaders = User::whereHas('role', function($query) {
            $query->where('name', 'Líder de PeerBuddies');
        })->orderBy('name')->get();

        $buddies = Buddy::paginate(10);
        
        return view('friendships.index', compact('friendships', 'buddies', 'availableBuddies', 'availablePeerBuddies', 'availableBuddyLeaders',
            'availablePeerBuddyLeaders'));
    }

     public function filter(Request $request)
    {
        $query = Friendship::with(['buddy', 'peerBuddy']);
        
        if ($request->filled('status') && $request->status != 'Todos') {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('buddy_search')) {
            $query->whereHas('buddy', function($q) use ($request) {
                $q->where('first_name', 'like', '%'.$request->buddy_search.'%')
                  ->orWhere('last_name', 'like', '%'.$request->buddy_search.'%');
            });
        }
        
        if ($request->filled('peer_buddy_search')) {
            $query->whereHas('peerBuddy', function($q) use ($request) {
                $q->where('first_name', 'like', '%'.$request->peer_buddy_search.'%')
                  ->orWhere('last_name', 'like', '%'.$request->peer_buddy_search.'%');
            });
        }
        
        $friendships = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Obtener buddies y peerbuddies disponibles para el formulario de creación
        $availableBuddies = Buddy::where('type', 'buddy')->orderBy('first_name')->get();
        $availablePeerBuddies = Buddy::where('type', 'peer_buddy')->orderBy('first_name')->get();
        
        // CORREGIDO: Obtener líderes desde users con roles
        $availableBuddyLeaders = User::whereHas('role', function($query) {
            $query->where('name', 'Líder de Buddies');
        })->orderBy('name')->get();
        
        $availablePeerBuddyLeaders = User::whereHas('role', function($query) {
            $query->where('name', 'Líder de PeerBuddies');
        })->orderBy('name')->get();

        $buddies = Buddy::paginate(10);
        
        return view('friendships.index', compact('friendships', 'buddies', 'availableBuddies', 'availablePeerBuddies', 'availableBuddyLeaders',
            'availablePeerBuddyLeaders'));
    }

    //Detalles de amistad
    // app/Http/Controllers/FriendshipController.php

// En tu controlador FriendshipController
public function show(Friendship $friendship){
    $friendship->load([
        'buddy', 
        'peerBuddy', 
        'buddyLeader', 
        'peerBuddyLeader',
        'followUps' => function($query) {
            $query->orderBy('created_at', 'desc')->with('user');
        },
        'attendanceRecords' => function($query) {
            $query->orderBy('updated_at', 'desc')
            ->orderBy('date', 'desc');
        }
    ]);
    // Obtener el último timestamp de actualización
    $lastUpdate = optional($friendship->attendanceRecords->first())->updated_at;

    // Filtrar solo los registros de la última actualización
    $lastUpdatedRecords = $lastUpdate 
        ? $friendship->attendanceRecords->where('updated_at', $lastUpdate)
        : collect();

    // Procesar datos de asistencia para estadísticas
     $stats = [
        'total' => $friendship->attendanceRecords->count(),
        'buddy_attended' => $friendship->attendanceRecords->where('buddy_attended', true)->count(),
        'peer_attended' => $friendship->attendanceRecords->where('peer_buddy_attended', true)->count(),
        'both_attended' => $friendship->attendanceRecords
            ->where('buddy_attended', true)
            ->where('peer_buddy_attended', true)
            ->count(),
        'last_update' => $lastUpdate
    ];

    return response()->json([
        'success' => true,
        'friendship' => $friendship,
        'buddy' => $friendship->buddy,
        'peerBuddy' => $friendship->peerBuddy,
        'buddyLeader' => $friendship->buddyLeader,
        'peerBuddyLeader' => $friendship->peerBuddyLeader,
        'followUps' => $friendship->followUps,
        'allAttendanceRecords' => $friendship->attendanceRecords,
        'attendanceRecords' => $lastUpdatedRecords,
        'attendanceStats' => $stats,
        'hasFollowUps' => $friendship->followUps->isNotEmpty(),
        'hasAttendance' => $lastUpdatedRecords->isNotEmpty()
    ]);
}
    public function createBuddy()
    {
        return view('buddies.create');
    }

    public function storeBuddy(Request $request)
    {
        $validated = $request->validate([
            'ci' => 'required|unique:buddies|max:20',
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'type' => 'required|in:buddy,peer_buddy',
            'disability' => 'required_if:type,buddy|nullable|max:100',
            'age' => 'required|integer|min:1',
            'phone' => 'required|max:20',
            'address' => 'required|max:255',
            'email' => 'nullable|email|max:100',
            'interests' => 'nullable',
            'additional_info' => 'nullable'
        ]);

        Buddy::create($validated);

        return redirect()->route('friendships.index')
            ->with('success', 'Persona registrada exitosamente.');
    }

    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'buddy_id' => 'required|exists:buddies,id',
    //         'peer_buddy_id' => 'required|exists:buddies,id|different:buddy_id',
    //         'buddy_leader_id' => 'required|exists:users,id', // CAMBIADO: ahora referencia users
    //         'peer_buddy_leader_id' => 'required|exists:users,id', // CAMBIADO: ahora referencia users
    //         'start_date' => 'required|date',
    //         'status' => 'required|string',
    //         'notes' => 'nullable|string'
    //     ]);

    //     // Verificar tipos
    //     $buddy = Buddy::findOrFail($validated['buddy_id']);
    //     $peerBuddy = Buddy::findOrFail($validated['peer_buddy_id']);
        
    //     // CORREGIDO: Verificar que los líderes tengan los roles correctos
    //     $buddyLeader = User::with('role')->findOrFail($validated['buddy_leader_id']);
    //     $peerBuddyLeader = User::with('role')->findOrFail($validated['peer_buddy_leader_id']);

    //     if ($buddy->type !== 'buddy') {
    //         return back()->with('error', 'El Buddy seleccionado debe ser una persona con discapacidad');
    //     }

    //     if ($peerBuddy->type !== 'peer_buddy') {
    //         return back()->with('error', 'El PeerBuddy seleccionado debe ser una persona sin discapacidad');
    //     }

    //     if (!$buddyLeader->role || $buddyLeader->role->name !== 'Líder de Buddies') {
    //         return back()->with('error', 'El usuario seleccionado debe tener el rol de Líder de Buddies');
    //     }

    //     if (!$peerBuddyLeader->role || $peerBuddyLeader->role->name !== 'Líder de PeerBuddies') {
    //         return back()->with('error', 'El usuario seleccionado debe tener el rol de Líder de PeerBuddies');
    //     }

    //     // Verificar relación existente
    //     $existing = Friendship::where(function($q) use ($validated) {
    //         $q->where('buddy_id', $validated['buddy_id'])
    //           ->where('peer_buddy_id', $validated['peer_buddy_id']);
    //     })->orWhere(function($q) use ($validated) {
    //         $q->where('buddy_id', $validated['peer_buddy_id'])
    //           ->where('peer_buddy_id', $validated['buddy_id']);
    //     })->exists();

    //     if ($existing) {
    //         return back()->with('error', 'Esta relación de amistad ya existe');
    //     }

    //     Friendship::create($validated);

    //     return redirect()->route('friendships.index')
    //         ->with('success', 'Emparejamiento creado exitosamente');
    // }

    public function store(Request $request)
{
    try {
        // Log para debug
        \Log::info('Iniciando creación de amistad', [
            'request_data' => $request->all()
        ]);

        $validated = $request->validate([
            'buddy_id' => 'required|exists:buddies,id',
            'peer_buddy_id' => 'required|exists:buddies,id|different:buddy_id',
            'buddy_leader_id' => 'required|exists:users,id',
            'peer_buddy_leader_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'status' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        // Verificar tipos
        $buddy = Buddy::findOrFail($validated['buddy_id']);
        $peerBuddy = Buddy::findOrFail($validated['peer_buddy_id']);
        
        // Verificar que los líderes tengan los roles correctos
        $buddyLeader = User::with('role')->findOrFail($validated['buddy_leader_id']);
        $peerBuddyLeader = User::with('role')->findOrFail($validated['peer_buddy_leader_id']);

        if ($buddy->type !== 'buddy') {
            return back()->with('error', 'El Buddy seleccionado debe ser una persona con discapacidad');
        }

        if ($peerBuddy->type !== 'peer_buddy') {
            return back()->with('error', 'El PeerBuddy seleccionado debe ser una persona sin discapacidad');
        }

        if (!$buddyLeader->role || $buddyLeader->role->name !== 'Líder de Buddies') {
            return back()->with('error', 'El usuario seleccionado debe tener el rol de Líder de Buddies');
        }

        if (!$peerBuddyLeader->role || $peerBuddyLeader->role->name !== 'Líder de PeerBuddies') {
            return back()->with('error', 'El usuario seleccionado debe tener el rol de Líder de PeerBuddies');
        }

        // Verificar relación existente
        $existing = Friendship::where(function($q) use ($validated) {
            $q->where('buddy_id', $validated['buddy_id'])
              ->where('peer_buddy_id', $validated['peer_buddy_id']);
        })->orWhere(function($q) use ($validated) {
            $q->where('buddy_id', $validated['peer_buddy_id'])
              ->where('peer_buddy_id', $validated['buddy_id']);
        })->exists();

        if ($existing) {
            return back()->with('error', 'Esta relación de amistad ya existe');
        }

        // Crear la amistad
        $friendship = Friendship::create($validated);
        
        \Log::info('Amistad creada exitosamente', [
            'friendship_id' => $friendship->id
        ]);

        // CAMBIO IMPORTANTE: Usar la URL absoluta en lugar de route()
        return redirect('/friendships')
            ->with('success', 'Emparejamiento creado exitosamente');

    } catch (\Illuminate\Validation\ValidationException $e) {
        \Log::error('Error de validación en friendship store', [
            'errors' => $e->errors(),
            'request_data' => $request->all()
        ]);
        return back()->withErrors($e->errors())->withInput();
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        \Log::error('Modelo no encontrado en friendship store', [
            'error' => $e->getMessage(),
            'request_data' => $request->all()
        ]);
        return back()->with('error', 'Registro no encontrado: ' . $e->getMessage());
    } catch (\Exception $e) {
        \Log::error('Error general en friendship store', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'request_data' => $request->all()
        ]);
        return back()->with('error', 'Error interno del servidor. Por favor, inténtelo nuevamente.');
    }
}

    public function updateStatus(Request $request, Friendship $friendship)
    {
        $validated = $request->validate([
            'status' => 'required|string',
        ]);

        $friendship->update(['status' => $validated['status']]);
        
        return redirect()->route('friendships.index')
            ->with('success', 'Estado de amistad actualizado.');
    }

    public function indexBuddy()
    {
        $buddies = Buddy::orderBy('type')
                    ->orderBy('last_name')
                    ->paginate(10);
        
        return view('buddies.index', compact('buddies'));
    }

    public function update(Request $request, Friendship $friendship)
    {
        $validated = $request->validate([
            'status' => 'required|in:Emparejado,Inactivo,Finalizado',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
            'buddy_leader_id' => 'nullable|exists:users,id', // CAMBIADO: ahora referencia users
            'peer_buddy_leader_id' => 'nullable|exists:users,id', // CAMBIADO: ahora referencia users
        ]);

        // CORREGIDO: Verificar roles de usuarios
        if (isset($validated['buddy_leader_id'])) {
            $buddyLeader = User::with('role')->findOrFail($validated['buddy_leader_id']);
            if (!$buddyLeader->role || $buddyLeader->role->name !== 'Líder de Buddies') {
                return back()->with('error', 'El usuario seleccionado debe tener el rol de Líder de Buddies');
            }
        }

        if (isset($validated['peer_buddy_leader_id'])) {
            $peerBuddyLeader = User::with('role')->findOrFail($validated['peer_buddy_leader_id']);
            if (!$peerBuddyLeader->role || $peerBuddyLeader->role->name !== 'Líder de PeerBuddies') {
                return back()->with('error', 'El usuario seleccionado debe tener el rol de Líder de PeerBuddies');
            }
        }

        $friendship->update($validated);

        return redirect()->route('friendships.index')
            ->with('success', 'Emparejamiento actualizado exitosamente');
    }

    public function destroy(Friendship $friendship)
    {
        $friendship->delete();
        
        return redirect()->route('friendships.index')
            ->with('success', 'Emparejamiento eliminado exitosamente.');
    }

    // ===============================
    // MÉTODOS DE SEGUIMIENTO (FOLLOW-UP)
    // ===============================

    /**
     * Mostrar el modal de seguimiento para un emparejamiento específico
     */
    public function showTracking(Friendship $friendship)
    {
        // Cargar relaciones necesarias
        $friendship->load(['buddy', 'peerBuddy', 'followUps' => function($query) {
            $query->with('user')->orderBy('created_at', 'desc');
        }]);

        return response()->json([
            'friendship' => $friendship,
            'buddy' => $friendship->buddy,
            'peerBuddy' => $friendship->peerBuddy,
            'followUps' => $friendship->followUps
        ]);
    }

    /**
     * Obtener registros de asistencia para un rango de fechas
     */
    public function getAttendanceData(Request $request, Friendship $friendship)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        
        // Generar datos de asistencia simulados (puedes reemplazar con datos reales)
        $attendanceData = [];
        $current = $startDate->copy();
        
        while ($current <= $endDate) {
            // Solo días laborables
            if ($current->isWeekday()) {
                $attendanceData[] = [
                    'date' => $current->format('Y-m-d'),
                    'day_name' => $current->translatedFormat('l'),
                    'day_num' => $current->day,
                    'month_name' => $current->translatedFormat('F'),
                    'buddy_present' => rand(0, 100) > 20, // 80% probabilidad de asistencia
                    'peer_buddy_present' => rand(0, 100) > 15, // 85% probabilidad de asistencia
                    'activities' => $this->getRandomActivities(),
                    'notes' => rand(0, 100) > 70 ? $this->getRandomNote() : ''
                ];
            }
            $current->addDay();
        }

        return response()->json(['attendance' => $attendanceData]);
    }

    /**
     * Guardar seguimiento de emparejamiento
     */
    public function storeTracking(Request $request, Friendship $friendship)
    {
        $validated = $request->validate([
            'buddy_progress' => 'required|integer|between:1,5',
            'peer_buddy_progress' => 'required|integer|between:1,5',
            'relationship_quality' => 'required|integer|between:1,5',
            'goals_achieved' => 'nullable|string|max:1000',
            'challenges_faced' => 'nullable|string|max:1000',
            'recommendations' => 'nullable|string|max:1000',
            'next_steps' => 'nullable|string|max:1000',
            'support_needed' => 'nullable|string|max:1000',
            'next_follow_up_date' => 'nullable|date|after:today'
        ]);

        FollowUp::where('friendship_id', $friendship->id)->delete();
        // Agregar datos adicionales
        $validated['friendship_id'] = $friendship->id;
        $validated['user_id'] = Auth::id();

        // Crear el seguimiento
        $followUp = FollowUp::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Seguimiento guardado exitosamente',
            'followUp' => $followUp->load('user')
        ]);
    }

    /**
     * Obtener historial de seguimientos de un emparejamiento
     */
    public function getFollowUpHistory(Friendship $friendship)
    {
        $followUps = $friendship->followUps()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['followUps' => $followUps]);
    }

    /**
     * Actualizar un seguimiento existente
     */
    public function updateTracking(Request $request, Friendship $friendship, FollowUp $followUp)
    {
        // Verificar que el seguimiento pertenece al emparejamiento
        if ($followUp->friendship_id !== $friendship->id) {
            return response()->json(['error' => 'Seguimiento no encontrado'], 404);
        }

        $validated = $request->validate([
            'buddy_progress' => 'required|integer|between:1,5',
            'peer_buddy_progress' => 'required|integer|between:1,5',
            'relationship_quality' => 'required|integer|between:1,5',
            'goals_achieved' => 'nullable|string|max:1000',
            'challenges_faced' => 'nullable|string|max:1000',
            'recommendations' => 'nullable|string|max:1000',
            'next_steps' => 'nullable|string|max:1000',
            'support_needed' => 'nullable|string|max:1000',
            'next_follow_up_date' => 'nullable|date|after:today'
        ]);

        $followUp->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Seguimiento actualizado exitosamente',
            'followUp' => $followUp->load('user')
        ]);
    }

    /**
     * Eliminar un seguimiento
     */
    public function destroyTracking(Friendship $friendship, FollowUp $followUp)
    {
        // Verificar que el seguimiento pertenece al emparejamiento
        if ($followUp->friendship_id !== $friendship->id) {
            return response()->json(['error' => 'Seguimiento no encontrado'], 404);
        }

        $followUp->delete();

        return response()->json([
            'success' => true,
            'message' => 'Seguimiento eliminado exitosamente'
        ]);
    }

    /**
     * Obtener dashboard de seguimientos pendientes
     */
    public function getFollowUpDashboard()
    {
        // Nota: Necesitarás implementar el scope 'pending' en el modelo FollowUp
        $pendingFollowUps = FollowUp::where('next_follow_up_date', '<=', now())
            ->with(['friendship.buddy', 'friendship.peerBuddy', 'user'])
            ->get();

        $upcomingFollowUps = FollowUp::where('next_follow_up_date', '>', now())
            ->where('next_follow_up_date', '<=', now()->addDays(7))
            ->with(['friendship.buddy', 'friendship.peerBuddy', 'user'])
            ->get();

        $recentFollowUps = FollowUp::where('created_at', '>=', now()->subDays(7))
            ->with(['friendship.buddy', 'friendship.peerBuddy', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'pending' => $pendingFollowUps,
            'upcoming' => $upcomingFollowUps,
            'recent' => $recentFollowUps
        ]);
    }

    /**
     * Generar reporte de seguimiento
     */
    public function generateFollowUpReport(Request $request, Friendship $friendship)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date'
        ]);

        $query = $friendship->followUps()->with('user');

        if ($request->start_date) {
            $query->where('created_at', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->where('created_at', '<=', $request->end_date);
        }

        $followUps = $query->orderBy('created_at', 'desc')->get();

        // Calcular estadísticas
        $stats = [
            'total_followups' => $followUps->count(),
            'average_buddy_progress' => $followUps->avg('buddy_progress'),
            'average_peer_buddy_progress' => $followUps->avg('peer_buddy_progress'),
            'average_relationship_quality' => $followUps->avg('relationship_quality'),
        ];

        return response()->json([
            'followUps' => $followUps,
            'stats' => $stats,
            'friendship' => $friendship->load('buddy', 'peerBuddy')
        ]);
    }

    // ===============================
    // MÉTODOS AUXILIARES PRIVADOS
    // ===============================

    /**
     * Obtener actividades aleatorias para simular datos
     */
    private function getRandomActivities()
    {
        $activities = [
            'Actividad de integración',
            'Juegos recreativos',
            'Taller de arte',
            'Actividad deportiva',
            'Conversación libre',
            'Actividad musical',
            'Taller de cocina',
            'Paseo por el parque'
        ];

        return collect($activities)->random(rand(1, 3))->values()->toArray();
    }

    /**
     * Obtener nota aleatoria para simular datos
     */
    private function getRandomNote()
    {
        $notes = [
            'Excelente participación de ambos',
            'Se observó buena comunicación',
            'Necesita más apoyo en actividades grupales',
            'Mostró gran entusiasmo',
            'Requiere seguimiento especial',
            'Progreso notable en socialización'
        ];

        return collect($notes)->random();
    }

    // Método para obtener las fechas y asistencia existente
    public function getAttendanceInfo(Friendship $friendship)
    {
        $friendship->load('attendanceRecords');
        
        return response()->json([
            'friendship' => [
                'id' => $friendship->id,
                'start_date' => $friendship->start_date,
                'end_date' => $friendship->end_date,
                'status' => $friendship->status
            ],
            'existingAttendance' => $friendship->attendanceRecords,
            'recentAttendance' => $friendship->attendanceRecords()
                ->orderBy('date', 'desc')
                ->limit(10)
                ->get()
        ]);
    }

    // Método para guardar las asistencias
public function storeAttendance(Request $request, $friendshipId)
{
    $validated = $request->validate([
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'attendance' => 'required|array',
        'attendance.*.date' => 'required|date',
        'attendance.*.buddy_attended' => 'sometimes|boolean',
        'attendance.*.peer_buddy_attended' => 'sometimes|boolean',
        'attendance.*.notes' => 'nullable|string',
        'attendance.*.id' => 'sometimes|integer|exists:friendship_attendances,id'
    ]);

    $processed = 0;
    $currentTimestamp = now();
    
    // OPCIÓN 1: Eliminar registros anteriores y crear nuevos
    // Eliminar todos los registros de asistencia anteriores para este friendship
    FriendshipAttendance::where('friendship_id', $friendshipId)->delete();
    
    // Crear todos los nuevos registros con el mismo timestamp
    foreach ($validated['attendance'] as $attendance) {
        try {
            FriendshipAttendance::create([
                'friendship_id' => $friendshipId,
                'date' => $attendance['date'],
                'buddy_attended' => $attendance['buddy_attended'] ?? false,
                'peer_buddy_attended' => $attendance['peer_buddy_attended'] ?? false,
                'notes' => $attendance['notes'] ?? null,
                'created_at' => $currentTimestamp,
                'updated_at' => $currentTimestamp
            ]);
            $processed++;
        } catch (\Exception $e) {
            // Log el error pero continuar
            \Log::error('Error creando registro de asistencia: ' . $e->getMessage());
            continue;
        }
    }

    return response()->json([
        'success' => true,
        'message' => 'Asistencia procesada correctamente',
        'processed' => $processed,
        'total_records' => count($validated['attendance'])
    ]);
}

    public function tracking()
    {
        return view('modals.friendships.tracking');
    }

    public function getAttendanceRange(Friendship $friendship, Request $request)
{
    $startDate = $request->get('start_date');
    $endDate = $request->get('end_date');
    
    $attendance = FriendshipAttendance::where('friendship_id', $friendship->id)
        ->whereBetween('date', [$startDate, $endDate])
        ->get()
        ->keyBy('date');
    
    $result = [];
    foreach ($attendance as $date => $record) {
        $result[$date] = [
            'id' => $record->id,
            'buddy_attended' => $record->buddy_attended,
            'peer_buddy_attended' => $record->peer_buddy_attended,
            'notes' => $record->notes
        ];
    }
    
    return response()->json(['attendance' => $result]);
}

public function storeAttendanceBulk(Friendship $friendship, Request $request)
{
    $attendance = $request->get('attendance', []);
    
    try {
        DB::beginTransaction();
        
        foreach ($attendance as $date => $data) {
            if (empty($data['date'])) continue;
            
            $attendanceData = [
                'friendship_id' => $friendship->id,
                'date' => $data['date'],
                'buddy_attended' => isset($data['buddy_attended']) ? 1 : 0,
                'peer_buddy_attended' => isset($data['peer_buddy_attended']) ? 1 : 0,
                'notes' => $data['notes'] ?? null,
            ];
            
            if (!empty($data['attendance_id'])) {
                // Actualizar existente
                FriendshipAttendance::where('id', $data['attendance_id'])
                    ->update($attendanceData);
            } else {
                // Crear nuevo o actualizar si ya existe para esa fecha
                FriendshipAttendance::updateOrCreate(
                    [
                        'friendship_id' => $friendship->id,
                        'date' => $data['date']
                    ],
                    $attendanceData
                );
            }
        }
        
        DB::commit();
        
        return response()->json([
            'success' => true,
            'message' => 'Asistencia guardada correctamente'
        ]);
        
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Error al guardar la asistencia: ' . $e->getMessage()
        ], 500);
    }
}

public function storeFollowUp(Request $request, Friendship $friendship)
{
    // Validación
    $validated = $request->validate([
        'buddy_progress' => 'required|integer|between:1,5',
        'peer_buddy_progress' => 'required|integer|between:1,5',
        'relationship_quality' => 'required|integer|between:1,5',
        'goals_achieved' => 'nullable|string',
        'challenges_faced' => 'nullable|string',
        'recommendations' => 'nullable|string',
        'next_steps' => 'nullable|string',
        'support_needed' => 'nullable|string',
        'next_follow_up_date' => 'nullable|date',
    ]);

    // Crear el seguimiento con el user_id del usuario autenticado
    $followUp = $friendship->followUps()->create([
        ...$validated,
        'user_id' => auth()->id() // Añade el ID del usuario autenticado
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Seguimiento creado exitosamente',
        'followUpId' => $followUp->id
    ]);
}

public function updateFollowUp(Request $request, Friendship $friendship, FollowUp $followUp)
{
    // Opcional: Verificar que el seguimiento pertenece a la amistad
    if ($followUp->friendship_id !== $friendship->id) {
        abort(403, 'Este seguimiento no pertenece a esta amistad');
    }

    // Validación
    $validated = $request->validate([
        'buddy_progress' => 'required|integer|between:1,5',
        'peer_buddy_progress' => 'required|integer|between:1,5',
        'relationship_quality' => 'required|integer|between:1,5',
        // otros campos...
    ]);

    // Actualizar el seguimiento
    $followUp->update($validated);

    return response()->json([
        'success' => true,
        'message' => 'Seguimiento actualizado exitosamente'
    ]);
}

public function getFriendshipsList()
{
    try {
        $friendships = Friendship::with(['buddy', 'peerBuddy'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($friendship) {
                return [
                    'id' => $friendship->id,
                    'buddy_id' => $friendship->buddy_id,
                    'peer_buddy_id' => $friendship->peer_buddy_id,
                    'status' => $friendship->status,
                    'buddy' => [
                        'id' => $friendship->buddy->id,
                        'first_name' => $friendship->buddy->first_name,
                        'last_name' => $friendship->buddy->last_name,
                        'email' => $friendship->buddy->email ?? null,
                    ],
                    'peer_buddy' => [
                        'id' => $friendship->peerBuddy->id,
                        'first_name' => $friendship->peerBuddy->first_name,
                        'last_name' => $friendship->peerBuddy->last_name,
                        'email' => $friendship->peerBuddy->email ?? null,
                    ],
                    'created_at' => $friendship->created_at,
                ];
            });

        return response()->json([
            'success' => true,
            'friendships' => $friendships
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al obtener las amistades: ' . $e->getMessage()
        ], 500);
    }
}

}