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
<!-- @include('modals.friendships.view') -->
 <div id="view-friendship-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 max-w-6xl max-h-[90vh] overflow-y-auto">
        <div id="modal-content">
            
            @include('components.friendships.modal-header')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-6">
                @include('components.friendships.buddy-info-card')
                @include('components.friendships.peerbuddy-info-card')
            </div>
            
            @include('components.friendships.leaders-info')
            
            @include('components.friendships.friendship-details')
            
            @include('components.friendships.follow-up-section')
            
            @include('components.friendships.attendance-section')
            
            @include('components.friendships.modal-actions')
            
        </div>
    </div>
</div>

<!-- @include('modals.friendships.tracking') -->
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
  
      let currentFriendshipId = null;
let currentFollowUpId = null;

async function showFriendshipDetails(friendshipId) {
    const modal = document.getElementById('view-friendship-modal');
    
    try {
        
        modal.classList.remove('hidden');
        
        // Mostrar indicador de carga solo en el contenido, no reemplazar todo el modal
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
        
        // Solo mostrar error en el contenido del modal, mantener la estructura
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

// Función para poblar los datos en el modal (SIN CAMBIOS - ya está bien)
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

// Función para convertir valor numérico a badge con texto y color
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

// Función para restaurar el contenido original del modal
function restoreModalContent() {
    const modal = document.getElementById('view-friendship-modal');
    // Esto restaura el HTML original del modal que definiste en tu blade
    modal.innerHTML = `
        <div class="bg-white rounded-lg shadow-xl p-6 max-w-6xl max-h-[90vh] overflow-y-auto">
            <!-- Aquí va todo el contenido HTML original de tu modal -->
            ${document.getElementById('view-friendship-modal').innerHTML}
        </div>
    `;
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

// Función para abrir el modal de seguimiento
function openTrackingFriendshipModal(friendshipId) {
    // Hacer petición AJAX para obtener los datos del friendship
    fetch(`/friendships/${friendshipId}`)
        .then(response => response.json())
        .then(data => {
            // Poblar el modal con los datos
            populateTrackingModal(data.friendship);
            
            // Mostrar el modal
            document.getElementById('tracking-friendship-modal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar los datos del emparejamiento');
        });
}

// Función para cerrar el modal de seguimiento
function closeTrackingFriendshipModal() {
    document.getElementById('tracking-friendship-modal').classList.add('hidden');
    // Limpiar el formulario
    document.getElementById('tracking-friendship-form').reset();
    // Volver a la primera pestaña por defecto
    switchTab('attendance');
}

 src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"
// Variable para almacenar el contenido original del modal
let originalModalContent = null;

// Función principal para mostrar detalles del emparejamiento
async function showFriendshipDetails(friendshipId) {
    const modal = document.getElementById('view-friendship-modal');
    const modalContent = document.getElementById('modal-content');
    
    try {
        // Mostrar loading
        modal.classList.remove('hidden');
        modalContent.innerHTML = `
            <div class="flex justify-center items-center p-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <span class="ml-2">Cargando detalles del emparejamiento...</span>
            </div>
        `;

        // Hacer la petición
        const response = await fetch(`/friendships/${friendshipId}/show`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) throw new Error('Error al cargar los datos');

        const data = await response.json();
        
        // Restaurar contenido original
        modalContent.innerHTML = originalModalContent;
        
        // Poblar los datos
        populateModalData(data);
        handleFollowUpDisplay(data);
        handleAttendanceDisplay(data);

    } catch (error) {
        modalContent.innerHTML = `
            <div class="p-6 text-center">
                <div class="text-red-600 mb-4">
                    <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Error al cargar los datos</h3>
                <p class="text-gray-600 mb-4">${error.message}</p>
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

        // Manejo de asistencia
        if (data.attendanceRecords && data.attendanceRecords.length > 0) {
            displayAttendanceRecords(data.attendanceRecords);
        } else {
            const attendanceSection = document.getElementById('attendance-section');
            if (attendanceSection) {
                attendanceSection.classList.add('hidden');
            }
        }
    } catch (error) {
        console.error('Error al poblar los datos:', error);
    }
}

function getStatusBadgeClass(status) {
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

// Función mejorada para manejar la visualización de asistencias
function generateAttendanceTable(records) {
    if (!records || records.length === 0) {
        return '<p class="text-gray-500">No hay registros de asistencia recientes</p>';
    }

    return `
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Buddy</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">PeerBuddy</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notas</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    ${records.map(record => `
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                ${new Date(record.date).toLocaleDateString('es-ES')}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                ${record.buddy_attended ? '✅ Asistió' : '❌ Faltó'}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                ${record.peer_buddy_attended ? '✅ Asistió' : '❌ Faltó'}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                ${record.notes || 'Sin notas'}
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;
}

// Función alternativa con día de la semana
function formatDateWithDay(date) {
    try {
        if (typeof date === 'string') {
            date = new Date(date);
        }
        
        if (!(date instanceof Date) || isNaN(date.getTime())) {
            return 'Fecha inválida';
        }
        
        const dayName = date.toLocaleDateString('es-ES', { weekday: 'long' });
        const dateString = date.toLocaleDateString('es-ES', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
        
        return `${dayName}, ${dateString}`;
        
    } catch (error) {
        return 'Error en fecha';
    }
}

// Si quieres también corregir la función formatDate original que está causando el problema:
function formatDate(date) {
    return formatDateCorrect(date);
}

// Función adicional para mostrar solo el día y mes (útil para rangos del mismo año)
function formatDateShort(date) {
    try {
        if (typeof date === 'string') {
            date = new Date(date);
        }
        
        if (!(date instanceof Date) || isNaN(date.getTime())) {
            return 'Fecha inválida';
        }
        
        // Formato: "13 jun"
        return date.toLocaleDateString('es-ES', {
            day: 'numeric',
            month: 'short'
        });
        
    } catch (error) {
        return 'Error';
    }
}


// Función mejorada para manejar la visualización de asistencias - ACTUALIZADA
function handleAttendanceDisplay(data) {
    const statusIndicator = document.getElementById('attendance_status_indicator');
    const content = document.getElementById('attendance_content');
    const tableSection = document.getElementById('attendance_table_section');
    const tableBody = document.getElementById('attendance_table_body');
    const summary = document.getElementById('attendance_summary');

    // Verificar si hay registros de asistencia
    if (!data.attendanceRecords || data.attendanceRecords.length === 0) {
        showNoAttendanceMessage(statusIndicator, content, tableSection);
        return;
    }

    // Obtener SOLO los registros de la última actualización
    const lastUpdateTimestamp = data.attendanceStats.last_update;
    
    // Filtrar registros que coincidan EXACTAMENTE con el último timestamp
    const currentRecords = data.attendanceRecords.filter(record => {
        const recordTimestamp = record.updated_at;
        return recordTimestamp === lastUpdateTimestamp;
    });

    if (currentRecords.length === 0) {
        showNoAttendanceMessage(statusIndicator, content, tableSection);
        return;
    }

    // Calcular estadísticas SOLO de los registros actuales
    const currentStats = {
        total: currentRecords.length,
        buddy_attended: currentRecords.filter(r => r.buddy_attended).length,
        peer_attended: currentRecords.filter(r => r.peer_buddy_attended).length,
        both_attended: currentRecords.filter(r => r.buddy_attended && r.peer_buddy_attended).length,
        last_update: lastUpdateTimestamp
    };

    // Mostrar indicador de estado
    statusIndicator.innerHTML = `
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            ${currentStats.total} Registro${currentStats.total !== 1 ? 's' : ''} de Asistencia
        </span>
    `;

    // Mostrar estadísticas actuales - CORREGIDO: usar formatDateRangeCorrect
    content.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div class="text-center bg-blue-50 rounded-lg p-3">
                <div class="text-2xl font-bold text-blue-800">
                    ${currentStats.total > 0 ? Math.round((currentStats.buddy_attended / currentStats.total) * 100) : 0}%
                </div>
                <div class="text-sm text-blue-600">Asistencia Buddy</div>
                <div class="text-xs text-gray-500">
                    ${currentStats.buddy_attended}/${currentStats.total} sesiones
                </div>
            </div>
            <div class="text-center bg-green-50 rounded-lg p-3">
                <div class="text-2xl font-bold text-green-800">
                    ${currentStats.total > 0 ? Math.round((currentStats.peer_attended / currentStats.total) * 100) : 0}%
                </div>
                <div class="text-sm text-green-600">Asistencia PeerBuddy</div>
                <div class="text-xs text-gray-500">
                    ${currentStats.peer_attended}/${currentStats.total} sesiones
                </div>
            </div>
            <div class="text-center bg-purple-50 rounded-lg p-3">
                <div class="text-2xl font-bold text-purple-800">
                    ${currentStats.total > 0 ? Math.round((currentStats.both_attended / currentStats.total) * 100) : 0}%
                </div>
                <div class="text-sm text-purple-600">Ambos Presentes</div>
                <div class="text-xs text-gray-500">
                    ${currentStats.both_attended}/${currentStats.total} sesiones
                </div>
            </div>
        </div>
        <div class="text-center text-sm text-gray-600">
            <div class="bg-blue-50 rounded-lg p-2 inline-block">
                📅 Período registrado: ${formatDateRangeCorrect(currentRecords)}
            </div>
            <div class="text-xs text-gray-500 mt-1">
                Última actualización: ${formatDateTimeCorrect(currentStats.last_update)}
            </div>
        </div>
    `;

    // Ordenar registros por fecha (más reciente primero)
    const sortedRecords = currentRecords.sort((a, b) => new Date(b.date) - new Date(a.date));

    // Llenar tabla con registros ordenados - CORREGIDO: usar formatDateCorrect
    tableBody.innerHTML = sortedRecords.map(record => {
        // CORREGIDO: usar las funciones correctas de formato
        const formattedDate = formatDateCorrect(record.date);
        const weekday = formatWeekdayCorrect(record.date);
        
        return `
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <div class="flex flex-col">
                        <span class="font-medium">
                            ${formattedDate}
                        </span>
                        <span class="text-xs text-gray-500">
                            ${weekday}
                        </span>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    ${record.buddy_attended ? 
                        '<span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">✅ Asistió</span>' : 
                        '<span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">❌ Faltó</span>'}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    ${record.peer_buddy_attended ? 
                        '<span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">✅ Asistió</span>' : 
                        '<span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">❌ Faltó</span>'}
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">
                    ${record.notes || '<span class="text-gray-400 italic">Sin notas</span>'}
                </td>
            </tr>
        `;
    }).join('');

    // Mostrar resumen
    summary.innerHTML = `
        <div class="bg-gray-50 rounded-lg p-4">
            <h4 class="text-sm font-medium text-gray-900 mb-3">Resumen del Período Actual</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <p class="flex justify-between">
                        <span class="font-medium text-blue-700">• Buddy:</span> 
                        <span>${currentStats.buddy_attended}/${currentStats.total} asistencias</span>
                    </p>
                    <p class="flex justify-between">
                        <span class="font-medium text-green-700">• PeerBuddy:</span> 
                        <span>${currentStats.peer_attended}/${currentStats.total} asistencias</span>
                    </p>
                </div>
                <div class="space-y-2">
                    <p class="flex justify-between">
                        <span class="font-medium text-purple-700">• Ambos presentes:</span> 
                        <span>${currentStats.both_attended} sesiones</span>
                    </p>
                    <p class="flex justify-between">
                        <span class="font-medium text-gray-700">• Total sesiones:</span> 
                        <span>${currentStats.total}</span>
                    </p>
                </div>
            </div>
        </div>
    `;

    tableSection.classList.remove('hidden');
}

// FUNCIONES AUXILIARES CORREGIDAS:

// Función corregida para formatear fechas individuales
function formatDateCorrect(dateString) {
    try {
        const date = new Date(dateString);
        
        if (isNaN(date.getTime())) {
            return 'Fecha inválida';
        }

        return date.toLocaleDateString('es-ES', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
        
    } catch (error) {
        return 'Error en fecha';
    }
}

// Función corregida para día de la semana
function formatWeekdayCorrect(dateString) {
    try {
        const date = new Date(dateString);
        
        if (isNaN(date.getTime())) {
            return 'Día inválido';
        }
        return date.toLocaleDateString('es-ES', { weekday: 'long' });
        
    } catch (error) {
        return 'Error';
    }
}

// Función corregida para rango de fechas
function formatDateRangeCorrect(records) {
    if (!records || records.length === 0) return 'Sin registros';
    
    try {
        const validDates = records
            .map(r => {
                try {
                    const date = new Date(r.date);
                    return isNaN(date.getTime()) ? null : date;
                } catch (error) {
                    return null;
                }
            })
            .filter(date => date !== null)
            .sort((a, b) => a - b);

        if (validDates.length === 0) {
            return 'Fechas inválidas';
        }

        const startDate = validDates[0];
        const endDate = validDates[validDates.length - 1];

        if (validDates.length === 1) {
            return formatDateCorrect(startDate);
        }

        return `${formatDateCorrect(startDate)} - ${formatDateCorrect(endDate)}`;
        
    } catch (error) {
        return 'Error en rango de fechas';
    }
}

// Función corregida para fecha y hora
function formatDateTimeCorrect(dateTimeString) {
    try {
        const date = new Date(dateTimeString);
        
        if (isNaN(date.getTime())) {
            return 'Fecha/hora inválida';
        }

        return date.toLocaleString('es-ES', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        
    } catch (error) {
        return 'Error en fecha/hora';
    }
}
// Función para mostrar mensaje cuando no hay asistencias
function showNoAttendanceMessage(statusIndicator, content, tableSection) {
    statusIndicator.innerHTML = `
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            Sin Asistencias Registradas
        </span>
    `;
    
    content.innerHTML = `
            <div class="text-center py-8">
                <svg class="mx-auto h-16 w-16 text-purple-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <h3 class="text-lg font-medium text-purple-800 mb-2">No se han registrado asistencias</h3>
                <p class="text-sm text-purple-600 mb-4">Este emparejamiento aún no tiene registros de asistencia</p>
                <div class="bg-purple-100 rounded-lg p-4 text-left max-w-md mx-auto">
                    <h4 class="font-medium text-purple-800 mb-2">¿Cómo registrar asistencias?</h4>
                    <ul class="text-sm text-purple-700 space-y-1">
                        <li>• Accede a la sección de Asistencias</li>
                        <li>• Selecciona la fecha de la sesión</li>
                        <li>• Marca la asistencia de cada participante</li>
                        <li>• Agrega notas si es necesario</li>
                    </ul>
                </div>
            </div>
        `;
    tableSection.classList.add('hidden');
}

// Función para abrir el modal de asistencia
function openAttendanceModal(friendshipId, date = null) {
    // Si se proporciona una fecha, verificar si ya existe un registro
    if (date) {
        fetch(`/friendships/${friendshipId}/check-attendance?date=${date}`)
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    document.getElementById('save-attendance-btn').textContent = 'Actualizar Asistencia';
                    fillAttendanceForm(data.attendance);
                } else {
                    document.getElementById('save-attendance-btn').textContent = 'Guardar Asistencia';
                    resetAttendanceForm();
                }
            });
    }

    document.getElementById('attendance-modal').classList.remove('hidden');
}

// Función para guardar/actualizar asistencia
function saveAttendance(friendshipId) {
    const form = document.getElementById('attendance-form');
    const formData = new FormData(form);
    const btn = document.getElementById('save-attendance-btn');
    const originalBtnText = btn.textContent;

    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Guardando...';

    fetch(`/friendships/${friendshipId}/attendance`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessAlert(data.message);
            updateLastAttendanceView(data.attendance);
            closeAttendanceModal();
        } else {
            showErrorAlert(data.message);
        }
    })
    .catch(error => {
        showErrorAlert('Error en la conexión: ' + error.message);
    })
    .finally(() => {
        btn.disabled = false;
        btn.textContent = originalBtnText;
    });
}

// Función para actualizar la vista con el último registro
function updateLastAttendanceView(attendance) {
    const lastAttendanceContainer = document.getElementById('last-attendance-container');
    
    lastAttendanceContainer.innerHTML = `
        <div class="grid grid-cols-3 gap-4 mb-4">
            <div class="text-center">
                <p class="text-sm font-medium text-gray-600">Fecha</p>
                <p class="font-bold">${new Date(attendance.date).toLocaleDateString('es-ES')}</p>
            </div>
            <div class="text-center">
                <p class="text-sm font-medium text-gray-600">Buddy</p>
                ${attendance.buddy_attended ? 
                    '<span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Asistió</span>' : 
                    '<span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Faltó</span>'}
            </div>
            <div class="text-center">
                <p class="text-sm font-medium text-gray-600">PeerBuddy</p>
                ${attendance.peer_buddy_attended ? 
                    '<span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Asistió</span>' : 
                    '<span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Faltó</span>'}
            </div>
        </div>
        ${attendance.notes ? `
            <div class="bg-gray-50 p-3 rounded-lg">
                <p class="text-sm font-medium text-gray-600 mb-1">Notas</p>
                <p class="text-sm text-gray-700">${attendance.notes}</p>
            </div>
        ` : ''}
    `;
}

// Función para mostrar mensajes de error mejorados
function showErrorAlert(message) {
    const alertDiv = document.getElementById('error-alert');
    alertDiv.innerHTML = `
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
            <div class="flex justify-between">
                <div>
                    <p class="font-bold">Error</p>
                    <p>${message}</p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-red-700">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </div>
    `;
}
// Función para mostrar la tabla de asistencias
function showAttendanceTable(records) {
    const attendanceBody = document.getElementById('attendance_table_body');
    if (!attendanceBody) {
        console.error('Elemento attendance_table_body no encontrado');
        return;
    }
    
    // Limpiar tabla
    attendanceBody.innerHTML = '';
    
    // Ordenar por fecha (más reciente primero)
    const sortedRecords = [...records].sort((a, b) => new Date(b.date) - new Date(a.date));
    
    sortedRecords.forEach(record => {
        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50';
        
        // Celda de fecha
        const dateCell = document.createElement('td');
        dateCell.className = 'px-6 py-4 whitespace-nowrap text-sm text-gray-900';
        const date = new Date(record.date);
        dateCell.innerHTML = `
            <div class="flex flex-col">
                <span class="font-medium">${date.toLocaleDateString('es-ES')}</span>
                <span class="text-xs text-gray-500">${date.toLocaleDateString('es-ES', {weekday: 'long'})}</span>
            </div>
        `;
        
        // Celdas de asistencia
        const buddyCell = createAttendanceCell(record.buddy_attended);
        const peerCell = createAttendanceCell(record.peer_buddy_attended);
        
        // Celda de notas
        const notesCell = document.createElement('td');
        notesCell.className = 'px-6 py-4 text-sm text-gray-500 max-w-xs';
        notesCell.textContent = record.notes || 'Sin notas';
        
        // Agregar celdas a la fila
        row.appendChild(dateCell);
        row.appendChild(buddyCell);
        row.appendChild(peerCell);
        row.appendChild(notesCell);
        
        attendanceBody.appendChild(row);
    });
}

function createAttendanceCell(attended) {
    const cell = document.createElement('td');
    cell.className = 'px-6 py-4 whitespace-nowrap';
    
    if (attended) {
        cell.innerHTML = `
            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                Asistió
            </span>
        `;
    } else {
        cell.innerHTML = `
            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
                Faltó
            </span>
        `;
    }
    
    return cell;
}

let showingAllRecords = false;
function showAllAttendanceRecords() {
    // almacenar los registros originales en una variable global
    if (window.currentAttendanceRecords) {
        showingAllRecords = true;
        showAttendanceTable(window.currentAttendanceRecords, false);

        const infoRow = document.createElement('tr');
        infoRow.innerHTML = `
            <td colspan="4" class="px-6 py-3 text-center text-sm text-gray-500 bg-gray-50">
                <span class="inline-flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    Mostrando todos los ${window.currentAttendanceRecords.length} registros
                    <button onclick="showLatestAttendanceRecords()" class="ml-2 text-blue-600 hover:text-blue-800 underline">
                        Mostrar solo los últimos
                    </button>
                </span>
            </td>
        `;
        document.getElementById('attendance_table_body').appendChild(infoRow);
    }
}

function showLatestAttendanceRecords() {
    if (window.currentAttendanceRecords) {
        showingAllRecords = false;
        showAttendanceTable(window.currentAttendanceRecords, true, 2);
    }
}

//mostrar asistencias
function displayAttendanceRecords(records) {
    const container = document.getElementById('attendance-records-container');
    container.innerHTML = ''; // Limpiar contenedor
    
    // Crear tabla de asistencias
    const table = document.createElement('div');
    table.className = 'overflow-x-auto bg-white rounded-lg shadow mt-4';
    
    let html = `
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Buddy</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PeerBuddy</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notas</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
    `;
    
    records.forEach(record => {
        html += `
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    ${new Date(record.date).toLocaleDateString('es-ES')}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    ${record.buddy_attended ? 
                        '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Asistió</span>' : 
                        '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Faltó</span>'}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    ${record.peer_buddy_attended ? 
                        '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Asistió</span>' : 
                        '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Faltó</span>'}
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">
                    ${record.notes || '-'}
                </td>
            </tr>
        `;
    });
    
    html += `</tbody></table>`;
    table.innerHTML = html;
    container.appendChild(table);
    
    // Mostrar estadísticas
    const statsContainer = document.getElementById('attendance-stats');
    const buddyAttended = records.filter(r => r.buddy_attended).length;
    const peerAttended = records.filter(r => r.peer_buddy_attended).length;
    const bothAttended = records.filter(r => r.buddy_attended && r.peer_buddy_attended).length;
    
    statsContainer.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            <div class="bg-blue-50 p-4 rounded-lg">
                <p class="text-sm font-medium text-blue-800">Buddy</p>
                <p class="text-2xl font-bold">${buddyAttended}/${records.length} (${Math.round(buddyAttended/records.length*100)}%)</p>
            </div>
            <div class="bg-green-50 p-4 rounded-lg">
                <p class="text-sm font-medium text-green-800">PeerBuddy</p>
                <p class="text-2xl font-bold">${peerAttended}/${records.length} (${Math.round(peerAttended/records.length*100)}%)</p>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg">
                <p class="text-sm font-medium text-purple-800">Ambos</p>
                <p class="text-2xl font-bold">${bothAttended}/${records.length} (${Math.round(bothAttended/records.length*100)}%)</p>
            </div>
        </div>
    `;
}

function openFollowUpModal(friendshipId) {
    currentFriendshipId = friendshipId;
    currentFollowUpId = null;
    
    fetch(`/friendships/${friendshipId}/tracking`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            populateFollowUpModal(data);
            document.getElementById('follow-up-modal').classList.remove('hidden');
            setupAttendanceForm(data.friendship);
            switchTab('follow-up'); // Asegurar que el tab correcto esté activo
        })
        .catch(error => {
            console.error('Error loading friendship data:', error);
            alert('Error al cargar los datos del emparejamiento');
        });
}

function closeFollowUpModal() {
    document.getElementById('follow-up-modal').classList.add('hidden');
    document.getElementById('follow-up-form').reset();
    document.getElementById('attendance-form').reset();
    currentFriendshipId = null;
    currentFollowUpId = null;
    
    // Resetear tabs
    switchTab('follow-up');
}

function switchTab(tabName) {
    // Ocultar todos los contenidos
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remover clase active de todos los tabs
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('border-blue-500', 'text-blue-600', 'border-purple-500', 'text-purple-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Mapear nombres de tabs a sus contenidos correspondientes
    let contentId, tabId, activeColor;
    
    switch(tabName) {
        case 'follow-up':
            contentId = 'follow-up-content';
            tabId = 'follow-up-tab';
            activeColor = 'blue';
            break;
        case 'attendance':
            contentId = 'attendance-content';
            tabId = 'attendance-tab';
            activeColor = 'blue';
            break;
        case 'history':
        case 'monitoring':
            contentId = 'monitoring-content';
            tabId = 'history-tab';
            activeColor = 'blue';
            break;
        default:
            console.warn('Tab no reconocido:', tabName);
            return;
    }
    
    // Mostrar contenido activo
    const activeContent = document.getElementById(contentId);
    if (activeContent) {
        activeContent.classList.remove('hidden');
    } else {
        console.error('Contenido no encontrado:', contentId);
    }
    
    // Activar tab
    const activeTab = document.getElementById(tabId);
    if (activeTab) {
        if (activeColor === 'blue') {
            activeTab.classList.add('border-blue-500', 'text-blue-600');
        } else if (activeColor === 'purple') {
            activeTab.classList.add('border-purple-500', 'text-purple-600');
        }
        activeTab.classList.remove('border-transparent', 'text-gray-500');
    } else {
        console.error('Tab no encontrado:', tabId);
    }
    
    // Ejecutar lógica específica para cada tab
    switch(tabName) {
        case 'follow-up':
            // Lógica específica para evaluación
            console.log('Tab de evaluación activado');
            break;
        case 'attendance':
            // Lógica específica para asistencia
            console.log('Tab de asistencia activado');
            break;
        case 'history':
        case 'monitoring':
            // Lógica específica para monitoreo
            console.log('Tab de monitoreo activado');
            loadMonitoringData();
            break;
    }
}

// Función auxiliar para cargar datos del monitoreo
function loadMonitoringData() {
    // Cargar amistades disponibles si no se han cargado
    const friendshipSelect = document.getElementById('friendship-select');
    if (friendshipSelect && friendshipSelect.options.length <= 1) {
        loadFriendships();
    }
    
    // Establecer fecha actual en el período de monitoreo
    const periodSelect = document.getElementById('monitoring-period');
    if (periodSelect) {
        const currentDate = new Date();
        const currentMonth = currentDate.toLocaleString('es-ES', { month: 'long' });
        const currentYear = currentDate.getFullYear();
        const currentPeriod = `${currentMonth}-${currentYear}`;
        
        // Buscar y seleccionar el período actual si existe
        for (let option of periodSelect.options) {
            if (option.value.toLowerCase().includes(currentMonth.toLowerCase())) {
                option.selected = true;
                break;
            }
        }
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Activar el primer tab por defecto
    switchTab('follow-up');
    
    // Agregar event listeners a los tabs
    const tabs = document.querySelectorAll('.tab-button');
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const tabName = this.id.replace('-tab', '');
            switchTab(tabName);
        });
    });
});

function populateFollowUpModal(data) {
    const friendship = data.friendship;
    
    // Poblar información del emparejamiento
    document.getElementById('followup-buddy-name').textContent = `${friendship.buddy.first_name} ${friendship.buddy.last_name}`;
    document.getElementById('followup-buddy-disability').textContent = `Discapacidad: ${friendship.buddy.disability || 'N/A'}`;
    document.getElementById('followup-peer-buddy-name').textContent = `${friendship.peer_buddy.first_name} ${friendship.peer_buddy.last_name}`;
    
    // Badge de estado
    const statusBadge = document.getElementById('followup-status');
    statusBadge.textContent = friendship.status;
    const statusClasses = {
        'Emparejado': 'bg-green-100 text-green-800',
        'Inactivo': 'bg-yellow-100 text-yellow-800',
        'Finalizado': 'bg-gray-100 text-gray-800'
    };
    statusBadge.className = `inline-flex px-2 py-1 text-xs font-semibold rounded-full ${statusClasses[friendship.status] || 'bg-gray-100 text-gray-800'}`;
    
    // Fechas
    const startDate = friendship.start_date ? new Date(friendship.start_date).toLocaleDateString('es-ES') : 'N/A';
    const endDate = friendship.end_date ? new Date(friendship.end_date).toLocaleDateString('es-ES') : 'En curso';
    document.getElementById('followup-dates').textContent = `${startDate} - ${endDate}`;
    
    // Verificar si hay seguimiento existente
    checkExistingFollowUp(friendship.id);
}

function setupAttendanceForm(friendship) {
    // Mostrar nombres en los displays
    document.getElementById('buddy-name-display').textContent = `${friendship.buddy.first_name} ${friendship.buddy.last_name}`;
    document.getElementById('peer-buddy-name-display').textContent = `${friendship.peer_buddy.first_name} ${friendship.peer_buddy.last_name}`;
    
    // Configurar fechas del emparejamiento
    const startDate = friendship.start_date || friendship.created_at;
    const endDate = friendship.end_date || new Date().toISOString().split('T')[0];
    
    document.getElementById('attendance_start_date').value = startDate.split('T')[0];
    document.getElementById('attendance_end_date').value = endDate.split('T')[0];
    document.getElementById('attendance_start_date').min = startDate.split('T')[0];
    
    if (friendship.end_date) {
        document.getElementById('attendance_end_date').max = endDate.split('T')[0];
    }
}

function checkExistingFollowUp(friendshipId) {
    fetch(`/friendships/${friendshipId}/latest-follow-up`)
        .then(response => response.json())
        .then(data => {
            if (data.followUp) {
                currentFollowUpId = data.followUp.id;
                populateFollowUpForm(data.followUp);
                document.getElementById('follow-up-submit-btn').textContent = 'Actualizar Seguimiento';
                document.getElementById('follow-up-method').value = 'PUT';
            } else {
                document.getElementById('follow-up-submit-btn').textContent = 'Guardar Seguimiento';
                document.getElementById('follow-up-method').value = 'POST';
            }
        })
        .catch(error => {
            console.error('Error checking existing follow-up:', error);
        });
}

function populateFollowUpForm(followUp) {
    document.getElementById('buddy_progress').value = followUp.buddy_progress || '';
    document.getElementById('peer_buddy_progress').value = followUp.peer_buddy_progress || '';
    document.getElementById('relationship_quality').value = followUp.relationship_quality || '';
    document.getElementById('goals_achieved').value = followUp.goals_achieved || '';
    document.getElementById('challenges_faced').value = followUp.challenges_faced || '';
    document.getElementById('recommendations').value = followUp.recommendations || '';
    document.getElementById('next_steps').value = followUp.next_steps || '';
    document.getElementById('support_needed').value = followUp.support_needed || '';
    document.getElementById('next_follow_up_date').value = followUp.next_follow_up_date || '';
}

function createLocalDate(dateString) {
    const [year, month, day] = dateString.split('-').map(Number);
    return new Date(year, month - 1, day);
}

function formatDateToString(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function generateAttendanceDays() {
    const startDateStr = document.getElementById('attendance_start_date').value;
    const endDateStr = document.getElementById('attendance_end_date').value;
    
    document.getElementById('attendance_dates_hidden').value = JSON.stringify({
        start_date: startDateStr,
        end_date: endDateStr
    });

    if (!startDateStr || !endDateStr) {
        document.getElementById('attendance-days-container').innerHTML = `
            <div class="text-center py-8 text-gray-500">
                Selecciona las fechas de inicio y fin para generar los días
            </div>
        `;
        return;
    }
    
    const start = createLocalDate(startDateStr);
    const end = createLocalDate(endDateStr);
    const container = document.getElementById('attendance-days-container');
    
    if (start > end) {
        container.innerHTML = `
            <div class="text-center py-8 text-red-500">
                La fecha de inicio debe ser anterior a la fecha de fin
            </div>
        `;
        return;
    }
    
    // Obtener asistencias existentes
    fetch(`/friendships/${currentFriendshipId}/attendance-range?start_date=${startDateStr}&end_date=${endDateStr}`)
        .then(response => response.json())
        .then(data => {
            const existingAttendance = data.attendance || {};
            renderAttendanceDays(start, end, existingAttendance);
        })
        .catch(error => {
            console.error('Error loading existing attendance:', error);
            renderAttendanceDays(start, end, {});
        });
}

function renderAttendanceDays(startDate, endDate, existingAttendance) {
    const container = document.getElementById('attendance-days-container');
    let html = '<div class="space-y-3 max-h-96 overflow-y-auto p-4">';
    
    const currentDate = new Date(startDate);
    let dayCount = 0;
    
    while (currentDate <= endDate && dayCount < 365) {
        const dateStr = formatDateToString(currentDate);
        const dayName = currentDate.toLocaleDateString('es-ES', { weekday: 'long' });
        const dayMonth = currentDate.toLocaleDateString('es-ES', { day: 'numeric', month: 'long' });
        
        const attendance = existingAttendance[dateStr] || {};
        const buddyChecked = attendance.buddy_attended ? 'checked' : '';
        const peerBuddyChecked = attendance.peer_buddy_attended ? 'checked' : '';
        const attendanceId = attendance.id || '';
        const notes = attendance.notes || '';
        
        html += `
            <div class="bg-white border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-900 capitalize">${dayName}</h4>
                        <p class="text-sm text-gray-500">${dayMonth}, ${currentDate.getFullYear()}</p>
                    </div>
                    
                    <div class="flex items-center space-x-6">
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="buddy_${dateStr}" 
                                   name="attendance[${dateStr}][buddy_attended]" 
                                   value="1" 
                                   ${buddyChecked}
                                   class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded \">
                            <label for="buddy_${dateStr}" class="ml-2 text-sm font-medium text-blue-700 pr-3">
                                Buddy
                            </label>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="peer_buddy_${dateStr}" 
                                   name="attendance[${dateStr}][peer_buddy_attended]" 
                                   value="1" 
                                   ${peerBuddyChecked}
                                   class="h-5 w-5 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                            <label for="peer_buddy_${dateStr}" class="ml-2 text-sm font-medium text-green-700 pr-3">
                                PeerBuddy
                            </label>
                        </div>
                        
                        <div class="flex items-center">
                            <button type="button" 
                                    onclick="toggleNotesForDay('${dateStr}')" 
                                    class="flex items-center text-gray-600 hover:text-gray-800 focus:outline-none transition-colors">
                                <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h8a2 2 0 002-2V7l-5-5z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 3v4a1 1 0 001 1h4"/>
                                </svg>
                                Notas
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Campo para notas (inicialmente oculto) -->
                <div id="notes_${dateStr}" class="mt-3 ${notes ? '' : 'hidden'}">
                    <label for="notes_input_${dateStr}" class="block text-xs font-medium text-gray-700 mb-1">
                        Notas del día
                    </label>
                    <textarea 
                        id="notes_input_${dateStr}"
                        name="attendance[${dateStr}][notes]" 
                        rows="2" 
                        class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                        placeholder="Observaciones del día...">${notes}</textarea>
                </div>
                
                <!-- Campo oculto para ID de asistencia existente -->
                ${attendanceId ? `<input type="hidden" name="attendance[${dateStr}][id]" value="${attendanceId}">` : ''}
                
                <!-- Campo oculto para la fecha -->
                <input type="hidden" name="attendance[${dateStr}][date]" value="${dateStr}">
            </div>
        `;
        
        currentDate.setDate(currentDate.getDate() + 1);
        dayCount++;
    }
    
    html += '</div>';
    
    if (dayCount === 0) {
        html = `
            <div class="text-center py-8 text-gray-500">
                No hay días en el rango seleccionado
            </div>
        `;
    }
    
    container.innerHTML = html;
}
// Función para mostrar/ocultar notas de un día específico
function toggleNotesForDay(dateStr) {
    const notesDiv = document.getElementById(`notes_${dateStr}`);
    const button = event.target.closest('button');
    
    if (notesDiv.classList.contains('hidden')) {
        notesDiv.classList.remove('hidden');
        button.classList.add('text-blue-600');
        button.classList.remove('text-gray-600');
    } else {
        notesDiv.classList.add('hidden');
        button.classList.remove('text-blue-600');
        button.classList.add('text-gray-600');
    }
}

// Función para seleccionar/deseleccionar todas las asistencias
function toggleAllAttendance(type, checked) {
    const checkboxes = document.querySelectorAll(`input[name*="[${type}_attended]"]`);
    checkboxes.forEach(checkbox => {
        checkbox.checked = checked;
    });
}

// Función para cargar el historial de seguimientos
function loadFollowUpHistory(friendshipId) {
    const container = document.getElementById('followup-history-content');
    
    // Mostrar loading
    container.innerHTML = `
        <div class="text-center py-8">
            <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm shadow rounded-md text-gray-500 bg-white">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Cargando historial...
            </div>
        </div>
    `;
    
    fetch(`/friendships/${friendshipId}/follow-ups-history`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            renderFollowUpHistory(data.followUps || []);
        })
        .catch(error => {
            console.error('Error loading follow-up history:', error);
            container.innerHTML = `
                <div class="text-center py-8 text-red-500">
                    <p>Error al cargar el historial</p>
                    <button onclick="loadFollowUpHistory(${friendshipId})" class="mt-2 text-sm text-blue-600 hover:text-blue-800">
                        Reintentar
                    </button>
                </div>
            `;
        });
}

// Función para renderizar el historial de seguimientos
function renderFollowUpHistory(followUps) {
    const container = document.getElementById('followup-history-content');
    
    if (followUps.length === 0) {
        container.innerHTML = `
            <div class="text-center py-8 text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="mt-2">No hay seguimientos registrados</p>
            </div>
        `;
        return;
    }
    
    let html = '';
    followUps.forEach((followUp, index) => {
        const date = new Date(followUp.created_at).toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        
        html += `
            <div class="bg-white border rounded-lg p-4 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="font-medium text-gray-900">Seguimiento #${followUps.length - index}</h4>
                    <span class="text-sm text-gray-500">${date}</span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">${followUp.buddy_progress || 'N/A'}</div>
                        <div class="text-xs text-gray-500">Progreso Buddy</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">${followUp.peer_buddy_progress || 'N/A'}</div>
                        <div class="text-xs text-gray-500">Progreso PeerBuddy</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">${followUp.relationship_quality || 'N/A'}</div>
                        <div class="text-xs text-gray-500">Calidad Relación</div>
                    </div>
                </div>
                
                ${followUp.goals_achieved ? `
                    <div class="mb-3">
                        <h5 class="text-sm font-medium text-gray-700 mb-1">Objetivos Alcanzados:</h5>
                        <p class="text-sm text-gray-600">${followUp.goals_achieved}</p>
                    </div>
                ` : ''}
                
                ${followUp.challenges_faced ? `
                    <div class="mb-3">
                        <h5 class="text-sm font-medium text-gray-700 mb-1">Desafíos:</h5>
                        <p class="text-sm text-gray-600">${followUp.challenges_faced}</p>
                    </div>
                ` : ''}
                
                ${followUp.recommendations ? `
                    <div class="mb-3">
                        <h5 class="text-sm font-medium text-gray-700 mb-1">Recomendaciones:</h5>
                        <p class="text-sm text-gray-600">${followUp.recommendations}</p>
                    </div>
                ` : ''}
                
                ${followUp.next_follow_up_date ? `
                    <div class="text-sm text-gray-500">
                        <strong>Próximo seguimiento:</strong> ${new Date(followUp.next_follow_up_date).toLocaleDateString('es-ES')}
                    </div>
                ` : ''}
            </div>
        `;
    });
    
    container.innerHTML = html;
}

// Event listeners para el formulario de seguimiento
document.addEventListener('DOMContentLoaded', function() {
    // Formulario de seguimiento
    const followUpForm = document.getElementById('follow-up-form');
    if (followUpForm) {
        followUpForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitFollowUpForm();
        });
    }
    
    // Formulario de asistencia
    const attendanceForm = document.getElementById('attendance-form');
    if (attendanceForm) {
        attendanceForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitAttendanceForm();
        });
    }
    
    // Event listeners para fechas de asistencia
    const startDateInput = document.getElementById('attendance_start_date');
    const endDateInput = document.getElementById('attendance_end_date');
    
    if (startDateInput && endDateInput) {
        startDateInput.addEventListener('change', generateAttendanceDays);
        endDateInput.addEventListener('change', generateAttendanceDays);
    }
});

