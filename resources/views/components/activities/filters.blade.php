<?php
?>
<div class="bg-white p-4 rounded-lg shadow-sm border" x-data="activityFilters()">
    <h3 class="font-semibold mb-4 text-gray-800">Filtros</h3>
    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de actividad</label>
            <select x-model="filters.type" @change="applyFilters()" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors duration-200">
                <option value="">Todos los tipos</option>
                <option value="recreational">Recreativa</option>
                <option value="educational">Educativa</option>
                <option value="cultural">Cultural</option>
                <option value="sports">Deportiva</option>
                <option value="social">Social</option>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
            <select x-model="filters.status" @change="applyFilters()" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors duration-200">
                <option value="">Todos los estados</option>
                <option value="scheduled">Programada</option>
                <option value="in_progress">En Curso</option>
                <option value="completed">Completada</option>
                <option value="cancelled">Cancelada</option>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Inicio</label>
            <input type="date" x-model="filters.dateStart" @change="applyFilters()" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors duration-200">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Fin</label>
            <input type="date" x-model="filters.dateEnd" @change="applyFilters()" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors duration-200">
        </div>
        
        <!-- Botón limpiar filtros - Solo mostrar si hay filtros activos -->
        <div class="pt-2" x-show="hasActiveFilters()" x-transition>
            <button @click="clearFilters()" 
                    class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Limpiar filtros
            </button>
        </div>
        
        <!-- Filtros activos -->
        <div x-show="hasActiveFilters()" class="pt-2 border-t border-gray-200" x-transition>
            <div class="flex flex-wrap gap-2">
                <span class="text-xs font-medium text-gray-500 mb-2">Filtros activos:</span>
                <template x-for="filter in getActiveFilters()" :key="filter.key">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        <span x-text="filter.label"></span>
                        <button @click="removeFilter(filter.key)" class="ml-1 inline-flex items-center justify-center w-4 h-4 text-blue-400 hover:text-blue-600">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </span>
                </template>
            </div>
        </div>
    </div>
</div>

<!-- <script src="js/activity-filters.js"></script> -->
 <script>
    function activityFilters() {
    return {
        filters: {
            type: '',
            status: '',
            dateStart: '',
            dateEnd: ''
        },
        
        init() {
            // Escuchar cambios de mes del calendario
            document.addEventListener('calendar-month-changed', (event) => {
                this.applyFilters();
            });
            
            // Escuchar eventos de filtrado desde sidebar
            this.$watch('filters', () => {
                this.applyFilters();
            });
            
            // Escuchar eventos externos
            window.addEventListener('filter-changed', (event) => {
                if (event.detail.type) {
                    this.filters.type = event.detail.type;
                }
            });
        },
        
        applyFilters() {
            // Validar rango de fechas
            if (this.filters.dateStart && this.filters.dateEnd && 
                new Date(this.filters.dateStart) > new Date(this.filters.dateEnd)) {
                this.showError('La fecha de inicio no puede ser posterior a la fecha de fin');
                return;
            }
            
            // Crear objeto de filtros limpio (sin valores vacíos)
            const cleanFilters = {};
            Object.keys(this.filters).forEach(key => {
                if (this.filters[key] !== '') {
                    cleanFilters[key] = this.filters[key];
                }
            });
            
            console.log('Filtros aplicados:', cleanFilters); // Debug
            
            // Aplicar filtros al calendario
            if (window.activityCalendar) {
                window.activityCalendar.loadActivities(cleanFilters);
            }
            
            // Disparar evento para actualizar otros componentes
            this.$dispatch('filters-applied', {
                filters: cleanFilters
            });
            
            // También actualizar mediante evento global
            window.dispatchEvent(new CustomEvent('activity-filters-changed', {
                detail: { filters: cleanFilters }
            }));
            
            // Solo mostrar feedback si hay filtros activos
            if (Object.keys(cleanFilters).length > 0) {
                this.showFilterFeedback();
            }
        },
        

        
        clearFilters() {
            // Solo proceder si hay filtros activos
            if (!this.hasActiveFilters()) {
                console.log('No hay filtros activos para limpiar');
                return;
            }
            
            console.log('Limpiando filtros activos');
            
            // Limpiar filtros
            this.filters = {
                type: '',
                status: '',
                dateStart: '',
                dateEnd: ''
            };
            
            // Restaurar calendario al estado inicial sin filtros
            if (window.activityCalendar) {
                // Cargar actividades sin filtros
                window.activityCalendar.loadActivities();
                
                // Forzar re-renderizado del calendario para restaurar estado inicial
                window.activityCalendar.loadCalendar();
            }
            
            // Disparar eventos de limpieza
            this.$dispatch('filters-cleared');
            
            window.dispatchEvent(new CustomEvent('activity-filters-cleared'));
            
            // Mostrar notificación de filtros limpiados
            this.showClearFeedback();
        },
        
        hasActiveFilters() {
            return Object.values(this.filters).some(value => value !== '');
        },
        
        getActiveFilters() {
            const activeFilters = [];
            const labels = {
                type: {
                    'recreational': 'Recreativa',
                    'educational': 'Educativa', 
                    'cultural': 'Cultural',
                    'sports': 'Deportiva',
                    'social': 'Social'
                },
                status: {
                    'scheduled': 'Programada',
                    'in_progress': 'En Curso',
                    'completed': 'Completada',
                    'cancelled': 'Cancelada'
                }
            };
            
            if (this.filters.type) {
                activeFilters.push({
                    key: 'type',
                    label: labels.type[this.filters.type] || this.filters.type
                });
            }
            
            if (this.filters.status) {
                activeFilters.push({
                    key: 'status',
                    label: labels.status[this.filters.status] || this.filters.status
                });
            }
            
            if (this.filters.dateStart) {
                activeFilters.push({
                    key: 'dateStart',
                    label: `Desde: ${this.formatDate(this.filters.dateStart)}`
                });
            }
            
            if (this.filters.dateEnd) {
                activeFilters.push({
                    key: 'dateEnd',
                    label: `Hasta: ${this.formatDate(this.filters.dateEnd)}`
                });
            }
            
            return activeFilters;
        },
        
        removeFilter(key) {
            this.filters[key] = '';
            this.applyFilters();
        },
        
        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('es-ES', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit'
            });
        },
        
        showFilterFeedback() {
            // Crear feedback visual usando Tailwind
            const feedback = document.createElement('div');
            feedback.className = 'fixed top-4 right-4 bg-blue-500 text-white px-4 py-2 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300';
            feedback.innerHTML = `
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Filtros aplicados
                </div>
            `;
            document.body.appendChild(feedback);
            
            setTimeout(() => feedback.classList.remove('translate-x-full'), 100);
            setTimeout(() => {
                feedback.classList.add('translate-x-full');
                setTimeout(() => feedback.remove(), 300);
            }, 2000);
        },
        
        showClearFeedback() {
            // Crear feedback visual para limpieza de filtros
            const feedback = document.createElement('div');
            feedback.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300';
            feedback.innerHTML = `
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Filtros limpiados
                </div>
            `;
            document.body.appendChild(feedback);
            
            setTimeout(() => feedback.classList.remove('translate-x-full'), 100);
            setTimeout(() => {
                feedback.classList.add('translate-x-full');
                setTimeout(() => feedback.remove(), 300);
            }, 2000);
        },
        
        showError(message) {
            const toast = document.createElement('div');
            toast.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300';
            toast.textContent = message;
            document.body.appendChild(toast);
            
            setTimeout(() => toast.classList.remove('translate-x-full'), 100);
            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    }
}
 </script>