<!-- Follow-up Modal -->
<div id="follow-up-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg p-6 max-w-6xl w-full max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-medium text-gray-900">Seguimiento del Emparejamiento</h2>
            <button type="button" onclick="closeFollowUpModal()" class="text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Información del emparejamiento -->
        <div class="mb-6 bg-gray-50 p-4 rounded-lg">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="border-l-4 border-blue-500 pl-4">
                    <h4 class="font-medium text-blue-700">Buddy</h4>
                    <p id="followup-buddy-name" class="text-sm text-gray-600"></p>
                    <p id="followup-buddy-disability" class="text-xs text-gray-500"></p>
                </div>
                <div class="border-l-4 border-green-500 pl-4">
                    <h4 class="font-medium text-green-700">PeerBuddy</h4>
                    <p id="followup-peer-buddy-name" class="text-sm text-gray-600"></p>
                </div>
                <div class="border-l-4 border-purple-500 pl-4">
                    <h4 class="font-medium text-purple-700">Estado</h4>
                    <span id="followup-status" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"></span>
                    <p id="followup-dates" class="text-xs text-gray-500 mt-1"></p>
                </div>
            </div>
        </div>

        <!-- Tabs para Seguimiento y Asistencia -->
        <div class="mb-6">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <button type="button" onclick="switchTab('follow-up')" id="follow-up-tab" 
                            class="tab-button whitespace-nowrap border-b-2 border-blue-500 py-2 px-1 text-sm font-medium text-blue-600">
                        Evaluación
                    </button>
                    <button type="button" onclick="switchTab('attendance')" id="attendance-tab" 
                            class="tab-button whitespace-nowrap border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Asistencia
                    </button>
                    <button type="button" onclick="switchTab('history')" id="history-tab" 
                            class="tab-button whitespace-nowrap border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Monitoreo de Tutores
                    </button>
                </nav>
            </div>
        </div>

        <!-- Tab Content: Evaluación -->
        <div id="follow-up-content" class="tab-content">
            <form id="follow-up-form" method="POST">
                @csrf
                <input type="hidden" name="_method" id="follow-up-method" value="POST">
                
                <div class="bg-blue-50 p-4 rounded-lg mb-6">
                    <h3 class="text-md font-medium text-blue-800 mb-4">Evaluación del Emparejamiento</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Evaluaciones -->
                        <div class="space-y-4">
                            <div>
                                <label for="buddy_progress" class="block text-sm font-medium text-gray-700 mb-2">
                                    Progreso del Buddy (1-5)
                                    <span class="text-red-500">*</span>
                                </label>
                                <select id="buddy_progress" name="buddy_progress" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    <option value="">Seleccionar</option>
                                    <option value="1">1 - Muy Bajo</option>
                                    <option value="2">2 - Bajo</option>
                                    <option value="3">3 - Regular</option>
                                    <option value="4">4 - Bueno</option>
                                    <option value="5">5 - Excelente</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="peer_buddy_progress" class="block text-sm font-medium text-gray-700 mb-2">
                                    Progreso del PeerBuddy (1-5)
                                    <span class="text-red-500">*</span>
                                </label>
                                <select id="peer_buddy_progress" name="peer_buddy_progress" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    <option value="">Seleccionar</option>
                                    <option value="1">1 - Muy Bajo</option>
                                    <option value="2">2 - Bajo</option>
                                    <option value="3">3 - Regular</option>
                                    <option value="4">4 - Bueno</option>
                                    <option value="5">5 - Excelente</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="relationship_quality" class="block text-sm font-medium text-gray-700 mb-2">
                                    Calidad de la Relación (1-5)
                                    <span class="text-red-500">*</span>
                                </label>
                                <select id="relationship_quality" name="relationship_quality" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    <option value="">Seleccionar</option>
                                    <option value="1">1 - Muy Mala</option>
                                    <option value="2">2 - Mala</option>
                                    <option value="3">3 - Regular</option>
                                    <option value="4">4 - Buena</option>
                                    <option value="5">5 - Excelente</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Observaciones -->
                        <div class="space-y-4">
                            <div>
                                <label for="goals_achieved" class="block text-sm font-medium text-gray-700 mb-2">Objetivos Alcanzados</label>
                                <textarea id="goals_achieved" name="goals_achieved" rows="3" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Describe los objetivos que se han logrado..."></textarea>
                            </div>
                            
                            <div>
                                <label for="challenges_faced" class="block text-sm font-medium text-gray-700 mb-2">Desafíos Enfrentados</label>
                                <textarea id="challenges_faced" name="challenges_faced" rows="3" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Describe las dificultades encontradas..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button type="button" onclick="closeFollowUpModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                        Cancelar
                    </button>
                    <button type="submit" id="follow-up-submit-btn" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Guardar Seguimiento
                    </button>
                </div>
            </form>
        </div>

        <!-- Tab Content: Asistencia por Rango de Fechas -->
        <div id="attendance-content" class="tab-content hidden">
            <div class="bg-green-50 p-4 rounded-lg mb-6">
                <h3 class="text-md font-medium text-green-800 mb-4">Registro de Asistencia por Período</h3>
                
                <form id="attendance-form" method="POST">
                    <input type="hidden" name="attendance_dates" id="attendance_dates_hidden">
                    @csrf
                    
                    <!-- Rango de fechas -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label for="attendance_start_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Fecha de Inicio
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="attendance_start_date" name="start_date" required 
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        </div>
                        
                        <div>
                            <label for="attendance_end_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Fecha de Fin
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="attendance_end_date" name="end_date" required 
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        </div>
                    </div>

                    <!-- Información de participantes -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 bg-white p-4 rounded-lg border">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                            <div>
                                <p class="font-medium text-blue-700">Buddy</p>
                                <p id="buddy-name-display" class="text-sm text-gray-600"></p>
                            </div>
                        </div>
                        
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                            <div>
                                <p class="font-medium text-green-700">PeerBuddy</p>
                                <p id="peer-buddy-name-display" class="text-sm text-gray-600"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Controles rápidos -->
                    <div class="mb-4 p-3 bg-white rounded-lg border">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-medium text-gray-700">Controles Rápidos</h4>
                            <div class="flex space-x-2">
                                <button type="button" onclick="toggleAllAttendance('buddy', true)" 
                                        class="px-3 py-1 text-xs bg-blue-100 text-blue-700 rounded hover:bg-blue-200">
                                    Seleccionar Todos (Buddy)
                                </button>
                                <button type="button" onclick="toggleAllAttendance('peer_buddy', true)" 
                                        class="px-3 py-1 text-xs bg-green-100 text-green-700 rounded hover:bg-green-200">
                                    Seleccionar Todos (PeerBuddy)
                                </button>
                                <button type="button" onclick="toggleAllAttendance('buddy', false); toggleAllAttendance('peer_buddy', false)" 
                                        class="px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded hover:bg-gray-200">
                                    Limpiar Todo
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Container para los días de asistencia -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-sm font-medium text-gray-700">Días del Período</h4>
                        </div>
                        <div id="attendance-days-container" class="bg-white rounded-lg border">
                            <div class="text-center py-8 text-gray-500">
                                Selecciona las fechas de inicio y fin para generar los días
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        <button type="submit" id="attendance-submit-btn" 
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                            Guardar Asistencia
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div id="monitoring-content" class="tab-content hidden">
            <div class="bg-purple-50 p-4 rounded-lg mb-6">
                <h3 class="text-md font-medium text-purple-800 mb-2">Monitoreo Mensual de Tutores</h3>
                <p class="text-sm text-purple-600">Realiza el seguimiento mensual de los tutores en el programa</p>
            </div>
            
            <!-- Incluir el componente de monitoreo mensual -->
            @include('components.friendships.monthly-monitoring')
        </div>
    </div>
</div>

<script src="{{ asset('js/follow-up.js') }}"></script>