// Función para enviar el formulario de seguimiento
function submitFollowUpForm() {
    const form = document.getElementById('follow-up-form');
    const formData = new FormData(form);
    const submitBtn = document.getElementById('follow-up-submit-btn');
    
    // Deshabilitar botón mientras se procesa
    submitBtn.disabled = true;
    submitBtn.textContent = 'Guardando...';
    
    // Determinar URL y método
    const method = document.getElementById('follow-up-method').value;
    let url = `/friendships/${currentFriendshipId}/follow-ups`;
    
    if (method === 'PUT' && currentFollowUpId) {
        url = `/friendships/${currentFriendshipId}/follow-ups/${currentFollowUpId}`;
        formData.append('_method', 'PUT');
    }
    
    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => Promise.reject(err));
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert('Seguimiento guardado exitosamente');
            closeFollowUpModal();
            
            // Recargar la página o actualizar la tabla si es necesario
            if (typeof window.loadFriendships === 'function') {
                window.loadFriendships();
            } else {
                location.reload();
            }
        } else {
            throw new Error(data.message || 'Error al guardar el seguimiento');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert(error.message || 'Error al guardar el seguimiento');
    })
    .finally(() => {
        // Rehabilitar botón
        submitBtn.disabled = false;
        submitBtn.textContent = method === 'PUT' ? 'Actualizar Seguimiento' : 'Guardar Seguimiento';
    });
}

