
{{-- reports/tabs/actividades.blade.php --}}
<div>
    <!-- Header -->
    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
        <h2 class="text-xl font-semibold text-gray-900">Reporte de Participación en Actividades</h2>
        <p class="text-gray-600 mt-1">Análisis detallado de las actividades realizadas y su participación</p>
    </div>
    
    <!-- Loading State -->
    <div x-show="loading" class="text-center py-8">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto"></div>
        <p class="mt-4 text-gray-600">Cargando datos de actividades...</p>
    </div>
    
    <!-- Content when data is loaded -->
    <div x-show="!loading && reportData.actividades">
        <!-- Estadísticas de Actividades - Mejoradas para responsividad -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
            <!-- Total Actividades -->
            <div class="bg-blue-50 p-4 sm:p-6 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-calendar-check text-blue-600 text-xl sm:text-2xl mr-3 flex-shrink-0"></i>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs sm:text-sm text-gray-600 truncate">Total Actividades hasta hoy</p>
                        <p class="text-xl sm:text-2xl font-bold text-gray-900" x-text="reportData.actividades?.statistics?.totalActivities || 0"></p>
                    </div>
                </div>
            </div>
            
            <!-- Completadas -->
            <div class="bg-green-50 p-4 sm:p-6 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-600 text-xl sm:text-2xl mr-3 flex-shrink-0"></i>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs sm:text-sm text-gray-600 truncate">Completadas</p>
                        <p class="text-xl sm:text-2xl font-bold text-gray-900" x-text="reportData.actividades?.statistics?.completedActivities || 0"></p>
                    </div>
                </div>
            </div>
            
            <!-- Programadas -->
            <div class="bg-yellow-50 p-4 sm:p-6 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-clock text-yellow-600 text-xl sm:text-2xl mr-3 flex-shrink-0"></i>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs sm:text-sm text-gray-600 truncate">Programadas</p>
                        <p class="text-xl sm:text-2xl font-bold text-gray-900" x-text="reportData.actividades?.statistics?.scheduledActivities || 0"></p>
                    </div>
                </div>
            </div>
            
            <!-- Canceladas -->
            <div class="bg-red-50 p-4 sm:p-6 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-times-circle text-red-600 text-xl sm:text-2xl mr-3 flex-shrink-0"></i>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs sm:text-sm text-gray-600 truncate">Canceladas</p>
                        <p class="text-xl sm:text-2xl font-bold text-gray-900" x-text="reportData.actividades?.statistics?.cancelledActivities || 0"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tasa de Completación -->
        <div class="bg-white p-6 rounded-lg border border-gray-200 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Tasa de Completación</h3>
                    <p class="text-sm text-gray-600">Porcentaje de actividades completadas exitosamente</p>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold text-blue-600" x-text="(reportData.actividades?.statistics?.completionRate || 0) + '%'"></div>
                    <div class="text-sm text-gray-500">del total</div>
                </div>
            </div>
            <div class="mt-4 bg-gray-200 rounded-full h-2">
                <div class="bg-blue-600 h-2 rounded-full transition-all duration-500" 
                     :style="`width: ${reportData.actividades?.statistics?.completionRate || 0}%`"></div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Participación por Tipo -->
            <div class="bg-white p-6 border border-gray-200 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Participación por Tipo de Actividad</h3>
                <div class="h-64 flex items-center justify-center" x-show="!reportData.actividades?.charts?.participationByType?.length">
                    <p class="text-gray-500">No hay datos de tipos de actividad</p>
                </div>
                <div class="h-64" x-show="reportData.actividades?.charts?.participationByType?.length">
                    <canvas x-ref="participationChart"></canvas>
                </div>
            </div>

            <!-- Estado de Actividades -->
            <div class="bg-white p-6 border border-gray-200 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Estado de Actividades</h3>
                <div class="h-64 flex items-center justify-center" x-show="!reportData.actividades?.charts?.statusComparison?.length">
                    <p class="text-gray-500">No hay datos de estados</p>
                </div>
                <div class="h-64" x-show="reportData.actividades?.charts?.statusComparison?.length">
                    <canvas x-ref="statusChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Tendencia Mensual -->
        <div class="bg-white p-6 border border-gray-200 rounded-lg mb-8">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Tendencia Mensual de Actividades</h3>
            <div class="h-64 flex items-center justify-center" x-show="!reportData.actividades?.charts?.monthlyTrend?.length">
                <p class="text-gray-500">No hay datos de tendencia mensual</p>
            </div>
            <div class="h-64" x-show="reportData.actividades?.charts?.monthlyTrend?.length">
                <canvas x-ref="monthlyChart"></canvas>
            </div>
        </div>

        <!-- Tabla de Actividades Detallada -->
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-medium text-gray-900">Detalle de Actividades Recientes</h3>
                <p class="text-sm text-gray-600 mt-1" x-text="`Total de actividades hasta hoy: ${reportData.actividades?.activities?.length || 0}.`"></p>
            </div>
            
            <!-- Filtros de la tabla -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-64">
                        <input type="text" 
                               x-model="activityFilter" 
                               placeholder="Buscar actividad..." 
                               class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <select x-model="statusFilter" class="border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Todos los estados</option>
                            <option value="completed">Completadas</option>
                            <option value="scheduled">Programadas</option>
                            <option value="cancelled">Canceladas</option>
                            <option value="in_progress">En Progreso</option>
                        </select>
                    </div>
                    <div>
                        <select x-model="typeFilter" class="border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Todos los tipos</option>
                            <option value="recreational">Recreativa</option>
                            <option value="educational">Educativa</option>
                            <option value="cultural">Cultural</option>
                            <option value="sports">Deportiva</option>
                            <option value="social">Social</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Empty state -->
            <div x-show="!reportData.actividades?.activities?.length" class="px-6 py-12 text-center">
                <i class="fas fa-calendar-times text-gray-400 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No hay actividades</h3>
                <p class="text-gray-500">No se encontraron actividades en el período seleccionado.</p>
            </div>

            <!-- Table with data -->
            <div x-show="reportData.actividades?.activities?.length" class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actividad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ubicación</th>
                            <!-- <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Participantes</th> -->
                            <!-- <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duración</th> -->
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <!-- Show message when no filtered results -->
                        <tr x-show="filteredActivities.length === 0 && reportData.actividades?.activities?.length > 0">
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                No se encontraron actividades que coincidan con los filtros aplicados.
                            </td>
                        </tr>
                        
                        <!-- Activities rows -->
                        <!-- Activities rows -->
                        <template x-for="activity in paginatedActivities" :key="activity.id">
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900" x-text="activity.title || 'Sin título'"></div>
                                    <div class="text-xs text-gray-500" x-text="activity.description || ''"></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="activity.formatted_date || activity.date"></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                        :class="getActivityTypeClass(activity.type)"
                                        x-text="getActivityTypeText(activity.type)">
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                        :class="getActivityStatusClass(activity.status)"
                                        x-text="getActivityStatusText(activity.status)">
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="activity.location || 'No especificada'"></td>
                                <!-- <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="activity.participants_count || 0"></td> -->
                                <!-- <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="activity.duration || 'N/A'"></td> -->
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div x-show="filteredActivities.length > itemsPerPage" class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Mostrando 
                        <span x-text="Math.min((currentPage - 1) * itemsPerPage + 1, filteredActivities.length)"></span>
                        a 
                        <span x-text="Math.min(currentPage * itemsPerPage, filteredActivities.length)"></span> 
                        de 
                        <span x-text="filteredActivities.length"></span> actividades
                    </div>
                    <div class="flex space-x-2">
                        <button @click="currentPage = Math.max(1, currentPage - 1)" 
                                :disabled="currentPage === 1"
                                class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                            Anterior
                        </button>
                        <span class="px-3 py-1 text-sm text-gray-700" x-text="`Página ${currentPage} de ${Math.ceil(filteredActivities.length / itemsPerPage)}`"></span>
                        <button @click="currentPage = Math.min(Math.ceil(filteredActivities.length / itemsPerPage), currentPage + 1)" 
                                :disabled="currentPage * itemsPerPage >= filteredActivities.length"
                                class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                            Siguiente
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumen de Actividades Populares -->
        <div class="mt-8 bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Actividades Más Populares</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <template x-for="popular in reportData.actividades?.popular || []" :key="popular.id">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-medium text-gray-900" x-text="popular.title"></h4>
                                <p class="text-sm text-gray-600" x-text="popular.type_label"></p>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-blue-600" x-text="popular.participants_count"></div>
                                <div class="text-xs text-gray-500">participantes</div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
            <div x-show="!reportData.actividades?.popular?.length" class="text-center text-gray-500 py-4">
                No hay datos de actividades populares disponibles
            </div>
        </div>
    </div>

    <!-- Error state -->
    <div x-show="!loading && !reportData.actividades" class="text-center py-12">
        <i class="fas fa-exclamation-triangle text-yellow-400 text-4xl mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Error al cargar datos</h3>
        <p class="text-gray-500 mb-4">No se pudieron cargar los datos de actividades. Por favor, intenta nuevamente.</p>
        <button @click="loadReportData()" 
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <i class="fas fa-refresh mr-2"></i>
            Reintentar
        </button>
    </div>
</div>