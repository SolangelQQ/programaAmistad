<div class="overflow-x-auto">
    <table class="w-full rounded-lg h-full items-center">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Buddy</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peerbuddy</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Inicio</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($friendships as $friendship)
                <tr>
                    <td class="p-4 whitespace-nowrap text-sm font-medium text-gray-900">A{{ str_pad($friendship->id, 3, '0', STR_PAD_LEFT) }}</td>
                    <td class="p-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <img class="h-6 w-6 rounded-full object-cover" src="https://i.pravatar.cc/300?u={{ $friendship->buddy->email ?? $friendship->buddy->id }}" alt="{{ $friendship->buddy->full_name }}">
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $friendship->buddy->full_name }}</div>
                                <div class="text-sm text-gray-500">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $friendship->buddy->disability ?? 'N/A' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="p-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <img class="h-6 w-6 rounded-full object-cover" src="https://i.pravatar.cc/300?u={{ $friendship->peerBuddy->email ?? $friendship->peerBuddy->id }}" alt="{{ $friendship->peerBuddy->full_name }}">
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $friendship->peerBuddy->full_name }}</div>
                                <div class="text-sm text-gray-500">{{ $friendship->peerBuddy->age }} años</div>
                            </div>
                        </div>
                    </td>
                    <td class="p-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $friendship->start_date ? \Carbon\Carbon::parse($friendship->start_date)->format('d/m/Y') : 'N/A' }}
                    </td>
                    <td class="p-4 whitespace-nowrap">
                        @php
                            $statusClasses = [
                                'Emparejado' => 'bg-green-100 text-green-800',
                                'Inactivo' => 'bg-yellow-100 text-yellow-800',
                                'Finalizado' => 'bg-gray-100 text-gray-800'
                            ];
                        @endphp
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses[$friendship->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $friendship->status }}
                        </span>
                    </td>
                    <td class="p-4 whitespace-nowrap text-sm font-medium">
                        <!-- Seguimiento -->
                        <button type="button" onclick="openFollowUpModal({{ $friendship->id }})" 
                                class="text-purple-600 hover:text-purple-900 mr-3" title="Seguimiento">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        
                        <!-- Ver Detalles -->
                        <button type="button" onclick="showFriendshipDetails({{ $friendship->id }})" 
                                class="text-green-600 hover:text-green-900 mr-3" title="Ver Detalles">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        
                        <!-- Editar -->
                        <button type="button" onclick="openEditFriendshipModal({{ $friendship->id }}, '{{ $friendship->status }}', '{{ $friendship->start_date }}', '{{ $friendship->end_date }}', `{{ $friendship->notes }}`)" 
                                class="text-blue-600 hover:text-blue-900 mr-3" title="Editar">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                            </svg>
                        </button>
                        
                        <!-- Eliminar -->
                        <button type="button" onclick="confirmDelete({{ $friendship->id }})" 
                                class="text-red-600 hover:text-red-900" title="Eliminar">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                        No hay amistades registradas. <a href="#" onclick="openNewFriendshipModal()" class="text-blue-600 hover:text-blue-900">Crear nuevo emparejamiento</a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@include('modals.friendships.view')
<!-- @include('modals.friendships.tracking') -->
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

<div class="mt-4">
    {{ $friendships->links() }}
</div>



<script>
    // ========================================
// JAVASCRIPT UNIFICADO PARA FRIENDSHIPS
// ========================================

document.addEventListener('DOMContentLoaded', function() {
    // Inicialización
    initializeTabs();
    setupEventListeners();
});

// ========================================
// SISTEMA DE PESTAÑAS
// ========================================

function initializeTabs() {
    setTimeout(() => {
        switchToTab('friendships-section');
    }, 100);
}