// Función para enviar el formulario de asistencia
function submitAttendanceForm() {
    const form = document.getElementById('attendance-form');
    const startDate = form.querySelector('#attendance_start_date').value;
    const endDate = form.querySelector('#attendance_end_date').value;
    
    if (!startDate || !endDate) {
        alert('Por favor selecciona ambas fechas (inicio y fin)');
        return;
    }

    const attendanceData = {
        start_date: startDate,
        end_date: endDate,
        attendance: []
    };

    // Recopilar datos de cada día
    const dateInputs = form.querySelectorAll('input[name*="[date]"]');
    
    dateInputs.forEach(dateInput => {
        const dateStr = dateInput.value;
        const dateMatch = dateInput.name.match(/attendance\[(.*?)\]/);
        
        if (dateMatch && dateMatch[1]) {
            const dateKey = dateMatch[1];
            
            const buddyChecked = form.querySelector(`input[name="attendance[${dateKey}][buddy_attended]"]`)?.checked || false;
            const peerChecked = form.querySelector(`input[name="attendance[${dateKey}][peer_buddy_attended]"]`)?.checked || false;
            const notes = form.querySelector(`textarea[name="attendance[${dateKey}][notes]"]`)?.value || '';
            const attendanceId = form.querySelector(`input[name="attendance[${dateKey}][id]"]`)?.value || null;

            const attendanceRecord = {
                date: dateStr,
                buddy_attended: buddyChecked,
                peer_buddy_attended: peerChecked,
                notes: notes.trim()
            };

            // Incluir el ID si existe para indicar que es una actualización
            if (attendanceId && attendanceId !== '') {
                attendanceRecord.id = parseInt(attendanceId);
            }

            attendanceData.attendance.push(attendanceRecord);
        }
    });

    const submitBtn = document.getElementById('attendance-submit-btn');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Guardando...';

    fetch(`/friendships/${currentFriendshipId}/attendance`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify(attendanceData)
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => Promise.reject(err));
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert(`Asistencia guardada exitosamente. Registros procesados: ${data.processed || 0}`);
            // Recargar los días para mostrar los cambios
            generateAttendanceDays();
            closeFollowUpModal();
        } else {
            throw new Error(data.message || 'Error al guardar');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        
        // Mensaje más específico para errores de duplicado
        if (error.message.includes('Duplicate entry')) {
            alert('Error: Ya existe un registro de asistencia para una o más fechas seleccionadas. Por favor verifica los datos.');
        } else {
            alert(error.message || 'Error al guardar la asistencia');
        }
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Guardar Asistencia';
    });
}
 
</script>

