<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->orderBy('created_at', 'desc')->paginate(20);
        $unreadCount = $user->notifications()->where('read_at', null)->count();
        
        // Obtener todos los usuarios para el dropdown (solo si el usuario tiene permisos)
        $users = collect();
        if ($this->canSendNotifications()) {
            $users = User::select('id', 'name', 'email')->orderBy('name')->get();
        }
        
        return view('notifications.index', compact('notifications', 'unreadCount', 'users'));
    }

    /**
     * Store a newly created notification in storage.
     */
    public function store(Request $request)
    {
        // Verificar si el usuario tiene permisos para enviar notificaciones
        if (!$this->canSendNotifications()) {
            return response()->json(['message' => 'No tienes permisos para enviar notificaciones'], 403);
        }

        $validator = Validator::make($request->all(), [
            'recipient' => 'required|string',
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'type' => 'required|in:info,success,warning,error'
        ], [
            'recipient.required' => 'Debes seleccionar un destinatario',
            'title.required' => 'El título es obligatorio',
            'title.max' => 'El título no puede exceder 255 caracteres',
            'message.required' => 'El mensaje es obligatorio',
            'message.max' => 'El mensaje no puede exceder 1000 caracteres',
            'type.required' => 'Debes seleccionar un tipo de notificación',
            'type.in' => 'Tipo de notificación inválido'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $sender = Auth::user();
            $recipient = $request->recipient;
            $title = $request->title;
            $message = $request->message;
            $type = $request->type;

            if ($recipient === 'all') {
                // Enviar a todos los usuarios
                $users = User::where('id', '!=', $sender->id)->get();
                
                foreach ($users as $user) {
                    $this->createNotification($user->id, $sender->id, $title, $message, $type);
                }
                
                $message_response = 'Notificación enviada a todos los usuarios (' . $users->count() . ' usuarios)';
            } else {
                // Enviar a un usuario específico
                $user = User::find($recipient);
                
                if (!$user) {
                    return response()->json(['message' => 'Usuario no encontrado'], 404);
                }
                
                $this->createNotification($user->id, $sender->id, $title, $message, $type);
                $message_response = 'Notificación enviada a ' . $user->name;
            }

            return response()->json([
                'message' => $message_response,
                'success' => true
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function send(Request $request)
    {
        // Verificar si el usuario tiene permisos para enviar notificaciones
        if (!$this->canSendNotifications()) {
            return response()->json(['message' => 'No tienes permisos para enviar notificaciones'], 403);
        }

        $validator = Validator::make($request->all(), [
            'recipient' => 'required|string',
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'type' => 'required|in:info,success,warning,error'
        ], [
            'recipient.required' => 'Debes seleccionar un destinatario',
            'title.required' => 'El título es obligatorio',
            'title.max' => 'El título no puede exceder 255 caracteres',
            'message.required' => 'El mensaje es obligatorio',
            'message.max' => 'El mensaje no puede exceder 1000 caracteres',
            'type.required' => 'Debes seleccionar un tipo de notificación',
            'type.in' => 'Tipo de notificación inválido'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $sender = Auth::user();
            $recipient = $request->recipient;
            $title = $request->title;
            $message = $request->message;
            $type = $request->type;

            if ($recipient === 'all') {
                // Enviar a todos los usuarios
                $users = User::where('id', '!=', $sender->id)->get();
                
                foreach ($users as $user) {
                    $this->createNotification($user->id, $sender->id, $title, $message, $type);
                }
                
                $message_response = 'Notificación enviada a todos los usuarios (' . $users->count() . ' usuarios)';
            } else {
                // Enviar a un usuario específico
                $user = User::find($recipient);
                
                if (!$user) {
                    return response()->json(['message' => 'Usuario no encontrado'], 404);
                }
                
                $this->createNotification($user->id, $sender->id, $title, $message, $type);
                $message_response = 'Notificación enviada a ' . $user->name;
            }

            return response()->json([
                'message' => $message_response,
                'success' => true
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function createNotification($userId, $senderId, $title, $message, $type)
    {
        return Notification::create([
            'user_id' => $userId,
            'sender_id' => $senderId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    private function canSendNotifications()
    {
        $user = Auth::user();
        
        // Definir roles que pueden enviar notificaciones
        $allowedRoles = [
            'Encargado del Programa Amistad',
            'Coordinador de Gestión Humana',
            'Líder de Actividades',
            'Líder de Buddies',
            'Líder de PeerBuddies',
            'Líder de Tutores'
        ];
        
        // Si el usuario tiene un rol específico
        if ($user->role && in_array($user->role->name, $allowedRoles)) {
            return true;
        }
        
        // Si es administrador (puedes ajustar esta lógica según tu sistema)
        if ($user->is_admin ?? false) {
            return true;
        }
        
        return false;
    }

    /**
     * Show the details of a specific notification
     */
    public function show($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        
        // Marcar como leída automáticamente al abrir
        if (is_null($notification->read_at)) {
            $notification->update(['read_at' => now()]);
        }
        
        // Obtener información del remitente si existe
        $sender = null;
        if ($notification->sender_id) {
            $sender = User::find($notification->sender_id);
        }
        
        return view('notifications.show', compact('notification', 'sender'));
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->update(['read_at' => now()]);
        
        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        Auth::user()->notifications()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
            
        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->delete();
        
        return response()->json(['success' => true]);
    }
}