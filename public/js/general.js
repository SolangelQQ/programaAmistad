
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
