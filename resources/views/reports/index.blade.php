@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50" x-data="reportsManager()">
    <!-- Header Section -->
    @include('reports.partials.header')
    @include('modals.friendships.view')
    
    <!-- Main Content -->
    <div class="mx-auto" style="max-width: 95%">
        <!-- Tabs Navigation -->
        <div class="overflow-x-auto mb-6 border-b border-gray-200">
            <nav class="-mb-px flex space-x-8">
                <button
                    @click="activeTab = 'general'"
                    :class="activeTab === 'general' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                    Resumen General
                </button>
                <button
                    @click="activeTab = 'actividades'"
                    :class="activeTab === 'actividades' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                    Participación de Actividades
                </button>
                <button
                    @click="activeTab = 'amistades'"
                    :class="activeTab === 'amistades' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                    Seguimiento de Amistades
                </button>
                <button
                    @click="activeTab = 'liderazgo'"
                    :class="activeTab === 'liderazgo' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                    Desempeño de Líderes
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="bg-white shadow rounded-lg p-6">
            <!-- General Tab -->
            <div x-show="activeTab === 'general'" x-transition>
                @include('reports.tabs.general')
            </div>

            <!-- Actividades Tab -->
            <div x-show="activeTab === 'actividades'" x-transition>
                @include('reports.tabs.actividades')
            </div>

            <!-- Amistades Tab -->
            <div x-show="activeTab === 'amistades'" x-transition>
                @include('reports.tabs.amistades')
            </div>

            <!-- Liderazgo Tab -->
            <div x-show="activeTab === 'liderazgo'" x-transition>
                @include('reports.tabs.liderazgo')
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-6 flex justify-end space-x-4">
    <div class="relative" x-data="{ open: false }">
        <button
            @click="open = !open"
            :disabled="loading"
            class="bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center shadow-lg">
            <i class="fas fa-download mr-2"></i>
            <span x-show="!loading">Exportar Reporte</span>
            <span x-show="loading" class="flex items-center">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Exportando...
            </span>
            <i class="fas fa-chevron-down ml-2" x-show="!loading"></i>
        </button>
        
        <!-- Dropdown menu mejorado -->
        <div
            x-show="open && !loading"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            @click.away="open = false"
            class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl border border-gray-200 z-50 overflow-hidden">
            
            <div class="bg-gray-50 px-4 py-2 border-b border-gray-200">
                <p class="text-xs text-gray-600 font-medium">Selecciona el formato de exportación</p>
            </div>
            
            <div class="py-2">
                <button
                    @click="exportReport('pdf'); open = false"
                    class="w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-red-50 hover:text-red-700 flex items-center transition-colors duration-150">
                    <i class="fas fa-file-pdf text-red-500 mr-3 w-4"></i>
                    <div>
                        <div class="font-medium">Exportar como PDF</div>
                        <div class="text-xs text-gray-500">Formato de texto plano profesional</div>
                    </div>
                </button>
                
                <button
                    @click="exportReport('excel'); open = false"
                    class="w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-green-50 hover:text-green-700 flex items-center transition-colors duration-150">
                    <i class="fas fa-file-excel text-green-500 mr-3 w-4"></i>
                    <div>
                        <div class="font-medium">Exportar como Excel</div>
                        <div class="text-xs text-gray-500">Hoja de cálculo con datos estructurados</div>
                    </div>
                </button>
                
                <button
                    @click="exportReport('csv'); open = false"
                    class="w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 flex items-center transition-colors duration-150">
                    <i class="fas fa-file-csv text-blue-500 mr-3 w-4"></i>
                    <div>
                        <div class="font-medium">Exportar como CSV</div>
                        <div class="text-xs text-gray-500">Datos separados por comas</div>
                    </div>
                </button>
            </div>
            
            <div class="bg-gray-50 px-4 py-2 border-t border-gray-200">
                <p class="text-xs text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Incluye evaluaciones y asistencia detallada
                </p>
            </div>
        </div>
    </div>
    </div>

    <!-- Loading Modal -->
    <div x-show="loading" x-transition class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto"></div>
                <h3 class="text-lg font-medium text-gray-900 mt-4">Generando reporte...</h3>
                <p class="text-sm text-gray-500 mt-2">Por favor espere mientras procesamos la información.</p>
            </div>
        </div>
    </div>
</div>

