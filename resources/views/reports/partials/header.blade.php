{{-- resources/views/reports/partials/header.blade.php --}}
<div class="bg-white shadow-sm border-b border-gray-200 mx-auto" style="max-width: 95%" id="reportsHeader">
    <div class="mx-auto" style="max-width: 95%">
        <div class="flex justify-between items-center">
            <!-- Título y descripción -->
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-bar text-blue-600 text-lg"></i>
                    </div>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mt-4">Reportes</h1>
                    <p class="text-sm text-gray-600 pr-4 mb-4">
                        Análisis y estadísticas.
                    </p>
                </div>
            </div>
            
            <!-- Acciones del header -->
            <div class="flex items-center space-x-4">
                <!-- Selector de período -->
                <div class="flex items-center space-x-2">
                    <label class="text-sm font-medium text-gray-700">Período:</label>
                    <select id="selectedPeriod" class="h-10 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3">
                        <option value="last_7_days">Últimos 7 días</option>
                        <option value="last_30_days" selected>Últimos 30 días</option>
                        <option value="last_3_months">Últimos 3 meses</option>
                        <option value="last_6_months">Últimos 6 meses</option>
                        <option value="last_year">Último año</option>
                    </select>
                </div>
                
                <!-- Botón de actualización de datos -->
                <button id="refreshBtn" class="inline-flex items-center h-10 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                    <i class="fas fa-sync-alt mr-2" id="refreshIcon"></i>
                    <span id="refreshText">Actualizar Datos</span>
                </button>
                
                <!-- Botón de configuración -->
                <!-- <button id="configBtn" class="inline-flex items-center h-10 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <i class="fas fa-cog mr-2"></i>
                    Configurar
                </button> -->
            </div>
        </div>
        
        <!-- Barra de información adicional -->
        <div class="pb-4">
            <div class="flex items-center justify-between text-sm text-gray-600">
                <div class="flex items-center space-x-4">
                    <span>
                        <i class="fas fa-calendar mr-1"></i>
                        Última actualización: <span class="font-medium" id="lastUpdateTime"></span>
                    </span>
                    <span>
                        <i class="fas fa-database mr-1"></i>
                        Registros procesados: <span class="font-medium" id="totalRecords">{{ $total_records ?? 0 }}</span>
                    </span>
                </div>
                <div class="flex items-center space-x-2">
                    <span id="dataStatus" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <i class="fas fa-check-circle mr-1"></i>
                        Datos actualizados
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de configuración -->
<div id="configModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden opacity-0 transition-opacity duration-300">
    <div id="configModalContent" class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white transform scale-95 transition-transform duration-300">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Configuración de Reportes</h3>
                <button id="closeModalBtn" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Formato de exportación</label>
                    <select id="exportFormat" class="w-full h-10 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3">
                        <option value="pdf">PDF</option>
                        <option value="excel">Excel</option>
                        <option value="csv">CSV</option>
                    </select>
                </div>
                
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" id="includeCharts" checked class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Incluir gráficos en exportación</span>
                    </label>
                </div>
                
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" id="autoRefresh" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Actualización automática cada 5 minutos</span>
                    </label>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
                <button id="cancelBtn" class="h-10 px-4 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button id="saveBtn" class="h-10 px-4 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                    <span id="saveText">Guardar</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Configuración global de reportes
