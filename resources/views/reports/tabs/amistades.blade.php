{{-- reports/tabs/amistades.blade.php --}}
<div>
    <!-- Header -->
    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
        <h2 class="text-xl font-semibold text-gray-900">Seguimiento de Amistades</h2>
        <p class="text-gray-600 mt-1">Análisis del desarrollo y evolución de las relaciones de amistad</p>
    </div>

    
    <!-- Estadísticas de Amistades -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 sm:gap-6 mb-8" x-show="reportData.amistades">
        <div class="bg-blue-50 p-4 sm:p-6 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-handshake text-blue-600 text-xl sm:text-2xl mr-3 flex-shrink-0"></i>
                <div class="min-w-0 flex-1">
                    <p class="text-xs sm:text-sm text-gray-600 truncate">Total Amistades</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900" x-text="reportData.amistades?.statistics?.totalFriendships || 0"></p>
                </div>
            </div>
        </div>

        <div class="bg-green-50 p-4 sm:p-6 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-heart text-green-600 text-xl sm:text-2xl mr-3 flex-shrink-0"></i>
                <div class="min-w-0 flex-1">
                    <p class="text-xs sm:text-sm text-gray-600 truncate">Activas</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900" x-text="reportData.amistades?.statistics?.activeFriendships || 0"></p>
                </div>
            </div>
        </div>

        <div class="bg-yellow-50 p-4 sm:p-6 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-pause text-yellow-600 text-xl sm:text-2xl mr-3 flex-shrink-0"></i>
                <div class="min-w-0 flex-1">
                    <p class="text-xs sm:text-sm text-gray-600 truncate">Inactivas</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900" x-text="reportData.amistades?.statistics?.inactiveFriendships || 0"></p>
                </div>
            </div>
        </div>

        <div class="bg-purple-50 p-4 sm:p-6 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-purple-600 text-xl sm:text-2xl mr-3 flex-shrink-0"></i>
                <div class="min-w-0 flex-1">
                    <p class="text-xs sm:text-sm text-gray-600 truncate">Completadas</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900" x-text="reportData.amistades?.statistics?.completedFriendships || 0"></p>
                </div>
            </div>
        </div>

        <div class="bg-indigo-50 p-4 sm:p-6 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-calendar-alt text-indigo-600 text-xl sm:text-2xl mr-3 flex-shrink-0"></i>
                <div class="min-w-0 flex-1">
                    <p class="text-xs sm:text-sm text-gray-600 truncate">Duración Promedio</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900">
                        <span x-text="reportData.amistades?.statistics?.averageDuration || 0"></span> días
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Estado de Amistades -->
        <div class="bg-white p-6 border border-gray-200 rounded-lg">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Estado de Amistades</h3>
            <div class="h-64">
                <canvas x-ref="friendshipStatusChart"></canvas>
            </div>
        </div>

        <!-- Nuevas Amistades por Mes -->
        <div class="bg-white p-6 border border-gray-200 rounded-lg">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Nuevas Amistades por Mes</h3>
            <div class="h-64">
                <canvas x-ref="newFriendshipsChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Métricas de Rendimiento -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 border border-gray-200 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Tasa de Éxito</p>
                    <p class="text-2xl font-bold text-green-600" x-text="calculateSuccessRate() + '%'"></p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-chart-line text-green-600 text-xl"></i>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">Amistades activas vs total</p>
        </div>

        <div class="bg-white p-6 border border-gray-200 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Nuevas Este Mes</p>
                    <p class="text-2xl font-bold text-blue-600" x-text="reportData.amistades?.statistics?.newFriendships || 0"></p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-plus-circle text-blue-600 text-xl"></i>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">En el período seleccionado</p>
        </div>

        <div class="bg-white p-6 border border-gray-200 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Retención</p>
                    <p class="text-2xl font-bold text-purple-600" x-text="calculateRetentionRate() + '%'"></p>
                </div>
                <div class="p-3 bg-purple-100 rounded-full">
                    <i class="fas fa-user-check text-purple-600 text-xl"></i>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">Amistades que continúan activas</p>
        </div>
    </div>

    <!-- Tabla de Amistades Detallada -->
    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Detalle de Amistades</h3>
                <div class="flex space-x-2">
                    <select x-model="friendshipStatusFilter" @change="friendshipCurrentPage = 1" class="text-sm border-gray-300 rounded-md">
                        <option value="">Todos los estados</option>
                        <option value="Emparejado">Activas</option>
                        <option value="Inactivo">Inactivas</option>
                        <option value="Finalizado">Finalizadas</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Buddy</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peer Buddy</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Inicio</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duración</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notas</th>
                        
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="friendship in paginatedFriendships" :key="friendship.id">
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center">
                                            <span class="text-sm font-medium text-white" x-text="friendship.buddy_name.charAt(0)"></span>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900" x-text="friendship.buddy_name"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <div class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center">
                                            <span class="text-sm font-medium text-white" x-text="friendship.peer_buddy_name.charAt(0)"></span>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900" x-text="friendship.peer_buddy_name"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="friendship.formatted_start_date"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span x-text="Math.abs(friendship.duration_days)"></span> días
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                      :class="getFriendshipStatusClass(friendship.status)"
                                      x-text="friendship.status">
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="friendship.notes"></td>
                            
                        </tr>
                    </template>
                    <tr x-show="filteredFriendships.length === 0">
                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                            No se encontraron amistades con los filtros seleccionados
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Mostrando 
                    <span x-text="Math.min((friendshipCurrentPage - 1) * friendshipItemsPerPage + 1, filteredFriendships.length)"></span>
                    a 
                    <span x-text="Math.min(friendshipCurrentPage * friendshipItemsPerPage, filteredFriendships.length)"></span> 
                    de <span x-text="filteredFriendships.length"></span> amistades
                </div>
                <div class="flex space-x-2">
                    <button @click="friendshipCurrentPage--" 
                            :disabled="friendshipCurrentPage === 1"
                            class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50">
                        Anterior
                    </button>
                    <button @click="friendshipCurrentPage++" 
                            :disabled="friendshipCurrentPage * friendshipItemsPerPage >= filteredFriendships.length"
                            class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50">
                        Siguiente
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Insights y Recomendaciones -->
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-blue-50 p-6 rounded-lg">
            <h4 class="text-lg font-medium text-blue-900 mb-3">💡 Insights Clave</h4>
            <ul class="space-y-2 text-sm text-blue-800">
                <li class="flex items-start">
                    <i class="fas fa-arrow-up text-green-500 mr-2 mt-1"></i>
                    <span x-show="reportData.amistades?.statistics?.activeFriendships > reportData.amistades?.statistics?.inactiveFriendships">
                        Las amistades activas superan a las inactivas, indicando un buen nivel de compromiso.
                    </span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-clock text-blue-500 mr-2 mt-1"></i>
                    <span>
                        La duración promedio de las amistades es de 
                        <strong x-text="reportData.amistades?.statistics?.averageDuration || 0"></strong> días.
                    </span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-chart-line text-purple-500 mr-2 mt-1"></i>
                    <span x-show="reportData.amistades?.statistics?.newFriendships > 0">
                        Se han formado <strong x-text="reportData.amistades?.statistics?.newFriendships"></strong> nuevas amistades este período.
                    </span>
                </li>
            </ul>
        </div>

        <div class="bg-green-50 p-6 rounded-lg">
            <h4 class="text-lg font-medium text-green-900 mb-3">📋 Recomendaciones</h4>
            <ul class="space-y-2 text-sm text-green-800">
                <li class="flex items-start">
                    <i class="fas fa-users text-green-600 mr-2 mt-1"></i>
                    <span x-show="calculateSuccessRate() < 70">
                        Considera implementar más actividades de integración para mejorar la tasa de éxito.
                    </span>
                    <span x-show="calculateSuccessRate() >= 70">
                        Excelente tasa de éxito en las amistades. ¡Continúa con las estrategias actuales!
                    </span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-calendar text-green-600 mr-2 mt-1"></i>
                    <span>Programa seguimientos regulares para mantener las amistades activas.</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-heart text-green-600 mr-2 mt-1"></i>
                    <span>Organiza eventos especiales para celebrar los hitos de las amistades duraderas.</span>
                </li>
            </ul>
        </div>
    </div>