function switchToTab(targetSectionId) {
    console.log('Switching to tab:', targetSectionId);
    
    const allSections = ['friendships-section', 'activities-section', 'buddies-section'];
    
    // Ocultar todas las secciones
    allSections.forEach(sectionId => {
        const section = document.getElementById(sectionId);
        if (section) {
            section.classList.add('hidden');
            console.log('Hiding section:', sectionId);
        }
    });
    
    // Mostrar la sección seleccionada
    const targetSection = document.getElementById(targetSectionId);
    if (targetSection) {
        targetSection.classList.remove('hidden');
        console.log('Showing section:', targetSectionId);
    } else {
        console.error('Section not found:', targetSectionId);
    }
    
    // Actualizar el estado visual de las pestañas
    document.querySelectorAll('.tab-link').forEach(tab => {
        tab.classList.remove('border-indigo-500', 'text-gray-900');
        tab.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Activar la pestaña correspondiente
    const activeTab = document.querySelector(`[data-target="${targetSectionId}"]`);
    if (activeTab) {
        activeTab.classList.remove('border-transparent', 'text-gray-500');
        activeTab.classList.add('border-indigo-500', 'text-gray-900');
    }
}

function setupEventListeners() {
    // Event listeners para las pestañas
    document.querySelectorAll('.tab-link').forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('data-target');
            console.log('Tab clicked, target:', targetId);
            switchToTab(targetId);
        });
    });
    
    // Validación de formularios
    setupFormValidation();
}

// ========================================
// MODAL DE DETALLES DE EMPAREJAMIENTO
// ========================================

async function showFriendshipDetails(friendshipId) {
    const modal = document.getElementById('view-friendship-modal');
    
    try {
        console.log('Cargando datos para emparejamiento ID:', friendshipId);
        
        // Mostrar modal inmediatamente
        modal.classList.remove('hidden');
        
        // Mostrar indicador de carga
        const modalContent = document.getElementById('modal-content');
        const originalContent = modalContent.innerHTML;
        
        modalContent.innerHTML = `
            <div class="flex justify-center items-center p-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <span class="ml-2">Cargando detalles del emparejamiento...</span>
            </div>
        `;

        // Hacer la petición AJAX
        const response = await fetch(`/friendships/${friendshipId}/show`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        });

        if (!response.ok) {
            const errorData = await response.text();
            console.error('Error en la respuesta:', response.status, errorData);
            throw new Error(`Error ${response.status}: ${response.statusText}`);
        }

        const data = await response.json();
        console.log('Datos recibidos:', data);

        // Verificar que los datos sean válidos
        if (!data || !data.friendship) {
            throw new Error('Los datos recibidos no son válidos');
        }

        // Restaurar contenido original del modal
        modalContent.innerHTML = originalContent;
        
        // Poblar el modal con los datos
        populateModalData(data);
        
        // Manejar la información de seguimiento
        handleFollowUpDisplay(data);
        handleAttendanceDisplay(data);

    } catch (error) {
        console.error('Error completo:', error);
        
        // Mostrar error en el contenido del modal
        const modalContent = document.getElementById('modal-content');
        modalContent.innerHTML = `
            <div class="p-6 text-center">
                <div class="flex justify-between items-center mb-5">
                    <h2 class="text-xl font-bold text-gray-900">Error</h2>
                    <button type="button" onclick="closeViewFriendshipModal()" class="text-gray-400 hover:text-gray-500 transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="text-red-600 mb-4">
                    <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Error al cargar los datos</h3>
                <p class="text-gray-600 mb-4">No se pudieron cargar los detalles del emparejamiento.</p>
                <p class="text-sm text-gray-500 mb-4">Error: ${error.message}</p>
                <button onclick="closeViewFriendshipModal()" 
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                    Cerrar
                </button>
            </div>
        `;
    }
}