window.ReportsHeader = (function() {
    'use strict';
    
    // Estado del componente
    let state = {
        loading: false,
        saving: false,
        selectedPeriod: 'last_30_days',
        exportFormat: 'pdf',
        includeCharts: true,
        autoRefresh: false,
        totalRecords: {{ $total_records ?? 0 }},
        autoRefreshInterval: null
    };
    
    // Referencias DOM
    const elements = {
        refreshBtn: null,
        refreshIcon: null,
        refreshText: null,
        configBtn: null,
        configModal: null,
        configModalContent: null,
        closeModalBtn: null,
        cancelBtn: null,
        saveBtn: null,
        saveText: null,
        selectedPeriod: null,
        exportFormat: null,
        includeCharts: null,
        autoRefresh: null,
        lastUpdateTime: null,
        totalRecords: null,
        dataStatus: null
    };
    
    // Inicializar el componente
    function init() {
        // Obtener referencias DOM
        cacheElements();
        
        // Configurar event listeners
        bindEvents();
        
        // Configurar estado inicial
        updateLastUpdateTime();
        
        // Actualizar la hora cada minuto
        setInterval(updateLastUpdateTime, 60000);
        
        console.log('ReportsHeader initialized');
    }
    
    // Cachear elementos DOM
    function cacheElements() {
        elements.refreshBtn = document.getElementById('refreshBtn');
        elements.refreshIcon = document.getElementById('refreshIcon');
        elements.refreshText = document.getElementById('refreshText');
        elements.configBtn = document.getElementById('configBtn');
        elements.configModal = document.getElementById('configModal');
        elements.configModalContent = document.getElementById('configModalContent');
        elements.closeModalBtn = document.getElementById('closeModalBtn');
        elements.cancelBtn = document.getElementById('cancelBtn');
        elements.saveBtn = document.getElementById('saveBtn');
        elements.saveText = document.getElementById('saveText');
        elements.selectedPeriod = document.getElementById('selectedPeriod');
        elements.exportFormat = document.getElementById('exportFormat');
        elements.includeCharts = document.getElementById('includeCharts');
        elements.autoRefresh = document.getElementById('autoRefresh');
        elements.lastUpdateTime = document.getElementById('lastUpdateTime');
        elements.totalRecords = document.getElementById('totalRecords');
        elements.dataStatus = document.getElementById('dataStatus');
    }
    
    // Configurar event listeners
    function bindEvents() {
        // Botón de actualizar
        elements.refreshBtn?.addEventListener('click', refreshAllData);
        
        // Botón de configurar
        elements.configBtn?.addEventListener('click', openConfigModal);
        
        // Cerrar modal
        elements.closeModalBtn?.addEventListener('click', closeConfigModal);
        elements.cancelBtn?.addEventListener('click', closeConfigModal);
        
        // Guardar configuración
        elements.saveBtn?.addEventListener('click', saveConfig);
        
        // Selector de período
        elements.selectedPeriod?.addEventListener('change', updateDateRange);
        
        // Auto refresh checkbox
        elements.autoRefresh?.addEventListener('change', toggleAutoRefresh);
        
        // Cerrar modal al hacer click fuera
        elements.configModal?.addEventListener('click', function(e) {
            if (e.target === elements.configModal) {
                closeConfigModal();
            }
        });
        
        // Escapar para cerrar modal
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !elements.configModal.classList.contains('hidden')) {
                closeConfigModal();
            }
        });
    }
    
    // Actualizar fecha y hora (timezone Bolivia UTC-4)
    function updateLastUpdateTime() {
        const now = new Date();
        const boliviaTime = new Date(now.getTime() - (4 * 60 * 60 * 1000));
        
        const day = boliviaTime.getUTCDate().toString().padStart(2, '0');
        const month = (boliviaTime.getUTCMonth() + 1).toString().padStart(2, '0');
        const year = boliviaTime.getUTCFullYear();
        const hours = boliviaTime.getUTCHours().toString().padStart(2, '0');
        const minutes = boliviaTime.getUTCMinutes().toString().padStart(2, '0');
        
        const timeString = `${day}/${month}/${year} ${hours}:${minutes}`;
        elements.lastUpdateTime.textContent = timeString;
    }
    
    // Actualizar rango de fechas
    function updateDateRange() {
        state.selectedPeriod = elements.selectedPeriod.value;
        console.log('Período actualizado:', state.selectedPeriod);
    }
    
    // Calcular rango de fechas
    function calculateDateRange() {
        const now = new Date();
        let from, to = new Date();
        
        switch (state.selectedPeriod) {
            case 'last_7_days':
                from = new Date(now.getTime() - (7 * 24 * 60 * 60 * 1000));
                break;
            case 'last_30_days':
                from = new Date(now.getTime() - (30 * 24 * 60 * 60 * 1000));
                break;
            case 'last_3_months':
                from = new Date(now.getTime() - (90 * 24 * 60 * 60 * 1000));
                break;
            case 'last_6_months':
                from = new Date(now.getTime() - (180 * 24 * 60 * 60 * 1000));
                break;
            case 'last_year':
                from = new Date(now.getTime() - (365 * 24 * 60 * 60 * 1000));
                break;
            default:
                from = new Date(now.getTime() - (30 * 24 * 60 * 60 * 1000));
        }
        
        return {
            from: from.toISOString().split('T')[0],
            to: to.toISOString().split('T')[0]
        };
    }
    
    // Actualizar datos
    async function refreshAllData() {
        if (state.loading) return;
        
        state.loading = true;
        updateLoadingState(true);
        updateDataStatus('updating');
        
        try {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!token) {
                throw new Error('Token CSRF no encontrado');
            }

            const dates = calculateDateRange();
            const url = `/reports/general?from=${dates.from}&to=${dates.to}`;
            
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const data = await response.json();
            
            if (response.ok && data.success) {
                // Actualizar estadísticas
                if (data.statistics) {
                    state.totalRecords = data.statistics.totalParticipants || state.totalRecords;
                    elements.totalRecords.textContent = state.totalRecords;
                }
                
                updateLastUpdateTime();
                updateDataStatus('updated');
                
                // Disparar evento para otros componentes
                window.dispatchEvent(new CustomEvent('dataRefreshed', { 
                    detail: data 
                }));
                
                showNotification('Datos actualizados correctamente', 'success');
            } else {
                throw new Error(data.message || 'Error al actualizar datos');
            }
        } catch (error) {
            console.error('Error al actualizar datos:', error);
            updateDataStatus('updated');
            showNotification(error.message || 'Error al actualizar los datos', 'error');
        } finally {
            state.loading = false;
            updateLoadingState(false);
        }
    }
    
    // Actualizar estado de carga
    function updateLoadingState(loading) {
        elements.refreshBtn.disabled = loading;
        
        if (loading) {
            elements.refreshIcon.className = 'animate-spin mr-2 h-4 w-4';
            elements.refreshIcon.innerHTML = '<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>';
            elements.refreshText.textContent = 'Actualizando...';
        } else {
            elements.refreshIcon.className = 'fas fa-sync-alt mr-2';
            elements.refreshIcon.innerHTML = '';
            elements.refreshText.textContent = 'Actualizar Datos';
        }
    }
    
    // Actualizar estado de datos
    function updateDataStatus(status) {
        if (status === 'updating') {
            elements.dataStatus.className = 'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800';
            elements.dataStatus.innerHTML = '<i class="fas fa-clock mr-1"></i>Actualizando datos';
        } else {
            elements.dataStatus.className = 'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800';
            elements.dataStatus.innerHTML = '<i class="fas fa-check-circle mr-1"></i>Datos actualizados';
        }
    }
    
    // Abrir modal de configuración
    function openConfigModal() {
        elements.configModal.classList.remove('hidden');
        
        // Forzar reflow
        elements.configModal.offsetHeight;
        
        // Animar entrada
        elements.configModal.classList.remove('opacity-0');
        elements.configModalContent.classList.remove('scale-95');
        elements.configModalContent.classList.add('scale-100');
        
        // Enfocar el primer elemento
        elements.exportFormat.focus();
    }
    
    // Cerrar modal de configuración
    function closeConfigModal() {
        // Animar salida
        elements.configModal.classList.add('opacity-0');
        elements.configModalContent.classList.remove('scale-100');
        elements.configModalContent.classList.add('scale-95');
        
        // Ocultar después de la animación
        setTimeout(() => {
            elements.configModal.classList.add('hidden');
        }, 300);
    }
    
    // Guardar configuración
    async function saveConfig() {
        if (state.saving) return;
        
        state.saving = true;
        updateSavingState(true);
        
        try {
            // Obtener valores del formulario
            state.exportFormat = elements.exportFormat.value;
            state.includeCharts = elements.includeCharts.checked;
            state.autoRefresh = elements.autoRefresh.checked;
            
            // Simular guardado
            await new Promise(resolve => setTimeout(resolve, 1000));
            
            const config = {
                export_format: state.exportFormat,
                include_charts: state.includeCharts,
                auto_refresh: state.autoRefresh
            };
            
            console.log('Configuración guardada:', config);
            
            closeConfigModal();
            showNotification('Configuración guardada correctamente', 'success');
            
            // Aplicar configuración de auto-refresh
            if (state.autoRefresh) {
                startAutoRefresh();
            } else {
                stopAutoRefresh();
            }
            
        } catch (error) {
            console.error('Error al guardar configuración:', error);
            showNotification('Error al guardar la configuración', 'error');
        } finally {
            state.saving = false;
            updateSavingState(false);
        }
    }
    
    // Actualizar estado de guardado
    function updateSavingState(saving) {
        elements.saveBtn.disabled = saving;
        elements.saveText.textContent = saving ? 'Guardando...' : 'Guardar';
    }
    
    // Toggle auto refresh
    function toggleAutoRefresh() {
        state.autoRefresh = elements.autoRefresh.checked;
        
        if (state.autoRefresh) {
            showNotification('Actualización automática activada', 'info');
        } else {
            showNotification('Actualización automática desactivada', 'info');
        }
    }
    
    // Iniciar auto refresh
    function startAutoRefresh() {
        if (state.autoRefreshInterval) {
            clearInterval(state.autoRefreshInterval);
        }
        
        state.autoRefreshInterval = setInterval(() => {
            refreshAllData();
        }, 300000); // 5 minutos
    }
    
    // Detener auto refresh
    function stopAutoRefresh() {
        if (state.autoRefreshInterval) {
            clearInterval(state.autoRefreshInterval);
            state.autoRefreshInterval = null;
        }
    }
    
    // Sistema de notificaciones
    function showNotification(message, type = 'info') {
        const colors = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            info: 'bg-blue-500',
            warning: 'bg-yellow-500'
        };
        
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full`;
        notification.innerHTML = `
            <div class="flex items-center">
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Animar entrada
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        // Auto-remover después de 5 segundos
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 300);
        }, 5000);
    }
    
    // Limpiar al destruir
    function destroy() {
        stopAutoRefresh();
    }
    
    // API pública
    return {
        init: init,
        refreshData: refreshAllData,
        openConfig: openConfigModal,
        destroy: destroy
    };
    
})();

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    window.ReportsHeader.init();
});

// Limpiar al cerrar la página
window.addEventListener('beforeunload', function() {
    window.ReportsHeader.destroy();
});
</script>