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
<script src="{{ asset('js/general.js') }}"></script>
@endpush