{{-- resources/views/components/notification-item.blade.php --}}
@props(['notification'])

<div class="p-6 hover:bg-gray-50 {{ $notification->read_at ? '' : 'bg-blue-50' }}" onclick="window.location.href='{{ route('notifications.show', $notification->id) }}'" style="cursor: pointer;">
    <div class="flex items-start justify-between">
        <div class="flex-1">
            <div class="flex items-center space-x-2">
                @if(!$notification->read_at)
                    <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                @endif
                <h3 class="text-sm font-medium text-gray-900">
                    {{ $notification->data['title'] ?? $notification->title ?? 'Notificación' }}
                </h3>
                <span class="text-xs text-gray-500">
                    {{ $notification->created_at->diffForHumans() }}
                </span>
            </div>
            <p class="mt-1 text-sm text-gray-600">
                {{ $notification->data['message'] ?? $notification->message ?? 'Sin mensaje' }}
            </p>
            @php
                $type = $notification->data['type'] ?? $notification->type ?? 'info';
            @endphp
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                @if($type === 'success') bg-green-100 text-green-800
                @elseif($type === 'warning') bg-yellow-100 text-yellow-800
                @elseif($type === 'error') bg-red-100 text-red-800
                @else bg-blue-100 text-blue-800
                @endif">
                {{ ucfirst($type) }}
            </span>
        </div>
        <div class="flex items-center space-x-2 ml-4">
            @if(!$notification->read_at)
                <button onclick="event.stopPropagation(); markAsRead('{{ $notification->id }}')"
                        class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                    Marcar como leída
                </button>
            @endif
            <button onclick="event.stopPropagation(); deleteNotification('{{ $notification->id }}')"
                    class="text-xs text-red-600 hover:text-red-800 font-medium">
                Eliminar
            </button>
        </div>
    </div>
</div>