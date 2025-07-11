// public/js/upcoming-activities.js

let upcomingActivitiesInstance;

class UpcomingActivities {
    constructor() {
        this.init();
    }
    
    init() {
        this.loadUpcomingActivities();
        this.setupEventListeners();
    }
    
    setupEventListeners() {
        // Escuchar cambios del calendario
        document.addEventListener('activity-created', (e) => {
            this.loadUpcomingActivities();
        });
        
        document.addEventListener('calendar-month-changed', (e) => {
            this.loadUpcomingActivities();
        });
    }
    
    async loadUpcomingActivities() {
        const container = document.getElementById('upcoming-activities');
        if (!container) return;
        
        try {
            // Mostrar loading state
            this.showLoadingState();
            
            const response = await fetch('/api/activities/upcoming', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });
            
            if (!response.ok) {
                throw new Error(`Error ${response.status}: ${response.statusText}`);
            }
            
            const activities = await response.json();
            this.renderUpcomingActivities(activities);
            
        } catch (error) {
            console.error('Error loading upcoming activities:', error);
            this.showError('Error al cargar las actividades: ' + error.message);
        }
    }
    
    showLoadingState() {
        const container = document.getElementById('upcoming-activities');
        container.innerHTML = `
            <div class="space-y-4">
                <div class="flex items-center justify-center py-6">
                    <div class="animate-spin rounded-full h-8 w-8 border-3 border-blue-500 border-t-transparent"></div>
                    <span class="ml-3 text-sm text-gray-600">Cargando actividades...</span>
                </div>
                <!-- Skeleton cards -->
                <div class="space-y-3">
                    ${Array(3).fill(0).map(() => `
                        <div class="border-l-4 border-gray-300 bg-gray-50 p-4 rounded-r-lg">
                            <div class="loading-shimmer h-4 w-3/4 rounded mb-2"></div>
                            <div class="loading-shimmer h-3 w-1/2 rounded mb-3"></div>
                            <div class="space-y-2">
                                <div class="loading-shimmer h-3 w-full rounded"></div>
                                <div class="loading-shimmer h-3 w-2/3 rounded"></div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    }
    
    showError(message) {
        const container = document.getElementById('upcoming-activities');
        container.innerHTML = `
            <div class="text-center py-8">
                <div class="mb-4">
                    <div class="mx-auto w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <h4 class="text-lg font-medium text-gray-900 mb-2">Error al cargar</h4>
                <p class="text-sm text-gray-600 mb-4">${message}</p>
                <button onclick="upcomingActivitiesInstance.loadUpcomingActivities()" 
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Intentar de nuevo
                </button>
            </div>
        `;
    }

    cancelActivity(activityId) {
        if (confirm('¿Estás seguro de que deseas cancelar esta actividad?')) {
            console.log('Cancelando actividad:', activityId);
            
            // Obtener token CSRF
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            console.log('CSRF Token:', token ? 'Found' : 'Not found');
            
            fetch(`/activities/${activityId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    status: 'cancelled'
                })
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                return response.text();
            })
            .then(text => {
                console.log('Response text:', text);
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        this.showNotification('Actividad cancelada', 'success');
                        this.loadUpcomingActivities();
                    } else {
                        throw new Error(data.message || 'Error desconocido');
                    }
                } catch (e) {
                    throw new Error('Respuesta no es JSON válido: ' + text);
                }
            })
            .catch(error => {
                console.error('Error completo:', error);
                this.showError('Error al cancelar: ' + error.message);
            });
        }
    }

    showNotification(message, type = 'success') {
        // Crear notificación simple
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 ${
            type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 3000);
    }
    
    renderUpcomingActivities(activities) {
        const container = document.getElementById('upcoming-activities');
        
        if (!activities || activities.length === 0) {
            container.innerHTML = `
                <div class="text-center py-12">
                    <div class="mb-4">
                        <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0v1a2 2 0 002 2h4a2 2 0 002-2V7M8 7H6a2 2 0 00-2 2v10a2 2 0 002-2V9a2 2 0 00-2-2h-2"></path>
                            </svg>
                        </div>
                    </div>
                    <h4 class="text-lg font-medium text-gray-900 mb-2">Sin actividades</h4>
                    <p class="text-sm text-gray-500">No hay actividades programadas próximamente</p>
                </div>
            `;
            return;
        }

        // Generar HTML para cada actividad usando la misma estructura del calendario
        const activitiesHtml = activities.map((activity, index) => {
            const typeColors = {
                'recreational': 'border-l-blue-500 bg-blue-50',
                'educational': 'border-l-green-500 bg-green-50', 
                'cultural': 'border-l-purple-500 bg-purple-50',
                'sports': 'border-l-orange-500 bg-orange-50',
                'social': 'border-l-pink-500 bg-pink-50'
            };
            
            const statusColors = {
                'scheduled': 'bg-green-100 text-green-800',
                'in_progress': 'bg-yellow-100 text-yellow-800',
                'completed': 'bg-blue-100 text-blue-800',
                'cancelled': 'bg-red-100 text-red-800'
            };
            
            const statusLabels = {
                'scheduled': 'Programada',
                'in_progress': 'En progreso',
                'completed': 'Completada',
                'cancelled': 'Cancelada'
            };
            
            const borderColor = activity.status === 'cancelled' 
                ? 'border-l-red-500 bg-red-50' 
                : (typeColors[activity.type] || 'border-l-gray-500 bg-gray-50');
            const statusColor = statusColors[activity.status] || 'bg-gray-100 text-gray-800';
            const statusLabel = statusLabels[activity.status] || activity.status;
            const isCancelled = activity.status === 'cancelled';

            return `
                <div class="activity-card border-l-4 ${borderColor} p-4 rounded-r-lg hover:shadow-md transition-all duration-200 ${isCancelled ? 'cancelled cancelled-overlay' : ''}"
                     style="animation-delay: ${index * 0.1}s">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1 min-w-0">
                            <h4 class="text-base font-semibold ${isCancelled ? 'text-red-700 line-through' : 'text-gray-900'} mb-1">
                                ${this.escapeHtml(activity.title)}
                                ${isCancelled ? '<span class="text-xs font-normal text-red-600 ml-2">(CANCELADA)</span>' : ''}
                            </h4>
                            ${activity.description ? `<p class="text-sm ${isCancelled ? 'text-red-600' : 'text-gray-600'} mb-2">${this.escapeHtml(activity.description)}</p>` : ''}
                        </div>
                        <span class="status-badge inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium ${statusColor}">
                            ${statusLabel}
                        </span>
                    </div>
                    
                    <!-- Información de fecha y hora -->
                    <div class="space-y-2 mb-3">
                        <div class="flex items-center text-sm ${isCancelled ? 'text-red-600' : 'text-gray-600'}">
                            <svg class="w-4 h-4 mr-2 ${isCancelled ? 'text-red-500' : 'text-blue-500'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0v1a2 2 0 002 2h4a2 2 0 002-2V7M8 7H6a2 2 0 00-2 2v10a2 2 0 002-2V9a2 2 0 00-2-2h-2"></path>
                            </svg>
                            <span>${activity.formatted_date} - ${activity.formatted_time}</span>
                        </div>
                        
                        <div class="flex items-center text-sm ${isCancelled ? 'text-red-600' : 'text-gray-600'}">
                            <svg class="w-4 h-4 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="truncate ${isCancelled ? 'line-through' : ''}">${this.escapeHtml(activity.location)}</span>
                        </div>
                        
                        
                    </div>
                    
                    <!-- Botones de acción -->
                    <div class="flex space-x-2">
                        <button onclick="editActivity(${activity.id})" 
                                class="text-xs bg-blue-100 text-blue-800 px-3 py-1 rounded hover:bg-blue-200 transition-colors duration-200">
                            Editar
                        </button>
                        ${!isCancelled ? `
                            <button onclick="managePhotos(${activity.id})" 
                                    class="text-xs bg-green-100 text-green-800 px-3 py-1 rounded hover:bg-green-200 transition-colors duration-200">
                                Fotos
                            </button>
                            <button onclick="upcomingActivitiesInstance.cancelActivity(${activity.id})" 
                                    class="text-xs text-red-800 px-3 py-1 rounded hover:bg-red-200 transition-colors duration-200">
                                
                            </button>
                        ` : `
                            <button onclick="managePhotos(${activity.id})" 
                                    class="text-xs bg-gray-100 text-gray-600 px-3 py-1 rounded cursor-not-allowed" 
                                    disabled title="No disponible para actividades canceladas">
                                Fotos
                            </button>
                            <span class="text-xs text-red-600 px-3 py-1 bg-red-50 rounded">
                                Actividad Cancelada
                            </span>
                        `}
                    </div>
                </div>
            `;
        }).join('');

        container.innerHTML = `<div class="space-y-3">${activitiesHtml}</div>`;
    }
    
    escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
}

// Función global para el botón de refresh
function refreshUpcoming() {
    const button = event.target.closest('button');
    const icon = button.querySelector('svg');
    
    // Animación de rotación
    icon.classList.add('animate-spin');
    
    // Recargar datos
    if (upcomingActivitiesInstance) {
        upcomingActivitiesInstance.loadUpcomingActivities().finally(() => {
            // Remover animación después de cargar
            setTimeout(() => {
                icon.classList.remove('animate-spin');
            }, 500);
        });
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('upcoming-activities')) {
        upcomingActivitiesInstance = new UpcomingActivities();
    }
});