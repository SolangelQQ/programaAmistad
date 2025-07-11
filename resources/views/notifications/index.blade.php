@extends('layouts.app')

@section('title', 'Notificaciones')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow-sm">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">Notificaciones</h1>
                <div class="flex items-center space-x-4">
                    <!-- Botón para enviar notificación -->
                    <button onclick="openSendNotificationModal()"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span>Enviar Notificación</span>
                    </button>
                    
                    @if($unreadCount > 0)
                        <span class="text-sm text-gray-500">{{ $unreadCount }} sin leer</span>
                        <button onclick="markAllAsRead()"
                                class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                            Marcar todas como leídas
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Lista de notificaciones -->
        <div class="divide-y divide-gray-200">
            @forelse($notifications as $notification)
                <x-notification-item :notification="$notification" />
            @empty
                <div class="p-12 text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No tienes notificaciones</h3>
                    <p class="mt-2 text-sm text-gray-500">Cuando recibas notificaciones, aparecerán aquí.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Modal para enviar notificación -->
<div id="sendNotificationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Enviar Notificación</h3>
                <button onclick="closeSendNotificationModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="sendNotificationForm">
                <div class="mb-4">
                    <label for="recipient" class="block text-sm font-medium text-gray-700 mb-2">
                        Destinatario
                    </label>
                    <select id="recipient" name="recipient" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Seleccionar usuario...</option>
                        <option value="all">Todos los usuarios</option>
                        @if(isset($users))
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="mb-4">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Título
                    </label>
                    <input type="text" id="title" name="title" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Ingresa el título de la notificación">
                </div>

                <div class="mb-4">
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                        Mensaje
                    </label>
                    <textarea id="message" name="message" rows="4" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Escribe tu mensaje aquí..."></textarea>
                </div>

                <div class="mb-4">
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                        Tipo de Notificación
                    </label>
                    <select id="type" name="type" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="info">Información</option>
                        <option value="success">Éxito</option>
                        <option value="warning">Advertencia</option>
                        <option value="error">Error</option>
                    </select>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeSendNotificationModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors duration-200">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-200">
                        Enviar Notificación
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Funciones existentes
async function markAsRead(notificationId) {
    try {
        const response = await fetch(`/notifications/${notificationId}/mark-read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        });

        if (response.ok) {
            location.reload();
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

async function markAllAsRead() {
    try {
        const response = await fetch('/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        });

        if (response.ok) {
            location.reload();
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

async function deleteNotification(notificationId) {
    if (confirm('¿Estás seguro de que quieres eliminar esta notificación?')) {
        try {
            const response = await fetch(`/notifications/${notificationId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                }
            });

            if (response.ok) {
                location.reload();
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }
}

// Nuevas funciones para el modal
function openSendNotificationModal() {
    document.getElementById('sendNotificationModal').classList.remove('hidden');
}

function closeSendNotificationModal() {
    document.getElementById('sendNotificationModal').classList.add('hidden');
    document.getElementById('sendNotificationForm').reset();
}

// Manejar el envío de notificaciones
document.getElementById('sendNotificationForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    // Deshabilitar el botón de envío mientras se procesa
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.textContent;
    submitButton.disabled = true;
    submitButton.textContent = 'Enviando...';
    
    const formData = new FormData(this);
    const data = {
        recipient: formData.get('recipient'),
        title: formData.get('title'),
        message: formData.get('message'),
        type: formData.get('type')
    };
    
    console.log('Enviando datos:', data); // Para debug
    
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            throw new Error('Token CSRF no encontrado. Asegúrate de tener <meta name="csrf-token" content="{{ csrf_token() }}"> en tu layout.');
        }
        
        const response = await fetch('/notifications/send', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });

        console.log('Response status:', response.status); // Para debug
        
        const responseData = await response.json();
        console.log('Response data:', responseData); // Para debug

        if (response.ok) {
            closeSendNotificationModal();
            alert('✅ ' + responseData.message);
            location.reload();
        } else {
            let errorMessage = 'Error al enviar la notificación';
            
            if (responseData.errors) {
                // Errores de validación
                const errorMessages = Object.values(responseData.errors).flat();
                errorMessage = errorMessages.join('\n');
            } else if (responseData.message) {
                errorMessage = responseData.message;
            }
            
            alert('❌ ' + errorMessage);
        }
    } catch (error) {
        console.error('Error completo:', error);
        alert('❌ Error de conexión: ' + error.message);
    } finally {
        // Rehabilitar el botón
        submitButton.disabled = false;
        submitButton.textContent = originalText;
    }
});

// Cerrar modal al hacer clic fuera de él
document.getElementById('sendNotificationModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeSendNotificationModal();
    }
});
</script>
@endsection