</div>

<script>
// Variables para filtros y paginación
// Variables para filtros y paginación
let friendshipStatusFilter = '';
let friendshipCurrentPage = 1;
let friendshipItemsPerPage = 10;
let selectedFriendship = null;

// Computed properties
function getFilteredFriendships() {
    let friendships = this.reportData.amistades?.friendships || [];
    
    if (this.friendshipStatusFilter) {
        friendships = friendships.filter(f => f.status === this.friendshipStatusFilter);
    }
    
    return friendships;
}

function getPaginatedFriendships() {
    const start = (this.friendshipCurrentPage - 1) * this.friendshipItemsPerPage;
    const end = start + this.friendshipItemsPerPage;
    return this.filteredFriendships.slice(start, end);
}

function shouldShowFriendship(index) {
    const start = (this.friendshipCurrentPage - 1) * this.friendshipItemsPerPage;
    return index >= start && index < start + this.friendshipItemsPerPage;
}

// Función para mostrar detalles de una amistad
// Función modificada para mostrar detalles de una amistad
function showFriendshipDetails(friendship) {
    // Poblar los campos del modal con los datos de la amistad
    
    // Información del Buddy
    document.getElementById('view_buddy_name').textContent = friendship.buddy_name || '';
    document.getElementById('view_buddy_disability').textContent = friendship.buddy_disability || '';
    document.getElementById('view_buddy_age').textContent = friendship.buddy_age || '';
    document.getElementById('view_buddy_ci').textContent = friendship.buddy_ci || '';
    document.getElementById('view_buddy_phone').textContent = friendship.buddy_phone || '';
    document.getElementById('view_buddy_email').textContent = friendship.buddy_email || '';
    document.getElementById('view_buddy_address').textContent = friendship.buddy_address || '';
    
    // Información del PeerBuddy
    document.getElementById('view_peerbuddy_name').textContent = friendship.peer_buddy_name || '';
    document.getElementById('view_peerbuddy_age').textContent = friendship.peer_buddy_age || '';
    document.getElementById('view_peerbuddy_ci').textContent = friendship.peer_buddy_ci || '';
    document.getElementById('view_peerbuddy_phone').textContent = friendship.peer_buddy_phone || '';
    document.getElementById('view_peerbuddy_email').textContent = friendship.peer_buddy_email || '';
    document.getElementById('view_peerbuddy_address').textContent = friendship.peer_buddy_address || '';
    
    // Información del Emparejamiento
    document.getElementById('view_friendship_id').textContent = friendship.id || '';
    document.getElementById('view_start_date').textContent = friendship.formatted_start_date || friendship.start_date || '';
    document.getElementById('view_end_date').textContent = friendship.formatted_end_date || friendship.end_date || 'Activa';
    document.getElementById('view_notes').textContent = friendship.notes || 'Sin notas';
    
    // Configurar el badge de estado
    const statusBadge = document.getElementById('view_status_badge');
    statusBadge.textContent = friendship.status || '';
    statusBadge.className = `px-3 py-1 text-sm font-semibold rounded-full ${getFriendshipStatusClass(friendship.status)}`;
    
    // Mostrar el modal
    document.getElementById('view-friendship-modal').classList.remove('hidden');
}