function populateModalData(data) {
    try {
        console.log('Poblando datos del modal:', data);

        // Información del Buddy
        const buddyData = data.buddy || {};
        document.getElementById('view_buddy_name').textContent = 
            `${buddyData.first_name || ''} ${buddyData.last_name || ''}`.trim() || 'N/A';
        document.getElementById('view_buddy_disability').textContent = 
            buddyData.disability || 'No especificada';
        document.getElementById('view_buddy_age').textContent = 
            buddyData.age ? `${buddyData.age} años` : 'N/A';
        document.getElementById('view_buddy_ci').textContent = 
            buddyData.ci || 'N/A';
        document.getElementById('view_buddy_phone').textContent = 
            buddyData.phone || 'N/A';
        document.getElementById('view_buddy_email').textContent = 
            buddyData.email || 'No especificado';
        document.getElementById('view_buddy_address').textContent = 
            buddyData.address || 'N/A';
        
        // Información del PeerBuddy
        const peerBuddyData = data.peerBuddy || {};
        document.getElementById('view_peerbuddy_name').textContent = 
            `${peerBuddyData.first_name || ''} ${peerBuddyData.last_name || ''}`.trim() || 'N/A';
        document.getElementById('view_peerbuddy_age').textContent = 
            peerBuddyData.age ? `${peerBuddyData.age} años` : 'N/A';
        document.getElementById('view_peerbuddy_ci').textContent = 
            peerBuddyData.ci || 'N/A';
        document.getElementById('view_peerbuddy_phone').textContent = 
            peerBuddyData.phone || 'N/A';
        document.getElementById('view_peerbuddy_email').textContent = 
            peerBuddyData.email || 'No especificado';
        document.getElementById('view_peerbuddy_address').textContent = 
            peerBuddyData.address || 'N/A';
        
        // Información de líderes
        const buddyLeader = data.buddyLeader || {};
        const peerBuddyLeader = data.peerBuddyLeader || {};
        
        document.getElementById('view_buddy_leader_name').textContent = 
            buddyLeader.name || 'No asignado';
        document.getElementById('view_buddy_leader_email').textContent = 
            buddyLeader.email || 'N/A';
        document.getElementById('view_peerbuddy_leader_name').textContent = 
            peerBuddyLeader.name || 'No asignado';
        document.getElementById('view_peerbuddy_leader_email').textContent = 
            peerBuddyLeader.email || 'N/A';
        
        // Información del emparejamiento
        const friendshipData = data.friendship || {};
        document.getElementById('view_friendship_id').textContent = 
            friendshipData.id || 'N/A';
        document.getElementById('view_start_date').textContent = 
            friendshipData.start_date ? new Date(friendshipData.start_date).toLocaleDateString('es-ES') : 'N/A';
        document.getElementById('view_end_date').textContent = 
            friendshipData.end_date ? new Date(friendshipData.end_date).toLocaleDateString('es-ES') : 'No definida';
        document.getElementById('view_notes').textContent = 
            friendshipData.notes || 'Sin notas adicionales';
        
        // Status badge
        const statusBadge = document.getElementById('view_status_badge');
        if (statusBadge) {
            statusBadge.textContent = friendshipData.status || 'N/A';
            statusBadge.className = `px-3 py-1 text-sm font-semibold rounded-full ${getStatusBadgeClass(friendshipData.status)}`;
        }

        console.log('Datos poblados correctamente');
    } catch (error) {
        console.error('Error al poblar los datos:', error);
    }
}

