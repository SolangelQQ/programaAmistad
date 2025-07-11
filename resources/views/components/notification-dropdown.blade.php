{{-- resources/views/components/notification-dropdown.blade.php --}}
<div x-data="notificationDropdown()" class="relative">
    <!-- Botón de notificaciones -->
   

    <!-- Dropdown de notificaciones -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         @click.away="open = false"
         class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 z-50"
         style="display: none;">
        
        <!-- Header del dropdown -->
        <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center bg-gray-50 rounded-t-lg">
            <h3 class="text-lg font-semibold text-gray-900">Notificaciones</h3>
            <div class="flex items-center space-x-2">
                <span x-show="unreadCount > 0" 
                      class="text-xs text-gray-500" 
                      x-text="`${unreadCount} sin leer`">
                </span>
                <button @click="markAllAsRead()" 
                        x-show="unreadCount > 0"
                        class="text-sm text-blue-600 hover:text-blue-800">
                    Marcar todas como leídas
                </button>
            </div>
        </div>

        <!-- Lista de notificaciones -->
        <div class="max-h-96 overflow-y-auto">
            <!-- Indicador de carga -->
            <template x-if="loading">
                <div class="p-8 text-center">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
                    <p class="text-sm text-gray-500 mt-2">Cargando notificaciones...</p>
                </div>
            </template>

            <!-- Lista de notificaciones -->
            <template x-if="!loading && notifications.length > 0">
                <div>
                    <template x-for="notification in notifications" :key="notification.id">
                        <div class="p-4 border-b border-gray-100 hover:bg-gray-50 transition-colors duration-200 last:border-b-0"
                             :class="{'bg-blue-50': !notification.read_at}"
                             x-data="{ menuOpen: false }">
                            <div class="flex items-start space-x-3">
                                <!-- Icono de la notificación -->
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center"
                                         :class="getNotificationBgClass(notification.type)">
                                        <div class="w-4 h-4" 
                                             :class="getNotificationIconClass(notification.type)"
                                             x-html="getNotificationIcon(notification.type)">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Contenido de la notificación -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h4 class="text-sm font-medium text-gray-900 truncate"
                                                :class="{'font-bold': !notification.read_at}"
                                                x-text="notification.title">
                                            </h4>
                                            <p class="text-sm text-gray-600 mt-1 line-clamp-2" 
                                               x-text="notification.message">
                                            </p>
                                            
                                            <!-- Información del remitente -->
                                            <div class="flex items-center mt-2 text-xs text-gray-500">
                                                <template x-if="notification.sender">
                                                    <div class="flex items-center mr-3">
                                                        <img class="w-4 h-4 rounded-full mr-1" 
                                                             :src="notification.sender.avatar || 'https://i.pravatar.cc/300'" 
                                                             :alt="notification.sender.name">
                                                        <span x-text="notification.sender.name"></span>
                                                    </div>
                                                </template>
                                                <span x-text="formatDate(notification.created_at)"></span>
                                            </div>
                                        </div>

                                        <!-- Indicadores y menú de acciones -->
                                        <div class="flex items-center space-x-2 ml-2">
                                            <!-- Indicador de no leída -->
                                            <template x-if="!notification.read_at">
                                                <span class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0"></span>
                                            </template>
                                            
                                            <!-- Menú de acciones -->
                                            <div class="relative">
                                                <button @click="menuOpen = !menuOpen" 
                                                        class="p-1 text-gray-400 hover:text-gray-600 rounded">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                              d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z">
                                                        </path>
                                                    </svg>
                                                </button>

                                                <div x-show="menuOpen" 
                                                     @click.away="menuOpen = false"
                                                     x-transition:enter="transition ease-out duration-100"
                                                     x-transition:enter-start="transform opacity-0 scale-95"
                                                     x-transition:enter-end="transform opacity-100 scale-100"
                                                     x-transition:leave="transition ease-in duration-75"
                                                     x-transition:leave-start="transform opacity-100 scale-100"
                                                     x-transition:leave-end="transform opacity-0 scale-95"
                                                     class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-20">
                                                    <div class="py-1">
                                                        <template x-if="!notification.read_at">
                                                            <button @click="markAsRead(notification.id); menuOpen = false" 
                                                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                                <div class="flex items-center">
                                                                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                                    </svg>
                                                                    Marcar como leída
                                                                </div>
                                                            </button>
                                                        </template>
                                                        
                                                        <template x-if="notification.data && notification.data.action_url">
                                                            <a :href="notification.data.action_url" 
                                                               @click="menuOpen = false"
                                                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                                <div class="flex items-center">
                                                                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                                    </svg>
                                                                    Ver detalles
                                                                </div>
                                                            </a>
                                                        </template>
                                                        
                                                        <button @click="deleteNotification(notification.id); menuOpen = false" 
                                                                class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                                            <div class="flex items-center">
                                                                <svg class="w-4 h-4 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                </svg>
                                                                Eliminar
                                                            </div>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
            
            <!-- Mensaje cuando no hay notificaciones -->
            <template x-if="!loading && notifications.length === 0">
                <div class="p-8 text-center text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <p class="mt-2">No tienes notificaciones</p>
                </div>
            </template>
        </div>

        <!-- Footer del dropdown -->
        <div class="px-4 py-3 border-t border-gray-200 bg-gray-50 rounded-b-lg">
            <a href="{{ route('notifications.index') }}" 
               class="block text-center text-sm text-blue-600 hover:text-blue-800 font-medium">
                Ver todas las notificaciones
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
function notificationDropdown() {
    return {
        open: false,
        notifications: [],
        unreadCount: 0,
        loading: false,

        init() {
            this.loadNotifications();
            // Actualizar cada 30 segundos
            setInterval(() => {
                if (!this.open) {
                    this.loadNotifications();
                }
            }, 30000);
        },

        toggleDropdown() {
            this.open = !this.open;
            if (this.open && this.notifications.length === 0) {
                this.loadNotifications();
            }
        },

        async loadNotifications() {
            this.loading = true;
            try {
                const response = await fetch('/notifications/api');
                const data = await response.json();
                this.notifications = data.notifications || [];
                this.unreadCount = data.unread_count || 0;
                this.updateGlobalCounter();
            } catch (error) {
                console.error('Error al cargar notificaciones:', error);
                this.notifications = [];
                this.unreadCount = 0;
            } finally {
                this.loading = false;
            }
        },

        async markAsRead(notificationId) {
            try {
                const response = await fetch(`/notifications/${notificationId}/mark-read`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    }
                });

                if (response.ok) {
                    const notification = this.notifications.find(n => n.id === notificationId);
                    if (notification && !notification.read_at) {
                        notification.read_at = new Date().toISOString();
                        this.unreadCount = Math.max(0, this.unreadCount - 1);
                        this.updateGlobalCounter();
                    }
                }
            } catch (error) {
                console.error('Error al marcar como leída:', error);
            }
        },

        async markAllAsRead() {
            try {
                const response = await fetch('/notifications/mark-all-read', {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    }
                });

                if (response.ok) {
                    this.notifications.forEach(notification => {
                        if (!notification.read_at) {
                            notification.read_at = new Date().toISOString();
                        }
                    });
                    this.unreadCount = 0;
                    this.updateGlobalCounter();
                }
            } catch (error) {
                console.error('Error al marcar todas como leídas:', error);
            }
        },

        async deleteNotification(notificationId) {
            try {
                const response = await fetch(`/notifications/${notificationId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    }
                });

                if (response.ok) {
                    const index = this.notifications.findIndex(n => n.id === notificationId);
                    if (index !== -1) {
                        const notification = this.notifications[index];
                        if (!notification.read_at) {
                            this.unreadCount = Math.max(0, this.unreadCount - 1);
                        }
                        this.notifications.splice(index, 1);
                        this.updateGlobalCounter();
                    }
                }
            } catch (error) {
                console.error('Error al eliminar notificación:', error);
            }
        },

        updateGlobalCounter() {
            // Actualizar contador global si existe
            const globalCounter = document.getElementById('notificationCount');
            if (globalCounter) {
                globalCounter.textContent = this.unreadCount;
                globalCounter.style.display = this.unreadCount > 0 ? 'flex' : 'none';
            }
        },

        // Método para ser llamado desde otros componentes
        refreshNotifications() {
            this.loadNotifications();
        }
    }
}

// Funciones auxiliares para el componente de notificación
window.getNotificationIcon = function(type) {
    const icons = {
        info: '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>',
        success: '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>',
        warning: '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>',
        error: '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>',
        friendship: '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path></svg>',
        message: '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path></svg>',
        activity: '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path></svg>'
    };
    return icons[type] || icons.info;
};

window.getNotificationBgClass = function(type) {
    const classes = {
        info: 'bg-blue-100',
        success: 'bg-green-100',
        warning: 'bg-yellow-100',
        error: 'bg-red-100',
        friendship: 'bg-purple-100',
        message: 'bg-indigo-100',
        activity: 'bg-orange-100'
    };
    return classes[type] || 'bg-blue-100';
};

window.getNotificationIconClass = function(type) {
    const classes = {
        info: 'text-blue-500',
        success: 'text-green-500',
        warning: 'text-yellow-500',
        error: 'text-red-500',
        friendship: 'text-purple-500',
        message: 'text-indigo-500',
        activity: 'text-orange-500'
    };
    return classes[type] || 'text-blue-500';
};

window.formatDate = function(dateString) {
    if (!dateString) return '';
    
    const date = new Date(dateString);
    const now = new Date();
    const diffInMinutes = Math.floor((now - date) / (1000 * 60));
    
    if (diffInMinutes < 1) return 'Ahora';
    if (diffInMinutes < 60) return `${diffInMinutes}m`;
    if (diffInMinutes < 1440) return `${Math.floor(diffInMinutes / 60)}h`;
    if (diffInMinutes < 10080) return `${Math.floor(diffInMinutes / 1440)}d`;
    
    return date.toLocaleDateString();
};

// Función global para acceder al componente desde otros lugares
window.refreshNotifications = function() {
    const notificationComponent = document.querySelector('[x-data*="notificationDropdown"]');
    if (notificationComponent && notificationComponent._x_dataStack) {
        const data = notificationComponent._x_dataStack[0];
        if (data.refreshNotifications) {
            data.refreshNotifications();
        }
    }
};
</script>
@endpush