// Función para cerrar el modal (si no existe ya)
function closeViewFriendshipModal() {
    document.getElementById('view-friendship-modal').classList.add('hidden');
}

// Función para calcular tasa de éxito
function calculateSuccessRate() {
    const stats = this.reportData.amistades?.statistics;
    if (!stats || !stats.totalFriendships) return 0;
    return Math.round((stats.activeFriendships / stats.totalFriendships) * 100);
}

// Función para calcular tasa de retención
function calculateRetentionRate() {
    const stats = this.reportData.amistades?.statistics;
    if (!stats || !stats.totalFriendships) return 0;
    const activeAndCompleted = stats.activeFriendships + stats.completedFriendships;
    return Math.round((activeAndCompleted / stats.totalFriendships) * 100);
}

function calculateFriendshipDuration(friendship) {
    const startDate = new Date(friendship.start_date);
    const endDate = friendship.end_date ? new Date(friendship.end_date) : new Date();
    
    // Calcular la diferencia en milisegundos
    const timeDifference = endDate.getTime() - startDate.getTime();
    
    // Convertir a días y asegurar que sea positivo
    const daysDifference = Math.abs(Math.ceil(timeDifference / (1000 * 3600 * 24)));
    
    return daysDifference;
}

// Alternativa más simple usando Math.abs para asegurar valor positivo
function processFriendshipData(friendships) {
    return friendships.map(friendship => {
        const startDate = new Date(friendship.start_date);
        const endDate = friendship.end_date ? new Date(friendship.end_date) : new Date();
        
        // Usar Math.abs para garantizar valor positivo
        const duration_days = Math.abs(Math.floor((endDate - startDate) / (1000 * 60 * 60 * 24)));
        
        return {
            ...friendship,
            duration_days: duration_days
        };
    });
}