<script>
function reportsManager() {
    return {
        // Gestión de pestañas
        activeTab: 'general',
        loading: false,
        
        // Nuevas opciones de período
        reportPeriod: 'custom', // 'custom', 'monthly', 'annual'
        selectedMonth: new Date().getMonth() + 1,
        selectedYear: new Date().getFullYear(),
        dateFrom: null,
        dateTo: null,

        // Propiedades de datos
        reportData: {
            general: null,
            actividades: {
                statistics: null,
                charts: null,
                activities: []
            },
            amistades: null,
            liderazgo: null
        },
        
        // Filtros de fecha
        dateFrom: '',
        dateTo: '',
        friendshipStatusChartInstance: null,
        newFriendshipsChartInstance: null,
        
        // Filtros para actividades
        activityFilter: '',
        statusFilter: '',
        typeFilter: '',
        currentPage: 1,
        itemsPerPage: 10,
        
        // Instancias de gráficos para actividades
        participationChartInstance: null,
        statusChartInstance: null,
        monthlyChartInstance: null,
        satisfactionChartInstance: null,
        
        // Traducciones para la interfaz
        translations: {
            // Estados de actividades
            status: {
                'completed': 'Completada',
                'scheduled': 'Programada',
                'in_progress': 'En Progreso',
                'cancelled': 'Cancelada',
                'active': 'Activa',
                'inactive': 'Inactiva',
                'finished': 'Finalizada'
            },
            // Tipos de actividades
            activityTypes: {
                'recreational': 'Recreativa',
                'educational': 'Educativa',
                'cultural': 'Cultural',
                'sports': 'Deportiva',
                'social': 'Social',
                'community': 'Comunitaria',
                'volunteer': 'Voluntariado',
                'academic': 'Académica'
            },
            // Estados de amistad
            friendshipStatus: {
                'Emparejado': 'Emparejado',
                'Inactivo': 'Inactivo',
                'Finalizado': 'Finalizado',
                'Activo': 'Activo',
                'Pendiente': 'Pendiente'
            },
            // Mensajes de la interfaz
            messages: {
                'loading': 'Cargando...',
                'noData': 'No hay datos disponibles',
                'error': 'Error al cargar los datos',
                'exportSuccess': 'Reporte exportado exitosamente',
                'exportError': 'Error al exportar el reporte',
                'searchPlaceholder': 'Buscar actividades...',
                'selectStatus': 'Seleccionar estado',
                'selectType': 'Seleccionar tipo',
                'allStatuses': 'Todos los estados',
                'allTypes': 'Todos los tipos',
                'itemsPerPage': 'elementos por página',
                'page': 'Página',
                'of': 'de',
                'total': 'Total',
                'activities': 'Actividades',
                'friendships': 'Amistades',
                'leadership': 'Liderazgo',
                'general': 'General'
            }
        },
        
        // Propiedades para amistades
        selectedFriendship: null,
        friendshipData: {
            list: [],
            statistics: null,
            charts: null
        },
        
        // Modal y selección
        isFriendshipModalOpen: false,
        
        // Métodos para manejar la tabla de amistades
        friendshipStatusFilter: '',
        friendshipCurrentPage: 1,
        friendshipItemsPerPage: 10,
        
        // Propiedades computadas para actividades
        get filteredActivities() {
            if (!this.reportData.actividades?.activities || !Array.isArray(this.reportData.actividades.activities)) {
                return [];
            }
            
            let activities = this.reportData.actividades.activities;
            
            // Aplicar filtros
            if (this.activityFilter) {
                activities = activities.filter(activity => 
                    activity.title.toLowerCase().includes(this.activityFilter.toLowerCase()) ||
                    activity.description?.toLowerCase().includes(this.activityFilter.toLowerCase())
                );
            }
            
            if (this.statusFilter) {
                activities = activities.filter(activity => activity.status === this.statusFilter);
            }
            
            if (this.typeFilter) {
                activities = activities.filter(activity => activity.type === this.typeFilter);
            }
            
            return activities;
        },
        
        get paginatedActivities() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.filteredActivities.slice(start, end);
        },
        
        get totalPages() {
            return Math.ceil(this.filteredActivities.length / this.itemsPerPage);
        },
        
        get filteredFriendships() {
            if (!this.reportData.amistades?.friendships) return [];
            
            let friendships = this.reportData.amistades.friendships;
            
            if (this.friendshipStatusFilter) {
                friendships = friendships.filter(f => f.status === this.friendshipStatusFilter);
            }
            
            return friendships;
        },
        
        get paginatedFriendships() {
            const start = (this.friendshipCurrentPage - 1) * this.friendshipItemsPerPage;
            const end = start + this.friendshipItemsPerPage;
            return this.filteredFriendships.slice(start, end);
        },
        
        get friendshipTotalPages() {
            return Math.ceil(this.filteredFriendships.length / this.friendshipItemsPerPage);
        },
        
        // Métodos principales
        async loadTabData(tab) {
            // Si ya tenemos datos para este tab, no volver a cargar
            if (tab === 'actividades' && this.reportData.actividades.statistics) return;
            if (tab === 'amistades' && this.reportData.amistades) return;
            if (tab === 'liderazgo' && this.reportData.liderazgo) return;
            
            this.loading = true;
            
            try {
                console.log(`Cargando datos para la pestaña: ${tab}`);
                
                // Construir la URL del endpoint
                const endpoint = `/reports/${tab}`;
                
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        from: this.dateFrom || new Date(new Date().setMonth(new Date().getMonth() - 1)).toISOString().split('T')[0],
                        to: this.dateTo || new Date().toISOString().split('T')[0]
                    })
                });
                
                if (!response.ok) {
                    throw new Error(`Error HTTP! estado: ${response.status}`);
                }
                
                const responseData = await response.json();
                console.log(`Respuesta recibida para ${tab}:`, responseData);
                
                // Manejar los datos según el tab
                if (tab === 'actividades') {
                    // El backend devuelve los datos en responseData.data
                    const data = responseData.data || responseData;
                    
                    // Para actividades, asegurar que tenemos la estructura correcta
                    this.reportData.actividades = {
                        statistics: data.statistics || {
                            totalActivities: 0,
                            completedActivities: 0,
                            scheduledActivities: 0,
                            cancelledActivities: 0,
                            completionRate: 0
                        },
                        charts: data.charts || {
                            participationByType: [],
                            statusComparison: [],
                            monthlyTrend: []
                        },
                        activities: data.activities || [],
                        popular: data.popular || []
                    };
                    
                    console.log('Datos de actividades procesados:', this.reportData.actividades);
                } else if (tab === 'liderazgo') {
                    const data = responseData.data || responseData;
                    
                    this.reportData.liderazgo = {
                        statistics: data.statistics || data.stats || {
                            totalLeaders: 0,
                            veryActive: 0,
                            needsAttention: 0,
                            avgSatisfaction: '0%'
                        },
                        charts: data.charts || {
                            participation: { labels: [], data: [] },
                            satisfaction: { labels: [], data: [] }
                        },
                        leaders: data.leaders || []
                    };
                    
                    console.log('Datos de liderazgo procesados:', this.reportData.liderazgo);
                } else {
                    // Para otros tabs, manejar de manera similar
                    const data = responseData.data || responseData;
                    this.reportData[tab] = data;
                }
                
                // Inicializar gráficos después de cargar los datos
                this.$nextTick(() => {
                    this.initializeCharts(tab);
                });
                
            } catch (error) {
                console.error(`Error cargando datos de ${tab}:`, error);
                this.showAlert(`Error al cargar datos de ${this.translations.messages[tab] || tab}: ${error.message}`, 'error');
                
                // Establecer datos vacíos en caso de error
                if (tab === 'actividades') {
                    this.reportData.actividades = {
                        statistics: {
                            totalActivities: 0,
                            completedActivities: 0,
                            scheduledActivities: 0,
                            cancelledActivities: 0,
                            completionRate: 0
                        },
                        charts: {
                            participationByType: [],
                            statusComparison: [],
                            monthlyTrend: []
                        },
                        activities: [],
                        popular: []
                    };
                } else if (tab === 'liderazgo') {
                    this.reportData.liderazgo = {
                        statistics: {
                            totalLeaders: 0,
                            veryActive: 0,
                            needsAttention: 0,
                            avgSatisfaction: '0%'
                        },
                        charts: {
                            participation: { labels: [], data: [] },
                            satisfaction: { labels: [], data: [] }
                        },
                        leaders: []
                    };
                }
            } finally {
                this.loading = false;
            }
        },
        
        // Inicializar gráficos basado en la pestaña
        initializeCharts(tab) {
            switch(tab) {
                case 'actividades':
                    this.initActivitiesCharts();
                    break;
                case 'amistades':
                    if (typeof initFriendshipsCharts === 'function') {
                        initFriendshipsCharts.call(this);
                    }
                    break;
                case 'general':
                    if (typeof initGeneralCharts === 'function') {
                        initGeneralCharts.call(this);
                    }
                    break;
                case 'liderazgo':
                    // this.initLeadershipCharts();
                    break;
            }
        },
        
        // Inicializar gráficos de actividades
        initActivitiesCharts() {
            console.log('Inicializando gráficos de actividades...');
            
            if (!this.reportData.actividades?.charts) {
                console.warn('No hay datos de gráficos disponibles para actividades');
                return;
            }
            
            const charts = this.reportData.actividades.charts;
            
            // Gráfico de Participación por Tipo
            if (this.$refs.participationChart && charts.participationByType) {
                this.destroyChart('participationChartInstance');
                
                const ctx = this.$refs.participationChart.getContext('2d');
                this.participationChartInstance = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: charts.participationByType.map(item => 
                            this.translations.activityTypes[item.type] || item.type
                        ),
                        datasets: [{
                            data: charts.participationByType.map(item => item.total),
                            backgroundColor: [
                                '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#06B6D4'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            },
                            title: {
                                display: true,
                                text: 'Participación por Tipo de Actividad'
                            }
                        }
                    }
                });
            }
            
            // Gráfico de Comparación de Estados
            if (this.$refs.statusChart && charts.statusComparison) {
                this.destroyChart('statusChartInstance');
                
                const ctx = this.$refs.statusChart.getContext('2d');
                this.statusChartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: charts.statusComparison.map(item => 
                            this.translations.status[item.status] || item.status
                        ),
                        datasets: [{
                            label: 'Cantidad',
                            data: charts.statusComparison.map(item => item.total),
                            backgroundColor: '#3B82F6'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Actividades por Estado'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Número de Actividades'
                                }
                            }
                        }
                    }
                });
            }
            
            // Gráfico de Tendencia Mensual
            if (this.$refs.monthlyChart && charts.monthlyTrend) {
                this.destroyChart('monthlyChartInstance');
                
                const ctx = this.$refs.monthlyChart.getContext('2d');
                this.monthlyChartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: charts.monthlyTrend.map(item => item.period),
                        datasets: [{
                            label: 'Actividades',
                            data: charts.monthlyTrend.map(item => item.total),
                            borderColor: '#3B82F6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Tendencia Mensual de Actividades'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Número de Actividades'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Período'
                                }
                            }
                        }
                    }
                });
            }
        },

        initLeadershipCharts() {
            console.log('Inicializando gráficos de liderazgo...');
            
            if (!this.reportData.liderazgo?.charts) {
                console.warn('No hay datos de gráficos disponibles para liderazgo');
                return;
            }
            
            const charts = this.reportData.liderazgo.charts;
            
            // Gráfico de Participación
            const participationCanvas = document.getElementById('participationChart');
            if (participationCanvas && charts.participation) {
                this.destroyChart('participationChartInstance');
                
                const ctx = participationCanvas.getContext('2d');
                this.participationChartInstance = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: charts.participation.labels || ['Muy Activo', 'Activo', 'Moderado', 'Pasivo'],
                        datasets: [{
                            data: charts.participation.data || [0, 0, 0, 0],
                            backgroundColor: ['#10B981', '#3B82F6', '#F59E0B', '#EF4444']
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            },
                            title: {
                                display: true,
                                text: 'Nivel de Participación'
                            }
                        }
                    }
                });
            }
            
            // Gráfico de Satisfacción
            const satisfactionCanvas = document.getElementById('satisfactionChart');
            if (satisfactionCanvas && charts.satisfaction) {
                this.destroyChart('satisfactionChartInstance');
                
                const ctx = satisfactionCanvas.getContext('2d');
                this.satisfactionChartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: charts.satisfaction.labels || ['Muy Satisfecho', 'Satisfecho', 'Neutral', 'Insatisfecho'],
                        datasets: [{
                            label: 'Cantidad de Líderes',
                            data: charts.satisfaction.data || [0, 0, 0, 0],
                            backgroundColor: '#8B5CF6'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Satisfacción de Líderes'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            }
        },
        
        // Destruir instancia de gráfico
        destroyChart(instanceName) {
            if (this[instanceName]) {
                this[instanceName].destroy();
                this[instanceName] = null;
            }
        },
        
        // Métodos de utilidad para estilos de actividades
        getActivityTypeClass(type) {
            const classes = {
                'recreational': 'bg-blue-100 text-blue-800',
                'educational': 'bg-green-100 text-green-800',
                'cultural': 'bg-purple-100 text-purple-800',
                'sports': 'bg-orange-100 text-orange-800',
                'social': 'bg-pink-100 text-pink-800',
                'community': 'bg-teal-100 text-teal-800',
                'volunteer': 'bg-indigo-100 text-indigo-800',
                'academic': 'bg-cyan-100 text-cyan-800'
            };
            return classes[type] || 'bg-gray-100 text-gray-800';
        },
        
        getActivityStatusClass(status) {
            const classes = {
                'completed': 'bg-green-100 text-green-800',
                'scheduled': 'bg-blue-100 text-blue-800',
                'in_progress': 'bg-yellow-100 text-yellow-800',
                'cancelled': 'bg-red-100 text-red-800',
                'active': 'bg-green-100 text-green-800',
                'inactive': 'bg-gray-100 text-gray-800'
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
        },
        
        getFriendshipStatusClass(status) {
            const classes = {
                'Emparejado': 'bg-green-100 text-green-800',
                'Inactivo': 'bg-yellow-100 text-yellow-800',
                'Finalizado': 'bg-gray-100 text-gray-800',
                'Activo': 'bg-green-100 text-green-800',
                'Pendiente': 'bg-blue-100 text-blue-800'
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
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
        
        // Métodos para obtener texto traducido
        getActivityTypeText(type) {
            return this.translations.activityTypes[type] || type;
        },
        
        getActivityStatusText(status) {
            return this.translations.status[status] || status;
        },
        
        // Traducir texto
        translate(key, fallback = null) {
            const keys = key.split('.');
            let value = this.translations;
            
            for (let k of keys) {
                if (value && value[k]) {
                    value = value[k];
                } else {
                    return fallback || key;
                }
            }
            
            return value;
        },
        
        // Métodos de paginación
        goToPage(page) {
            if (page >= 1 && page <= this.totalPages) {
                this.currentPage = page;
            }
        },
        
        goToFriendshipPage(page) {
            if (page >= 1 && page <= this.friendshipTotalPages) {
                this.friendshipCurrentPage = page;
            }
        },
        
         // Métodos de exportación - CORREGIDO
        async exportReport(format = 'pdf') {
    // Usar año actual de Bolivia
    const now = new Date();
    // Configurar timezone de Bolivia (UTC-4)
    const boliviaDate = new Date(now.getTime() - (4 * 60 * 60 * 1000));
    const currentYear = boliviaDate.getFullYear();
    
    this.loading = true;
    
    try {
        const requestData = {
            type: this.activeTab,
            format: format,
            period_type: 'annual',
            year: currentYear,
            // Fechas del año completo
            from: `${currentYear}-01-01`,
            to: `${currentYear}-12-31`
        };

        console.log('Exportando reporte:', requestData);

        const response = await fetch('/reports/export', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/octet-stream'
            },
            body: JSON.stringify(requestData)
        });
        
        if (!response.ok) {
            let errorMessage = 'Error desconocido';
            try {
                const errorData = await response.json();
                errorMessage = errorData.error || errorData.message || `Error HTTP: ${response.status}`;
            } catch (e) {
                errorMessage = `Error HTTP: ${response.status} - ${response.statusText}`;
            }
            throw new Error(errorMessage);
        }
        
        // Procesar descarga
        const blob = await response.blob();
        if (blob.size === 0) {
            throw new Error('El archivo generado está vacío');
        }
        
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        
        const extensions = {
            'pdf': 'pdf',
            'excel': 'xlsx',
            'csv': 'csv'
        };
        
        const extension = extensions[format] || 'pdf';
        const reportTypes = {
            'general': 'General',
            'actividades': 'Actividades',
            'amistades': 'Amistades',
            'liderazgo': 'Liderazgo'
        };
        
        const reportName = reportTypes[this.activeTab] || 'Reporte';
        a.download = `Reporte_${reportName}_${currentYear}.${extension}`;
        
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
        
        this.showAlert(`Reporte ${reportName} exportado exitosamente en formato ${format.toUpperCase()}`, 'success');
        
    } catch (error) {
        console.error('Error completo:', error);
        this.showAlert(`Error al exportar: ${error.message}`, 'error');
    } finally {
        this.loading = false;
    }
},
        // Función para mostrar selector de formato (mejorada)
        showFormatSelection() {
            return new Promise((resolve) => {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Seleccionar Formato de Exportación',
                        text: '¿En qué formato deseas exportar el reporte?',
                        icon: 'question',
                        showCancelButton: true,
                        cancelButtonText: 'Cancelar',
                        showDenyButton: true,
                        showConfirmButton: true,
                        confirmButtonText: '<i class="fas fa-file-pdf"></i> PDF',
                        denyButtonText: '<i class="fas fa-file-excel"></i> Excel',
                        footer: '<button class="swal2-confirm swal2-styled" onclick="Swal.clickConfirm()" data-format="csv"><i class="fas fa-file-csv"></i> CSV</button>',
                        customClass: {
                            confirmButton: 'bg-red-600 hover:bg-red-700',
                            denyButton: 'bg-green-600 hover:bg-green-700'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            resolve('pdf');
                        } else if (result.isDenied) {
                            resolve('excel');
                        } else if (result.dismiss === Swal.DismissReason.footer) {
                            resolve('csv');
                        } else {
                            resolve(null);
                        }
                    });
                } else {
                    const format = prompt('Selecciona formato:\n1. PDF\n2. Excel\n3. CSV\n\nEscribe el número (1-3):');
                    const formats = { '1': 'pdf', '2': 'excel', '3': 'csv' };
                    resolve(formats[format] || null);
                }
            });
        },
        
        // Método para mostrar alertas
        showAlert(message, type = 'info') {
            // Implementar sistema de alertas personalizado
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: type === 'error' ? 'Error' : type === 'success' ? 'Éxito' : 'Información',
                    text: message,
                    icon: type,
                    timer: 3000,
                    showConfirmButton: false
                });
            } else {
                alert(message);
            }
        },
        
        // Método para formatear fechas
        formatDate(dateString) {
            if (!dateString) return '';
            
            const date = new Date(dateString);
            return date.toLocaleDateString('es-ES', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        },
        
        // Método para formatear números
        formatNumber(number) {
            return new Intl.NumberFormat('es-ES').format(number);
        },
        
        // Método para formatear porcentajes
        formatPercentage(number) {
            return new Intl.NumberFormat('es-ES', {
                style: 'percent',
                minimumFractionDigits: 1,
                maximumFractionDigits: 1
            }).format(number / 100);
        },
        
        // Limpiar filtros
        clearFilters() {
            this.activityFilter = '';
            this.statusFilter = '';
            this.typeFilter = '';
            this.currentPage = 1;
        },
        
        clearFriendshipFilters() {
            this.friendshipStatusFilter = '';
            this.friendshipCurrentPage = 1;
        },
        
        // Método de inicialización
        init() {
            // Cargar Chart.js si no está disponible
            if (typeof Chart === 'undefined') {
                const script = document.createElement('script');
                script.src = 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js';
                script.onload = () => {
                    console.log('Chart.js cargado');
                };
                document.head.appendChild(script);
            }
            
            // Observar cambios de pestaña
            this.$watch('activeTab', (newTab) => {
                console.log(`Pestaña cambiada a: ${newTab}`);
                this.loadTabData(newTab);
            });
            
            // Observar cambios de filtros de fecha
            this.$watch('dateFrom', () => {
                if (this.dateFrom && this.dateTo) {
                    this.loadTabData(this.activeTab);
                }
            });
            
            this.$watch('dateTo', () => {
                if (this.dateFrom && this.dateTo) {
                    this.loadTabData(this.activeTab);
                }
            });
            
            // Cargar datos de la pestaña inicial
            this.loadTabData(this.activeTab);
        }
    };
}
</script>

@push('styles')
<style>
    .dropdown-shadow {
        box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    
    @keyframes pulse-loading {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    
    .pulse-loading {
        animation: pulse-loading 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

@media print {
    .no-print {
        display: none !important;
    }
    
    .print-full-width {
        width: 100% !important;
        max-width: none !important;
    }
}
</style>
@endpush

@endsection

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<!-- AlpineJS -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>