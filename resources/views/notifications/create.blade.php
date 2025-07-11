
{{-- resources/views/notifications/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Enviar Notificación')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h1 class="text-2xl font-bold text-gray-900">Enviar Notificación</h1>
            <p class="mt-1 text-sm text-gray-600">Envía una notificación personalizada a usuarios específicos o a todos.</p>
        </div>

        <form id="notificationForm" class="p-6 space-y-6">
            @csrf
            
            <!-- Título -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Título</label>
                <input type="text" 
                       id="title" 
                       name="title" 
                       required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <!-- Mensaje -->
            <div>
                <label for="message" class="block text-sm font-medium text-gray-700">Mensaje</label>
                <textarea id="message" 
                          name="message" 
                          rows="4"
                          required
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
            </div>

            <!-- Tipo de notificación -->
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700">Tipo</label>
                <select id="type" 
                        name="type"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="info">Información</option>
                    <option value="success">Éxito</option>
                    <option value="warning">Advertencia</option>
                    <option value="error">Error</option>
                    <option value="friendship">Amistad</option>
                    <option value="message">Mensaje</option>
                    <option value="activity">Actividad</option>
                </select>
            </div>

            <!-- Destinatarios -->
            <div x-data="{ sendToAll: false }">
                <div class="flex items-center justify-between">
                    <label class="block text-sm font-medium text-gray-700">Destinatarios</label>
                    <label class="flex items-center">
                        <input type="checkbox" 
                               x-model="sendToAll"
                               name="send_to_all"
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-600">Enviar a todos los usuarios</span>
                    </label>
                </div>

                <div x-show="!sendToAll" class="mt-2">
                    <select name="recipients[]" 
                            multiple
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-sm text-gray-500">Mantén presionado Ctrl (o Cmd) para seleccionar múltiples usuarios.</p>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route('notifications.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Enviar Notificación
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('notificationForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = {
        title: formData.get('title'),
        message: formData.get('message'),
        type: formData.get('type'),
        send_to_all: formData.has('send_to_all'),
        recipients: formData.getAll('recipients[]')
    };

    try {
        const response = await fetch('{{ route("notifications.store") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (response.ok) {
            alert(`✅ ${result.message}. Enviadas: ${result.sent_count}`);
            window.location.href = '{{ route("notifications.index") }}';
        } else {
            alert('❌ Error al enviar las notificaciones');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('❌ Error al enviar las notificaciones');
    }
});
</script>
@endsection