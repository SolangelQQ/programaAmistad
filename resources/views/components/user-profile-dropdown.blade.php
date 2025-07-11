{{-- resources/views/components/user-profile-dropdown.blade.php --}}
<div x-data="{ open: false }" class="relative">
    <button @click="open = !open" 
            class="flex items-center p-2 bg-white rounded-full border border-gray-200 shadow-sm hover:bg-gray-50 transition-colors duration-200">
        <img class="h-6 w-6 rounded-full object-cover" 
             src="{{ Auth::user()->avatar ?? 'https://i.pravatar.cc/300?u=' . Auth::user()->email }}" 
             alt="{{ Auth::user()->name }}">
        <span class="ml-2 font-medium text-gray-800 hidden sm:block">{{ Auth::user()->name }}</span>
        <svg class="ml-2 h-4 w-4 text-gray-500 transition-transform duration-200" 
             :class="{'rotate-180': open}"
             fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>
    
    <!-- Menú desplegable del perfil -->
    <div x-show="open"
         @click.away="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 z-50">
        
        <!-- Información del usuario -->
        <div class="px-4 py-3 bg-gray-50 rounded-t-lg">
            <div class="flex items-center">
                <img class="h-10 w-10 rounded-full object-cover" 
                     src="{{ Auth::user()->avatar ?? 'https://i.pravatar.cc/300?u=' . Auth::user()->email }}" 
                     alt="{{ Auth::user()->name }}">
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                </div>
            </div>
        </div>
        
        <!-- Enlaces del menú -->
        <div class="py-1">
            <!-- Enlace a perfil -->
            <a href="{{ route('profile.index') }}" 
               @click="open = false"
               class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition-colors duration-200">
                <svg class="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500" 
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Mi Perfil
            </a>
            
            <!-- Enlace a configuración -->
            <a href="{{ route('settings.index') }}" 
               @click="open = false"
               class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition-colors duration-200">
                <svg class="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500" 
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Configuración
            </a>
            
            <!-- Enlace a notificaciones -->
            <a href="{{ route('notifications.index') }}" 
               @click="open = false"
               class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition-colors duration-200">
                <svg class="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500" 
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M15 17h5l-5 5v-5zM9 17H4l5 5v-5zM9 3v5l5-5H9zM15 3v5l-5-5h5z" />
                </svg>
                Notificaciones
                @if(auth()->user()->unreadNotifications->count() > 0)
                    <span class="ml-auto inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        {{ auth()->user()->unreadNotifications->count() }}
                    </span>
                @endif
            </a>
        </div>
        
        <!-- Separador y logout -->
        <div class="py-1">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        @click="open = false"
                        class="group flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition-colors duration-200">
                    <svg class="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500" 
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Cerrar Sesión
                </button>
            </form>
        </div>
    </div>
</div>