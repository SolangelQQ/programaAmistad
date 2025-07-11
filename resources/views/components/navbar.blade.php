<header class="bg-blue-50 py-3 w-full transition-all duration-300"
:class="{'ml-64': sidebarExpanded, 'ml-16': !sidebarExpanded && window.innerWidth >= 768}">
    <div class="mx-auto" style="max-width: 90%">
        <div class="bg-blue-50 rounded-full flex pt-2 pl-4 pr-4">
            <div class="flex-1">
                {{-- Espacio para contenido adicional --}}
            </div>
            
            <!-- lado derecho -->
            <div class="flex items-center space-x-4">
                <!-- Componente de notificaciones -->
                <x-notification-dropdown />
                
                <!-- Perfil de usuario con menu desplegable -->
                <x-user-profile-dropdown />
            </div>
        </div>
    </div>
</header>

{{-- Integración con notificaciones --}}
@if (session('success') || session('error'))
    @include('components.notification')
@endif