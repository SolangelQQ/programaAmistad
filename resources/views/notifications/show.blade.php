@extends('layouts.app')

@section('title', 'Detalle de Notificación')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <!-- Encabezado -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-bell text-white text-2xl"></i>
                    <h1 class="text-xl font-bold text-white">Detalle de Notificación</h1>
                </div>
                <a href="{{ route('notifications.index') }}" 
                   class="text-white hover:text-blue-200 transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-1"></i> Volver
                </a>
            </div>
        </div>

        <!-- Cuerpo -->
        <div class="p-6">
            <!-- Badge de estado -->
            <div class="flex justify-between items-start mb-6">
                @php
                    $typeColors = [
                        'success' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'icon' => 'fa-check-circle'],
                        'warning' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'icon' => 'fa-exclamation-triangle'],
                        'error' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'icon' => 'fa-times-circle'],
                        'default' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'icon' => 'fa-info-circle']
                    ];
                    
                    $type = $notification->type ?? 'info';
                    $colors = $typeColors[$type] ?? $typeColors['default'];
                @endphp
                
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $colors['bg'] }} {{ $colors['text'] }}">
                    <i class="fas {{ $colors['icon'] }} mr-2"></i>
                    {{ ucfirst($type) }}
                </span>
                
                <span class="text-sm text-gray-500">
                    <i class="far fa-clock mr-1"></i>
                    {{ $notification->created_at->setTimezone('America/La_Paz')->format('d/m/Y H:i:s') }} (BO)
                </span>
            </div>

            <!-- Contenido principal -->
            <div class="space-y-6">
                <!-- Título -->
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">{{ $notification->title }}</h2>
                    <div class="border-t border-gray-200 pt-4">
                        <p class="text-gray-700 whitespace-pre-line">{{ $notification->message }}</p>
                    </div>
                </div>

                <!-- Metadatos -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                    <!-- Remitente -->
                    @if($sender)
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Remitente</h3>
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold">
                                {{ strtoupper(substr($sender->name, 0, 1)) }}
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ $sender->name }}</p>
                                <p class="text-sm text-gray-500">{{ $sender->email }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Estado -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Estado</h3>
                        @if($notification->read_at)
                        <div class="flex items-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i> Leída
                            </span>
                            <span class="ml-2 text-sm text-gray-500">
                                {{ $notification->read_at->setTimezone('America/La_Paz')->format('d/m/Y H:i:s') }}
                            </span>
                        </div>
                        @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            <i class="fas fa-clock mr-1"></i> No leída
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Pie de página -->
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            <div class="flex justify-between items-center">
                <button onclick="deleteNotification('{{ $notification->id }}')" 
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <i class="fas fa-trash mr-2"></i> Eliminar
                </button>
                
                <a href="{{ route('notifications.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-list mr-2"></i> Ver todas
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function deleteNotification(id) {
    if (confirm('¿Estás seguro de que quieres eliminar esta notificación?')) {
        fetch(`/notifications/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = "{{ route('notifications.index') }}";
            } else {
                alert('Error al eliminar la notificación: ' + (data.message || ''));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al eliminar la notificación');
        });
    }
}
</script>
@endsection