// Si ya tienes los datos procesados, puedes corregir el valor así:
function fixNegativeDurations(friendships) {
    return friendships.map(friendship => ({
        ...friendship,
        duration_days: Math.abs(friendship.duration_days || 0)
    }));
}

// Función para obtener clases CSS del estado de amistad
function getFriendshipStatusClass(status) {
    const classes = {
        'Emparejado': 'bg-green-100 text-green-800',
        'Inactivo': 'bg-yellow-100 text-yellow-800',
        'Finalizado': 'bg-gray-100 text-gray-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
}

// Función para calcular tasa de éxito
function calculateSuccessRate() {
    const stats = this.reportData.amistades?.statistics;
    if (!stats || !stats.totalFriendships) return 0;
    return Math.round((stats.activeFriendships / stats.totalFriendships) * 100);
}

// Función para calcular tasa de retención
function calculateRetentionRate() {
    const stats = this.reportData.amistades?.statistics;
    if (!stats || !stats.totalFriendships) return 0;
    const activeAndCompleted = stats.activeFriendships + stats.completedFriendships;
    return Math.round((activeAndCompleted / stats.totalFriendships) * 100);
}

// Función para obtener clases CSS del estado de amistad
function getFriendshipStatusClass(status) {
    const classes = {
        'Emparejado': 'bg-green-100 text-green-800',
        'Inactivo': 'bg-yellow-100 text-yellow-800',
        'Finalizado': 'bg-gray-100 text-gray-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
}

// Función para inicializar gráficos de amistades
function initFriendshipsCharts() {
    // Destruir gráficos existentes si los hay
    if (this.friendshipStatusChartInstance) {
        this.friendshipStatusChartInstance.destroy();
    }
    if (this.newFriendshipsChartInstance) {
        this.newFriendshipsChartInstance.destroy();
    }

    // Gráfico de Estado de Amistades
    const statusCtx = this.$refs.friendshipStatusChart.getContext('2d');
    const statusData = this.reportData.amistades?.charts?.friendshipStatus || [];
    
    // Asegurar que tenemos los 3 estados posibles (Emparejado, Inactivo, Finalizado)
    const statusLabels = ['Emparejado', 'Inactivo', 'Finalizado'];
    const statusCounts = statusLabels.map(label => {
        const found = statusData.find(item => item.status === label);
        return found ? found.total : 0;
    });
    
    this.friendshipStatusChartInstance = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusCounts,
                backgroundColor: ['#10B981', '#F59E0B', '#6B7280'],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });

    // Gráfico de Nuevas Amistades por Mes
    const newFriendshipsCtx = this.$refs.newFriendshipsChart.getContext('2d');
    const newFriendshipsData = this.reportData.amistades?.charts?.newFriendshipsByMonth || [];
    
    this.newFriendshipsChartInstance = new Chart(newFriendshipsCtx, {
        type: 'bar',
        data: {
            labels: newFriendshipsData.map(item => item.period),
            datasets: [{
                label: 'Nuevas Amistades',
                data: newFriendshipsData.map(item => item.total),
                backgroundColor: '#3B82F6',
                borderColor: '#2563EB',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `${context.dataset.label}: ${context.raw}`;
                        }
                    }
                }
            }
        }
    });
}
</script>