function getStatusBadgeClass(status) {
    if (!status) return 'bg-gray-100 text-gray-800';
    
    switch (status) {
        case 'Emparejado':
            return 'bg-green-100 text-green-800';
        case 'Inactivo':
            return 'bg-yellow-100 text-yellow-800';
        case 'Finalizado':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
}

function closeViewFriendshipModal() {
    document.getElementById('view-friendship-modal').classList.add('hidden');
}

// ========================================
// SISTEMA DE SEGUIMIENTO
// ========================================

function handleFollowUpDisplay(data) {
    const statusIndicator = document.getElementById('follow_up_status_indicator');
    const followUpContent = document.getElementById('follow_up_content');
    const lastFollowUpSection = document.getElementById('last_follow_up_section');
    
    if (!statusIndicator || !followUpContent || !lastFollowUpSection) {
        console.error('Elementos de seguimiento no encontrados');
        return;
    }
    
    try {
        if (data.hasFollowUps && data.followUps && data.followUps.length > 0) {
            console.log('Mostrando información de seguimientos:', data.followUps);
            
            const latestFollowUp = data.followUps[0];
            
            // Indicador de estado - Con seguimiento
            statusIndicator.innerHTML = `
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    Seguimiento Realizado
                </span>
            `;
            
            // Contenido del seguimiento
            followUpContent.innerHTML = `
                <div class="text-center">
                    <p class="text-indigo-800 font-medium mb-2">Se han realizado ${data.followUps.length} seguimiento(s)</p>
                    <p class="text-sm text-indigo-600">Último seguimiento: ${new Date(latestFollowUp.created_at).toLocaleDateString('es-ES')}</p>
                </div>
            `;
            
            // Mostrar detalles del último seguimiento
            showLastFollowUpDetails(latestFollowUp);
            lastFollowUpSection.classList.remove('hidden');
            
        } else {
            console.log('No hay seguimientos para mostrar');
            
            // Sin seguimientos
            statusIndicator.innerHTML = `
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    Sin Seguimiento
                </span>
            `;
            
            followUpContent.innerHTML = `
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-indigo-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <p class="text-indigo-800 font-medium mb-2">No se ha realizado seguimiento</p>
                    <p class="text-sm text-indigo-600">Este emparejamiento aún no tiene registros de seguimiento</p>
                </div>
            `;
            
            lastFollowUpSection.classList.add('hidden');
        }
    } catch (error) {
        console.error('Error al manejar información de seguimiento:', error);
    }
}

// Mapeo de valores numéricos a texto y colores
const progressMapping = {
    5: { text: 'Excelente', color: 'text-green-600 bg-green-50' },
    4: { text: 'Bueno', color: 'text-blue-600 bg-blue-50' },
    3: { text: 'Regular', color: 'text-yellow-600 bg-yellow-50' },
    2: { text: 'Bajo', color: 'text-orange-600 bg-orange-50' },
    1: { text: 'Muy Bajo', color: 'text-red-600 bg-red-50' },
    0: { text: 'No evaluado', color: 'text-gray-600 bg-gray-50' }
};

function createProgressBadge(value) {
    const progress = progressMapping[value] || progressMapping[0];
    return `<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${progress.color}">${progress.text}</span>`;
}

function showLastFollowUpDetails(followUp) {
    try {
        console.log('Mostrando detalles del seguimiento:', followUp);

        // Mostrar badges con texto y colores para los campos de progreso
        document.getElementById('buddy_progress_stars').innerHTML = createProgressBadge(followUp.buddy_progress);
        document.getElementById('peer_buddy_progress_stars').innerHTML = createProgressBadge(followUp.peer_buddy_progress);
        document.getElementById('relationship_quality_stars').innerHTML = createProgressBadge(followUp.relationship_quality);
        
        // Llenar campos de texto
        document.getElementById('goals_achieved').textContent = followUp.goals_achieved || 'No especificados';
        document.getElementById('challenges_faced').textContent = followUp.challenges_faced || 'No especificados';
        document.getElementById('recommendations').textContent = followUp.recommendations || 'No especificadas';
        document.getElementById('next_steps').textContent = followUp.next_steps || 'No especificados';
        
        // Información del usuario y fecha
        document.getElementById('follow_up_user').textContent = followUp.user ? followUp.user.name : 'Usuario desconocido';
        document.getElementById('follow_up_date').textContent = new Date(followUp.created_at).toLocaleDateString('es-ES');
        
        // Próximo seguimiento
        const nextFollowUpInfo = document.getElementById('next_follow_up_info');
        if (followUp.next_follow_up_date) {
            nextFollowUpInfo.innerHTML = `
                <p class="text-sm text-blue-600">
                    <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Próximo seguimiento programado: ${new Date(followUp.next_follow_up_date).toLocaleDateString('es-ES')}
                </p>
            `;
        } else {
            nextFollowUpInfo.innerHTML = '';
        }
    } catch (error) {
        console.error('Error al mostrar detalles del seguimiento:', error);
    }
}

// ========================================
// MODAL DE SEGUIMIENTO
// ========================================

function openFollowUpModal(friendshipId) {
    // Implementar lógica para abrir modal de seguimiento
    // Esta función podría cargar datos específicos del seguimiento
    console.log('Abriendo modal de seguimiento para:', friendshipId);
    document.getElementById('follow-up-modal').classList.remove('hidden');
}

function closeFollowUpModal() {
    document.getElementById('follow-up-modal').classList.add('hidden');
}

function switchTab(tabName) {
    // Ocultar todos los contenidos de pestañas
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remover clase activa de todas las pestañas
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('border-blue-500', 'text-blue-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Mostrar el contenido de la pestaña seleccionada
    const targetContent = document.getElementById(`${tabName}-content`);
    if (targetContent) {
        targetContent.classList.remove('hidden');
    }
    
    // Activar la pestaña seleccionada
    const targetTab = document.getElementById(`${tabName}-tab`);
    if (targetTab) {
        targetTab.classList.remove('border-transparent', 'text-gray-500');
        targetTab.classList.add('border-blue-500', 'text-blue-600');
    }
}

// ========================================
// FUNCIONES DE ASISTENCIA
// ========================================

function handleAttendanceDisplay(data) {
    // Implementar lógica para mostrar información de asistencia
    console.log('Manejando información de asistencia:', data);
}

function toggleAllAttendance(type, checked) {
    // Implementar lógica para seleccionar/deseleccionar asistencia
    console.log('Toggling attendance for', type, 'to', checked);
}

// ========================================
// GESTIÓN DE MODALES
// ========================================

// Funciones para abrir/cerrar modales
function openNewFriendshipModal() {
    document.getElementById('new-friendship-modal').classList.remove('hidden');
}

function closeNewFriendshipModal() {
    document.getElementById('new-friendship-modal').classList.add('hidden');
}

function openEditFriendshipModal(friendshipId, status, startDate, endDate, notes) {
    const form = document.getElementById('edit-friendship-form');
    form.action = `/friendships/${friendshipId}`;
    
    document.getElementById('edit_start_date').value = startDate || '';
    document.getElementById('edit_end_date').value = endDate || '';
    document.getElementById('edit_status').value = status || '';
    document.getElementById('edit_notes').value = notes || '';
    
    document.getElementById('edit-friendship-modal').classList.remove('hidden');
}

function closeEditFriendshipModal() {
    document.getElementById('edit-friendship-modal').classList.add('hidden');
}

// ========================================
// CONFIRMACIONES DE ELIMINACIÓN
// ========================================

function confirmDelete(friendshipId) {
    const form = document.getElementById('delete-friendship-form');
    form.action = `/friendships/${friendshipId}`;
    document.getElementById('delete-confirmation-modal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('delete-confirmation-modal').classList.add('hidden');
}

function confirmDeleteBuddy(buddyId) {
    const form = document.getElementById('delete-buddy-form');
    form.action = `/buddies/${buddyId}`;
    document.getElementById('delete-buddy-modal').classList.remove('hidden');
}

function closeDeleteBuddyModal() {
    document.getElementById('delete-buddy-modal').classList.add('hidden');
}

// ========================================
// VALIDACIÓN DE FORMULARIOS
// ========================================

function setupFormValidation() {
    const newFriendshipForm = document.getElementById('new-friendship-form');
    if (newFriendshipForm) {
        newFriendshipForm.addEventListener('submit', function(e) {
            const buddyId = document.getElementById('buddy_id').value;
            const peerBuddyId = document.getElementById('peer_buddy_id').value;
            
            if (!buddyId || !peerBuddyId) {
                e.preventDefault();
                alert('Por favor selecciona un Buddy y un PeerBuddy');
                return;
            }
            
            if (buddyId === peerBuddyId) {
                e.preventDefault();
                alert('El Buddy y el PeerBuddy no pueden ser la misma persona');
                return;
            }
        });
    }
}

// ========================================
// FUNCIONES AUXILIARES
// ========================================

function openTrackingFriendshipModal(friendshipId) {
    fetch(`/friendships/${friendshipId}`)
        .then(response => response.json())
        .then(data => {
            populateTrackingModal(data.friendship);
            document.getElementById('tracking-friendship-modal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar los datos del emparejamiento');
        });
}

function closeTrackingFriendshipModal() {
    document.getElementById('tracking-friendship-modal').classList.add('hidden');
    document.getElementById('tracking-friendship-form').reset();
    switchTab('attendance');
}

function populateTrackingModal(friendship) {
    // Implementar lógica para poblar el modal de tracking
    console.log('Poblando modal de tracking:', friendship);
}

// ========================================
// EXPOSICIÓN DE FUNCIONES GLOBALES
// ========================================

// Hacer las funciones accesibles globalmente para los onclick en HTML
window.showFriendshipDetails = showFriendshipDetails;
window.closeViewFriendshipModal = closeViewFriendshipModal;
window.openFollowUpModal = openFollowUpModal;
window.closeFollowUpModal = closeFollowUpModal;
window.switchTab = switchTab;
window.openNewFriendshipModal = openNewFriendshipModal;
window.closeNewFriendshipModal = closeNewFriendshipModal;
window.openEditFriendshipModal = openEditFriendshipModal;
window.closeEditFriendshipModal = closeEditFriendshipModal;
window.confirmDelete = confirmDelete;
window.closeDeleteModal = closeDeleteModal;
window.confirmDeleteBuddy = confirmDeleteBuddy;
window.closeDeleteBuddyModal = closeDeleteBuddyModal;
window.openTrackingFriendshipModal = openTrackingFriendshipModal;
window.closeTrackingFriendshipModal = closeTrackingFriendshipModal;
window.toggleAllAttendance = toggleAllAttendance;
</script>