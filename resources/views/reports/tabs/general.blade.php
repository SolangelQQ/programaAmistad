{{-- resources/views/reports/tabs/general.blade.php --}}

<div class="space-y-6" x-data="generalReportData()">
    <!-- Header con filtros de fecha -->
    <div class="flex justify-between items-center pb-4 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-900">Resumen General</h2>
        <div class="flex space-x-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Desde</label>
                <input type="date" x-model="dateFrom" class="mt-1 block w-full h-10 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Hasta</label>
                <input type="date" x-model="dateTo" class="mt-1 block w-full h-10 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div class="flex items-end">
                <button @click="loadGeneralData()" :disabled="loading"
                        class="bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white px-4 h-10 rounded-md text-sm font-medium transition-colors">
                <span x-show="!loading">Actualizar</span>
                <span x-show="loading" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Cargando...
                </span>
                </button>
            </div>
        </div>
    </div>

    <!-- Mensaje de error -->
    <!-- <div x-show="error" x-text="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded"></div> -->

    <!-- Tarjetas de estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-blue-50 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-users text-blue-600 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Miembros</dt>
                            <dd class="text-lg font-medium text-gray-900" x-text="stats.totalParticipants || 0"></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-green-50 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-calendar-check text-green-600 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Actividades Realizadas</dt>
                            <dd class="text-lg font-medium text-gray-900" x-text="stats.totalActivities || 0"></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-yellow-50 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-handshake text-yellow-600 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Amistades Activas</dt>
                            <dd class="text-lg font-medium text-gray-900" x-text="stats.activeFriendships || 0"></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-purple-50 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-star text-purple-600 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Tasa de Finalización</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                <span x-text="stats.activityCompletionRate || 0"></span>%
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Gráfico de participación mensual -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Participación Mensual</h3>
            <div class="relative" style="height: 300px;">
                <canvas id="monthlyParticipationChart" class="w-full h-full"></canvas>
            </div>
        </div>

        <!-- Gráfico de tipos de actividades -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Distribución de Actividades</h3>
            <div class="relative" style="height: 300px;">
                <canvas id="activityTypesChart" class="w-full h-full"></canvas>
            </div>
        </div>
    </div>

    <!-- Tabla de resumen -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Resumen por Categoría</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Este Mes</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cambio</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="item in summaryData" :key="item.category">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="item.category"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="item.total"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="item.current_month"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="inline-flex items-center" :class="item.change >= 0 ? 'text-green-600' : 'text-red-600'">
                                    <i :class="'fas fa-arrow-' + (item.change >= 0 ? 'up' : 'down') + ' mr-1'"></i>
                                    <span x-text="Math.abs(item.change) + '%'"></span>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                      :class="{
                                          'bg-green-100 text-green-800': item.status === 'excelente',
                                          'bg-yellow-100 text-yellow-800': item.status === 'bueno',
                                          'bg-red-100 text-red-800': item.status === 'regular'
                                      }"
                                      x-text="item.status.charAt(0).toUpperCase() + item.status.slice(1)">
                                </span>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="summaryData.length === 0">
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            No hay datos disponibles para el período seleccionado
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- <script src="{{ asset('js/general.js') }}"></script> -->
 <script>

