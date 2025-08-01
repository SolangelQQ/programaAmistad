{{-- resources/views/reports/tabs/liderazgo.blade.php --}}
<meta name="csrf-token" content="{{ csrf_token() }}">
<div x-data="liderazgoTab()" x-init="init()">
    <!-- Loading State -->
    <div x-show="loading" class="flex justify-center items-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-500"></div>
        <span class="ml-3 text-gray-600">Cargando datos de liderazgo...</span>
    </div>

    <!-- Error State -->
    <div x-show="error && !loading" class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
        <div class="flex items-center mb-4">
            <svg class="w-6 h-6 text-yellow-600 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <div>
                <h3 class="text-lg font-medium text-yellow-800">Configuración Inicial Requerida</h3>
                <p class="text-sm text-yellow-700 mt-1">Para poder generar reportes de liderazgo, necesitas completar algunos pasos previos.</p>
            </div>
        </div>
        
        <div class="bg-yellow-100 rounded-lg p-4 mb-4">
            <h4 class="font-medium text-yellow-800 mb-2">Pasos requeridos:</h4>
            <ul class="text-sm text-yellow-700 space-y-1">
                <li class="flex items-start">
                    <span class="inline-block w-2 h-2 bg-yellow-600 rounded-full mt-1.5 mr-2 flex-shrink-0"></span>
                    <span>Completar el seguimiento de emparejamiento de amistad</span>
                </li>
                <li class="flex items-start">
                    <span class="inline-block w-2 h-2 bg-yellow-600 rounded-full mt-1.5 mr-2 flex-shrink-0"></span>
                    <span>Generar reportes mensuales de monitoreo</span>
                </li>
                <li class="flex items-start">
                    <span class="inline-block w-2 h-2 bg-yellow-600 rounded-full mt-1.5 mr-2 flex-shrink-0"></span>
                    <span>Registrar evaluaciones de monitores</span>
                </li>
            </ul>
        </div>
        
    </div>

    <!-- Content -->
    <div x-show="!loading && !error" class="space-y-6">
        <!-- Header with Export Buttons -->
        <div class="flex justify-between items-center">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Análisis de Liderazgo</h3>
                <p class="text-sm text-gray-600">Evaluación del desempeño de monitores de amistad</p>
            </div>
            
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
            <!-- Total Monitores -->
            <div class="bg-purple-50 p-4 sm:p-6 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-users text-purple-600 text-xl sm:text-2xl mr-3 flex-shrink-0"></i>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs sm:text-sm text-gray-600 truncate">Total Monitores</p>
                        <p class="text-xl sm:text-2xl font-bold text-gray-900" x-text="stats.totalLeaders || 0"></p>
                    </div>
                </div>
            </div>
            
            <!-- Muy Activos -->
            <div class="bg-green-50 p-4 sm:p-6 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-600 text-xl sm:text-2xl mr-3 flex-shrink-0"></i>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs sm:text-sm text-gray-600 truncate">Muy Activos</p>
                        <p class="text-xl sm:text-2xl font-bold text-gray-900" x-text="stats.veryActive || 0"></p>
                    </div>
                </div>
            </div>
            
            <!-- Requieren Atención -->
            <div class="bg-yellow-50 p-4 sm:p-6 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-xl sm:text-2xl mr-3 flex-shrink-0"></i>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs sm:text-sm text-gray-600 truncate">Requieren Atención</p>
                        <p class="text-xl sm:text-2xl font-bold text-gray-900" x-text="stats.needsAttention || 0"></p>
                    </div>
                </div>
            </div>
            
            <!-- Satisfacción Promedio -->
            <div class="bg-blue-50 p-4 sm:p-6 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-star text-blue-600 text-xl sm:text-2xl mr-3 flex-shrink-0"></i>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs sm:text-sm text-gray-600 truncate">Satisfacción Promedio</p>
                        <p class="text-xl sm:text-2xl font-bold text-gray-900" x-text="stats.avgSatisfaction || 0"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4">Niveles de Participación</h3>
                <div style="height: 300px;">
                    <canvas id="participationChart"></canvas>
                </div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4">Niveles de Satisfacción</h3>
                <div style="height: 300px;">
                    <canvas id="satisfactionChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Leadership Performance Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Desempeño de Monitores</h3>
                <p class="text-sm text-gray-600 mt-1">Análisis detallado del rendimiento individual</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monitor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reportes</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amistades</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Participación</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Satisfacción</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Evaluación</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Último Reporte</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="leader in leaders" :key="leader.id">
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center text-sm font-medium text-purple-700" x-text="leader.initials"></div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900" x-text="leader.name"></div>
                                            <div class="text-sm text-gray-500" x-text="leader.email"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="font-medium" x-text="leader.activities_count"></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="font-medium" x-text="leader.friendships_count"></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="getParticipationBadgeClass(leader.participation)" x-text="leader.participation_label"></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="getSatisfactionBadgeClass(leader.satisfaction)" x-text="leader.satisfaction_label"></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="getEvaluationBadgeClass(leader.evaluation)" x-text="leader.evaluation_label"></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span x-show="leader.needs_attention" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        Requiere Atención
                                    </span>
                                    <span x-show="!leader.needs_attention" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Normal
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span x-text="leader.last_activity"></span>
                                </td>
                            </tr>
                        </template>
                        <!-- Empty state -->
                        <tr x-show="leaders.length === 0">
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-5m-5 0h5m0 0v-2a1 1 0 00-1-1h-4a1 1 0 00-1 1v2zM7 7h3v3H7V7z"/>
                                    </svg>
                                    <p class="text-lg font-medium">No hay datos disponibles</p>
                                    <p class="text-sm">No se encontraron reportes de monitoreo para mostrar.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function liderazgoTab() {
    return {
        loading: true,
        error: null,
        stats: {
            totalLeaders: 0,
            veryActive: 0,
            needsAttention: 0,
            avgSatisfaction: '0%'
        },
        leaders: [],
        participationChart: null,
        satisfactionChart: null,

        init() {
            console.log('Inicializando pestaña de liderazgo...');
            this.loadData();
        },

        async loadData() {
            try {
                this.loading = true;
                this.error = null;
                console.log('Cargando datos de liderazgo...');
                
                const baseUrl = window.location.origin;
                const url = `${baseUrl}/reports/liderazgo`;
                console.log('URL de petición:', url);
                
                const headers = {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                };
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (csrfToken) {
                    headers['X-CSRF-TOKEN'] = csrfToken;
                }
                
                const response = await fetch(url, {
                    method: 'GET',
                    headers: headers,
                    credentials: 'same-origin'
                });
                
                console.log('Status de respuesta:', response.status);
                
                if (!response.ok) {
                    // Manejo mejorado de errores
                    this.error = true;
                    return;
                }
                
                const data = await response.json();
                console.log('Datos recibidos:', data);
                
                if (data.success && data.data) {
                    this.stats = data.data.stats || this.stats;
                    this.leaders = data.data.leaders || [];
                    
                    console.log('Datos procesados correctamente:', {
                        stats: this.stats,
                        leadersCount: this.leaders.length
                    });
                    
                    // Inicializar gráficos después de actualizar datos
                    this.$nextTick(() => {
                        if (data.data.charts) {
                            this.initCharts(data.data.charts);
                        } else {
                            console.warn('No hay datos de gráficos disponibles');
                        }
                    });
                } else {
                    this.error = true;
                }
                
            } catch (error) {
                console.error('Error loading leadership data:', error);
                this.error = true;
                
            } finally {
                this.loading = false;
            }
        },

        initCharts(chartData) {
            console.log('Inicializando gráficos con datos:', chartData);
            
            try {
                if (typeof Chart === 'undefined') {
                    console.error('Chart.js no está disponible');
                    return;
                }
                
                if (this.participationChart) {
                    this.participationChart.destroy();
                }
                if (this.satisfactionChart) {
                    this.satisfactionChart.destroy();
                }
                
                const participationCanvas = document.getElementById('participationChart');
                const satisfactionCanvas = document.getElementById('satisfactionChart');
                
                if (!participationCanvas || !satisfactionCanvas) {
                    console.error('Elementos canvas no encontrados');
                    return;
                }
                
                // Participation Chart (Doughnut)
                const participationCtx = participationCanvas.getContext('2d');
                this.participationChart = new Chart(participationCtx, {
                    type: 'doughnut',
                    data: {
                        labels: chartData.participation.labels,
                        datasets: [{
                            data: chartData.participation.data,
                            backgroundColor: [
                                '#10B981', // Verde - Muy Activo
                                '#3B82F6', // Azul - Activo
                                '#F59E0B', // Amarillo - Moderado
                                '#EF4444', // Rojo - Pasivo
                                '#6B7280'  // Gris - Muy Pasivo
                            ],
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    usePointStyle: true
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.parsed;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                        return `${label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });

                // Satisfaction Chart (Bar)
                const satisfactionCtx = satisfactionCanvas.getContext('2d');
                this.satisfactionChart = new Chart(satisfactionCtx, {
                    type: 'bar',
                    data: {
                        labels: chartData.satisfaction.labels,
                        datasets: [{
                            label: 'Cantidad de Monitores',
                            data: chartData.satisfaction.data,
                            backgroundColor: '#8B5CF6',
                            borderColor: '#7C3AED',
                            borderWidth: 1,
                            borderRadius: 4
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
                            },
                            x: {
                                ticks: {
                                    maxRotation: 45
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `${context.dataset.label}: ${context.parsed.y}`;
                                    }
                                }
                            }
                        }
                    }
                });
                
                console.log('Gráficos inicializados correctamente');
                
            } catch (error) {
                console.error('Error inicializando gráficos:', error);
            }
        },

        getParticipationBadgeClass(participation) {
            const classes = {
                'muy-activo': 'bg-green-100 text-green-800',
                'activo': 'bg-blue-100 text-blue-800',
                'moderado': 'bg-yellow-100 text-yellow-800',
                'pasivo': 'bg-red-100 text-red-800',
                'muy-pasivo': 'bg-gray-100 text-gray-800'
            };
            return classes[participation] || 'bg-gray-100 text-gray-800';
        },

        getSatisfactionBadgeClass(satisfaction) {
            const classes = {
                'muy-satisfecho': 'bg-green-100 text-green-800',
                'satisfecho': 'bg-blue-100 text-blue-800',
                'neutral': 'bg-yellow-100 text-yellow-800',
                'insatisfecho': 'bg-red-100 text-red-800',
                'muy-insatisfecho': 'bg-gray-100 text-gray-800'
            };
            return classes[satisfaction] || 'bg-gray-100 text-gray-800';
        },

        getEvaluationBadgeClass(evaluation) {
            const classes = {
                'excelente': 'bg-green-100 text-green-800',
                'buena': 'bg-blue-100 text-blue-800',
                'regular': 'bg-yellow-100 text-yellow-800',
                'deficiente': 'bg-red-100 text-red-800',
                'critica': 'bg-gray-100 text-gray-800'
            };
            return classes[evaluation] || 'bg-gray-100 text-gray-800';
        },

        goToMatching() {
            // Redirigir a la sección de emparejamiento
            window.location.href = '/emparejamiento';
        },

        async exportPDF() {
            try {
                const url = `${window.location.origin}/reports/liderazgo/export/pdf`;
                window.open(url, '_blank');
            } catch (error) {
                console.error('Error exportando PDF:', error);
                alert('Error al exportar PDF');
            }
        },

        async exportExcel() {
            try {
                const url = `${window.location.origin}/reports/liderazgo/export/excel`;
                window.open(url, '_blank');
            } catch (error) {
                console.error('Error exportando Excel:', error);
                alert('Error al exportar Excel');
            }
        },

        async exportWord() {
            try {
                const url = `${window.location.origin}/reports/liderazgo/export/word`;
                window.open(url, '_blank');
            } catch (error) {
                console.error('Error exportando Word:', error);
                alert('Error al exportar Word');
            }
        },

        refreshData() {
            console.log('Refrescando datos...');
            this.loadData();
        }
    }
}
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>