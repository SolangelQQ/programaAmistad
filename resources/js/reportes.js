// Configuración global de Chart.js
Chart.defaults.font.family = 'Inter, system-ui, sans-serif';
Chart.defaults.color = '#6B7280';
Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(0, 0, 0, 0.8)';
Chart.defaults.plugins.tooltip.titleColor = '#ffffff';
Chart.defaults.plugins.tooltip.bodyColor = '#ffffff';
Chart.defaults.plugins.tooltip.cornerRadius = 8;

// Variables globales para los gráficos
let participacionChart, tendenciasChart, emparejamientoChart;

// Función para mostrar tabs
function showTab(tabName) {
    // Ocultar todos los contenidos
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Mostrar el contenido seleccionado
    document.getElementById(`content-${tabName}`).classList.remove('hidden');
    
    // Actualizar botones de tab
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('border-blue-500', 'text-blue-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    document.getElementById(`tab-${tabName}`).classList.remove('border-transparent', 'text-gray-500');
    document.getElementById(`tab-${tabName}`).classList.add('border-blue-500', 'text-blue-600');
    
    // Inicializar gráficos si es necesario
    if (tabName === 'general') {
        setTimeout(initCharts, 100);
    } else if (tabName === 'amistades') {
        setTimeout(initEmparejamientoChart, 100);
    }
}

// Inicializar gráficos del resumen general
function initCharts() {
    // Participación Chart
    const participacionCtx = document.getElementById('participacionChart');
    if (participacionCtx && !participacionChart) {
        participacionChart = new Chart(participacionCtx, {
            type: 'doughnut',
            data: {
                labels: window.chartData?.actividades?.labels || ['Act 1', 'Act 2', 'Act 3', 'Act 4'],
                datasets: [{
                    data: window.chartData?.actividades?.data || [85, 78, 65, 45],
                    backgroundColor: [
                        'rgba(168, 85, 247, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 101, 101, 0.8)'
                    ],
                    borderWidth: 0
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
                                return context.label + ': ' + context.parsed + '%';
                            }
                        }
                    }
                }
            }
        });
    }

    // Tendencias Chart
    const tendenciasCtx = document.getElementById('tendenciasChart');
    if (tendenciasCtx && !tendenciasChart) {
        tendenciasChart = new Chart(tendenciasCtx, {
            type: 'line',
            data: {
                labels: window.chartData?.tendencias?.labels || ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
                datasets: [{
                    label: 'Participación',
                    data: window.chartData?.tendencias?.participacion || [65, 75, 70, 85, 88, 92],
                    borderColor: 'rgba(59, 130, 246, 1)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Satisfacción',
                    data: window.chartData?.tendencias?.satisfaccion || [80, 85, 82, 90, 93, 95],
                    borderColor: 'rgba(168, 85, 247, 1)',
                    backgroundColor: 'rgba(168, 85, 247, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    }
}

