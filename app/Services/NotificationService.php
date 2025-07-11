<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class NotificationService
{
    /**
     * Crear una nueva notificación
     */
    public function create(
        User $recipient,
        string $title,
        string $message,
        string $type = Notification::TYPE_INFO,
        array $data = [],
        ?User $sender = null
    ): Notification {
        return Notification::create([
            'user_id' => $recipient->id,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'data' => $data,
            'sender_id' => $sender?->id,
        ]);
    }

    /**
     * Enviar notificación a múltiples usuarios
     */
    public function sendToMultipleUsers(
        array $userIds,
        string $title,
        string $message,
        string $type = Notification::TYPE_INFO,
        array $data = [],
        ?User $sender = null
    ): Collection {
        $notifications = collect();

        foreach ($userIds as $userId) {
            $user = User::find($userId);
            if ($user) {
                $notifications->push(
                    $this->create($user, $title, $message, $type, $data, $sender)
                );
            }
        }

        return $notifications;
    }

    /**
     * Enviar notificación a todos los usuarios excepto al remitente
     */
    public function sendToAllUsers(
        string $title,
        string $message,
        string $type = Notification::TYPE_INFO,
        array $data = [],
        ?User $sender = null
    ): Collection {
        $query = User::query();
        
        if ($sender) {
            $query->where('id', '!=', $sender->id);
        }

        $users = $query->get();
        $notifications = collect();

        foreach ($users as $user) {
            $notifications->push(
                $this->create($user, $title, $message, $type, $data, $sender)
            );
        }

        return $notifications;
    }

    /**
     * Obtener notificaciones de un usuario
     */
    public function getUserNotifications(User $user, int $limit = 10): Collection
    {
        return $user->notifications()
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Obtener notificaciones no leídas de un usuario
     */
    public function getUnreadNotifications(User $user): Collection
    {
        return $user->notifications()
            ->unread()
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Contar notificaciones no leídas
     */
    public function getUnreadCount(User $user): int
    {
        return $user->notifications()->unread()->count();
    }

    /**
     * Marcar notificación como leída
     */
    public function markAsRead(Notification $notification): void
    {
        $notification->markAsRead();
    }

    /**
     * Marcar todas las notificaciones como leídas
     */
    public function markAllAsRead(User $user): void
    {
        $user->notifications()->unread()->update(['read_at' => now()]);
    }

    /**
     * Eliminar notificación
     */
    public function delete(Notification $notification): void
    {
        $notification->delete();
    }

    /**
     * Eliminar notificaciones antiguas (más de 30 días)
     */
    public function deleteOldNotifications(): int
    {
        return Notification::where('created_at', '<', now()->subDays(30))->delete();
    }

    /**
     * Métodos específicos para tipos de notificación
     */

    /**
     * Notificación de nueva amistad
     */
    public function friendshipRequest(User $recipient, User $sender): Notification
    {
        return $this->create(
            $recipient,
            'Nueva solicitud de amistad',
            "{$sender->name} te ha enviado una solicitud de amistad.",
            Notification::TYPE_FRIENDSHIP,
            ['action_url' => route('friends.requests')],
            $sender
        );
    }

    /**
     * Notificación de amistad aceptada
     */
    public function friendshipAccepted(User $recipient, User $sender): Notification
    {
        return $this->create(
            $recipient,
            'Solicitud de amistad aceptada',
            "{$sender->name} ha aceptado tu solicitud de amistad.",
            Notification::TYPE_SUCCESS,
            ['action_url' => route('friends.index')],
            $sender
        );
    }

    /**
     * Notificación de nuevo mensaje
     */
    public function newMessage(User $recipient, User $sender, string $preview = ''): Notification
    {
        return $this->create(
            $recipient,
            'Nuevo mensaje',
            "Tienes un nuevo mensaje de {$sender->name}: {$preview}",
            Notification::TYPE_MESSAGE,
            ['action_url' => route('messages.show', $sender->id)],
            $sender
        );
    }

    /**
     * Notificación de actividad general
     */
    public function activityNotification(
        User $recipient,
        string $title,
        string $message,
        array $data = [],
        ?User $sender = null
    ): Notification {
        return $this->create(
            $recipient,
            $title,
            $message,
            Notification::TYPE_ACTIVITY,
            $data,
            $sender
        );
    }
}