function generalReportData() {
    return {
        dateFrom: new Date(new Date().setMonth(new Date().getMonth() - 1)).toISOString().split('T')[0],
        dateTo: new Date().toISOString().split('T')[0],
        loading: false,
        error: null,
        // stats: @json($initialStats ?? []),
        summaryData: [],
        charts: {},
        monthlyChart: null,
        activityChart: null,
        chartsInitialized: false,

        init() {
            // Cargar datos iniciales solo una vez
            this.loadGeneralData();
        },

        async loadGeneralData() {
            if (this.loading) return; // Prevenir múltiples llamadas simultáneas
            
            this.loading = true;
            this.error = null;

            try {
                const response = await fetch(`/reports/general?from=${this.dateFrom}&to=${this.dateTo}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                
                if (data.success) {
                    this.stats = data.statistics || {};
                    this.summaryData = data.summary_data || [];
                    this.charts = data.charts || {};
                    
                    // Esperar al siguiente tick antes de actualizar gráficos
                    await this.$nextTick();
                    
                    // Pequeño delay para asegurar que el DOM esté listo
                    setTimeout(() => {
                        this.updateCharts();
                    }, 100);
                } else {
                    throw new Error(data.message || 'Error desconocido');
                }

            } catch (error) {
                console.error('Error loading general data:', error);
                this.error = 'Error al cargar los datos: ' + error.message;
            } finally {
                this.loading = false;
            }
        },

        updateCharts() {
            try {
                this.initializeMonthlyChart();
                this.initializeActivityChart();
                this.chartsInitialized = true;
                console.log('Charts updated successfully');
            } catch (error) {
                console.error('Error updating charts:', error);
                this.error = 'Error al actualizar los gráficos: ' + error.message;
            }
        },

        initializeMonthlyChart() {
            const canvas = document.getElementById('monthlyParticipationChart');
            if (!canvas) {
                console.warn('Monthly chart canvas not found');
                return;
            }

            // Destruir gráfico anterior si existe
            if (this.monthlyChart) {
                this.monthlyChart.destroy();
                this.monthlyChart = null;
            }

            const monthlyData = this.charts.monthlyActivities || [];
            
            if (monthlyData.length === 0) {
                console.warn('No monthly data available');
                // Mostrar mensaje en el canvas
                const ctx = canvas.getContext('2d');
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                ctx.font = '16px Arial';
                ctx.textAlign = 'center';
                ctx.fillStyle = '#6b7280';
                ctx.fillText('No hay datos disponibles', canvas.width / 2, canvas.height / 2);
                return;
            }

            const labels = monthlyData.map(item => item.period || 'Sin fecha');
            const data = monthlyData.map(item => item.total || 0);

            try {
                this.monthlyChart = new Chart(canvas, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Actividades',
                            data: data,
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: 'rgb(59, 130, 246)',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: true,
                                    color: 'rgba(0, 0, 0, 0.1)'
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    display: true,
                                    color: 'rgba(0, 0, 0, 0.1)'
                                },
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        },
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        }
                    }
                });

                console.log('Monthly chart initialized successfully');
            } catch (error) {
                console.error('Error creating monthly chart:', error);
            }
        },

        initializeActivityChart() {
            const canvas = document.getElementById('activityTypesChart');
            if (!canvas) {
                console.warn('Activity chart canvas not found');
                return;
            }

            // Destruir gráfico anterior si existe
            if (this.activityChart) {
                this.activityChart.destroy();
                this.activityChart = null;
            }

            const activityData = this.charts.activitiesByType || [];
            
            if (activityData.length === 0) {
                console.warn('No activity data available');
                // Mostrar mensaje en el canvas
                const ctx = canvas.getContext('2d');
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                ctx.font = '16px Arial';
                ctx.textAlign = 'center';
                ctx.fillStyle = '#6b7280';
                ctx.fillText('No hay datos disponibles', canvas.width / 2, canvas.height / 2);
                return;
            }

            const labels = activityData.map(item => item.type || 'Sin tipo');
            const data = activityData.map(item => item.total || 0);

            try {
                this.activityChart = new Chart(canvas, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: data,
                            backgroundColor: [
                                'rgba(59, 130, 246, 0.8)',   // Blue
                                'rgba(16, 185, 129, 0.8)',   // Green
                                'rgba(245, 158, 11, 0.8)',   // Yellow
                                'rgba(139, 92, 246, 0.8)',   // Purple
                                'rgba(236, 72, 153, 0.8)',   // Pink
                                'rgba(239, 68, 68, 0.8)',    // Red
                                'rgba(6, 182, 212, 0.8)',    // Cyan
                                'rgba(34, 197, 94, 0.8)'     // Emerald
                            ],
                            borderColor: '#fff',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    usePointStyle: true
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.parsed || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        return `${label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });

                console.log('Activity chart initialized successfully');
            } catch (error) {
                console.error('Error creating activity chart:', error);
            }
        },

        // Método para limpiar recursos cuando sea necesario
        destroy() {
            if (this.monthlyChart) {
                this.monthlyChart.destroy();
                this.monthlyChart = null;
            }
            if (this.activityChart) {
                this.activityChart.destroy();
                this.activityChart = null;
            }
        }
    }
}

// Inicializar cuando Alpine esté listo
document.addEventListener('alpine:init', () => {
    console.log('Alpine initialized for general reports');
});

// Limpiar charts cuando la página se descarga
window.addEventListener('beforeunload', () => {
    // Intentar limpiar los charts si existen
    if (window.Chart) {
        Object.values(Chart.instances).forEach(chart => {
            if (chart && typeof chart.destroy === 'function') {
                chart.destroy();
            }
        });
    }
});

 </script>
@endpush