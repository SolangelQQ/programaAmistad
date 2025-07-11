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

<script src="js/activity-filters.js"></script>