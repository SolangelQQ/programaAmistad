{{-- programaAmistad/resources/views/dashboard/partials/welcome-card.blade.php --}}
<div style="background: linear-gradient(to right, #93C5FD, #C4B5FD)" class="rounded-2xl p-6 mb-6 border border-gray-200 shadow-sm">
    <h2 class="text-xl font-bold" id="welcomeMessage">Bienvenido, {{ Auth::user()->name }}</h2>
    <p class="text-gray-900" id="dateAndNotifications">
        Hoy es <span id="currentDate">{{ now()->timezone('America/La_Paz')->locale('es-ES')->isoFormat('dddd D [de] MMMM [de] YYYY') }}</span>.
        <span id="notificationText">
            @php
                $count = auth()->user()->unreadNotifications->count();
            @endphp
            @if($count == 0)
                No tiene notificaciones pendientes.
            @elseif($count == 1)
                Tiene <span id="notificationCount">{{ $count }}</span> notificación pendiente.
            @else
                Tiene <span id="notificationCount">{{ $count }}</span> notificaciones pendientes.
            @endif
        </span>
    </p>
</div>

{{-- Script para actualizar el contador dinámicamente --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Función para actualizar el contador de notificaciones
    function updateNotificationCount() {
        fetch('/notifications/unread-count')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                const count = data.count;
                const notificationText = document.getElementById('notificationText');
                
                if (count == 0) {
                    notificationText.innerHTML = 'No tiene notificaciones pendientes.';
                } else if (count == 1) {
                    notificationText.innerHTML = 'Tiene <span id="notificationCount">1</span> notificación pendiente.';
                } else {
                    notificationText.innerHTML = `Tiene <span id="notificationCount">${count}</span> notificaciones pendientes.`;
                }
            })
            .catch(error => {
                console.error('Error al obtener notificaciones:', error);
                // Mantener el valor actual si hay error
            });
    }

    // Actualizar cada 30 segundos
    setInterval(updateNotificationCount, 30000);
    
    // Actualizar inmediatamente al cargar (después de 2 segundos para dar tiempo a que se cargue la página)
    setTimeout(updateNotificationCount, 2000);
});
</script>