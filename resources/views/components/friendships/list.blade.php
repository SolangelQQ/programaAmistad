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
@include('modals.friendships.tracking')

<div class="mt-4">
    {{ $friendships->links() }}
</div>

<script>
  // Función principal para mostrar detalles del emparejamiento
async function showFriendshipDetails(friendshipId) {
    const modal = document.getElementById('view-friendship-modal');
    
    if (!modal) {
        console.error('Modal no encontrado');
        return;
    }
    
    try {
        // Mostrar modal inmediatamente
        modal.classList.remove('hidden');
        
        // Mostrar indicador de carga
        const modalContent = document.getElementById('modal-content');
        if (!modalContent) {
            console.error('Contenido del modal no encontrado');
            return;
        }
        
        const originalContent = modalContent.innerHTML;
        
        modalContent.innerHTML = `
            <div class="flex justify-center items-center p-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <span class="ml-2">Cargando detalles del emparejamiento...</span>
            </div>
        `;

        // Hacer la petición AJAX con mejor manejo de errores
        const response = await fetch(`/friendships/${friendshipId}/show`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        });

        // Verificar si la respuesta es exitosa
        if (!response.ok) {
            let errorMessage = `Error ${response.status}: ${response.statusText}`;
            
            try {
                const errorData = await response.text();
                console.error('Error del servidor:', errorData);
                
                // Si es HTML, es probable que sea una página de error de Laravel
                if (errorData.includes('<html>')) {
                    errorMessage = 'Error interno del servidor. Revisa los logs del servidor.';
                } else {
                    errorMessage = errorData || errorMessage;
                }
            } catch (e) {
                console.error('Error al leer respuesta de error:', e);
            }
            
            throw new Error(errorMessage);
        }

        // Intentar parsear la respuesta JSON
        let data;
        try {
            data = await response.json();
        } catch (e) {
            console.error('Error al parsear JSON:', e);
            throw new Error('La respuesta del servidor no es válida');
        }

        console.log('Datos recibidos:', data);

        // Verificar que los datos sean válidos
        if (!data || !data.friendship) {
            throw new Error('Los datos recibidos no contienen información del emparejamiento');
        }

        // Restaurar contenido original del modal
        modalContent.innerHTML = originalContent;
        
        // Poblar el modal con los datos
        populateModalData(data);
        
        // Manejar la información de seguimiento
        handleFollowUpDisplay(data);

    } catch (error) {
        console.error('Error completo:', error);
        
        // Mostrar error en el modal
        const modalContent = document.getElementById('modal-content');
        modalContent.innerHTML = `
            <div class="p-6 text-center">
                <div class="flex justify-between items-center mb-5">
                    <h2 class="text-xl font-bold text-gray-900">Error al cargar datos</h2>
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
                <h3 class="text-lg font-medium text-gray-900 mb-2">No se pudieron cargar los datos</h3>
                <p class="text-gray-600 mb-4">Ha ocurrido un error al intentar cargar los detalles del emparejamiento.</p>
                <p class="text-sm text-gray-500 mb-4">Error: ${error.message}</p>
                <div class="space-y-2">
                    <button onclick="showFriendshipDetails(${friendshipId})" 
                            class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 mr-2">
                        Reintentar
                    </button>
                    <button onclick="closeViewFriendshipModal()" 
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                        Cerrar
                    </button>
                </div>
            </div>
        `;
    }
}

// Función para poblar los datos en el modal
function populateModalData(data) {
    try {
        console.log('Poblando datos del modal:', data);

        // Función auxiliar para obtener elemento y establecer contenido
        const setElementContent = (id, content) => {
            const element = document.getElementById(id);
            if (element) {
                element.textContent = content;
            } else {
                console.warn(`Elemento no encontrado: ${id}`);
            }
        };

        // Información del Buddy
        const buddyData = data.buddy || {};
        setElementContent('view_buddy_name', 
            `${buddyData.first_name || ''} ${buddyData.last_name || ''}`.trim() || 'N/A');
        setElementContent('view_buddy_disability', buddyData.disability || 'No especificada');
        setElementContent('view_buddy_age', buddyData.age ? `${buddyData.age} años` : 'N/A');
        setElementContent('view_buddy_ci', buddyData.ci || 'N/A');
        setElementContent('view_buddy_phone', buddyData.phone || 'N/A');
        setElementContent('view_buddy_email', buddyData.email || 'No especificado');
        setElementContent('view_buddy_address', buddyData.address || 'N/A');
        
        // Información del PeerBuddy
        const peerBuddyData = data.peerBuddy || {};
        setElementContent('view_peerbuddy_name', 
            `${peerBuddyData.first_name || ''} ${peerBuddyData.last_name || ''}`.trim() || 'N/A');
        setElementContent('view_peerbuddy_age', peerBuddyData.age ? `${peerBuddyData.age} años` : 'N/A');
        setElementContent('view_peerbuddy_ci', peerBuddyData.ci || 'N/A');
        setElementContent('view_peerbuddy_phone', peerBuddyData.phone || 'N/A');
        setElementContent('view_peerbuddy_email', peerBuddyData.email || 'No especificado');
        setElementContent('view_peerbuddy_address', peerBuddyData.address || 'N/A');
        
        // Información de líderes
        const buddyLeader = data.buddyLeader || {};
        const peerBuddyLeader = data.peerBuddyLeader || {};
        
        setElementContent('view_buddy_leader_name', buddyLeader.name || 'No asignado');
        setElementContent('view_buddy_leader_email', buddyLeader.email || 'N/A');
        setElementContent('view_peerbuddy_leader_name', peerBuddyLeader.name || 'No asignado');
        setElementContent('view_peerbuddy_leader_email', peerBuddyLeader.email || 'N/A');
        
        // Información del emparejamiento
        const friendshipData = data.friendship || {};
        setElementContent('view_friendship_id', friendshipData.id || 'N/A');
        setElementContent('view_start_date', 
            friendshipData.start_date ? new Date(friendshipData.start_date).toLocaleDateString('es-ES') : 'N/A');
        setElementContent('view_end_date', 
            friendshipData.end_date ? new Date(friendshipData.end_date).toLocaleDateString('es-ES') : 'No definida');
        setElementContent('view_notes', friendshipData.notes || 'Sin notas adicionales');
        
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

// Función para abrir el modal de seguimiento (CORREGIDA)
async function openTrackingFriendshipModal(friendshipId) {
    const modal = document.getElementById('tracking-friendship-modal');
    
    if (!modal) {
        console.error('Modal de seguimiento no encontrado');
        alert('Error: Modal de seguimiento no encontrado');
        return;
    }

    try {
        // Mostrar modal con indicador de carga
        modal.classList.remove('hidden');
        
        // Mostrar indicador de carga si existe un contenedor para ello
        const loadingContainer = document.getElementById('tracking-loading-container');
        if (loadingContainer) {
            loadingContainer.classList.remove('hidden');
        }

        console.log(`Cargando datos de seguimiento para friendship ID: ${friendshipId}`);
        
        // Hacer petición AJAX
        const response = await fetch(`/friendships/${friendshipId}/tracking`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        });

        if (!response.ok) {
            let errorMessage = `Error ${response.status}: ${response.statusText}`;
            
            try {
                const errorData = await response.text();
                console.error('Error del servidor:', errorData);
                
                if (errorData.includes('<html>')) {
                    errorMessage = 'Error interno del servidor. Revisa los logs del servidor.';
                } else {
                    errorMessage = errorData || errorMessage;
                }
            } catch (e) {
                console.error('Error al leer respuesta de error:', e);
            }
            
            throw new Error(errorMessage);
        }

        // Intentar parsear la respuesta JSON
        let data;
        try {
            data = await response.json();
        } catch (e) {
            console.error('Error al parsear JSON:', e);
            throw new Error('La respuesta del servidor no es válida');
        }

        console.log('Datos de seguimiento recibidos:', data);

        // Verificar que los datos sean válidos
        if (!data || !data.friendship) {
            throw new Error('Los datos recibidos no contienen información del emparejamiento');
        }

        // Ocultar indicador de carga
        if (loadingContainer) {
            loadingContainer.classList.add('hidden');
        }

        // Poblar el modal con los datos
        populateTrackingModal(data.friendship);

    } catch (error) {
        console.error('Error loading friendship data:', error);
        
        // Ocultar indicador de carga
        const loadingContainer = document.getElementById('tracking-loading-container');
        if (loadingContainer) {
            loadingContainer.classList.add('hidden');
        }

        // Mostrar error al usuario
        alert(`Error al cargar los datos del emparejamiento: ${error.message}`);
        
        // Cerrar modal en caso de error
        closeTrackingFriendshipModal();
    }
}

// Función para poblar el modal de seguimiento
function populateTrackingModal(friendship) {
    try {
        console.log('Poblando modal de seguimiento:', friendship);
        
        // Función auxiliar para establecer contenido
        const setElementContent = (id, content) => {
            const element = document.getElementById(id);
            if (element) {
                element.textContent = content;
            } else {
                console.warn(`Elemento no encontrado en modal de seguimiento: ${id}`);
            }
        };

        // Establecer ID del friendship en el formulario
        const friendshipIdInput = document.getElementById('friendship_id');
        if (friendshipIdInput) {
            friendshipIdInput.value = friendship.id;
        }

        // Poblar información básica si existen los elementos
        if (friendship.buddy) {
            setElementContent('tracking_buddy_name', 
                `${friendship.buddy.first_name || ''} ${friendship.buddy.last_name || ''}`.trim() || 'N/A');
        }
        
        if (friendship.peerBuddy) {
            setElementContent('tracking_peerbuddy_name', 
                `${friendship.peerBuddy.first_name || ''} ${friendship.peerBuddy.last_name || ''}`.trim() || 'N/A');
        }

        setElementContent('tracking_friendship_id', friendship.id || 'N/A');
        setElementContent('tracking_start_date', 
            friendship.start_date ? new Date(friendship.start_date).toLocaleDateString('es-ES') : 'N/A');

        console.log('Modal de seguimiento poblado correctamente');
    } catch (error) {
        console.error('Error al poblar modal de seguimiento:', error);
    }
}

// Función para cerrar el modal de seguimiento
function closeTrackingFriendshipModal() {
    const modal = document.getElementById('tracking-friendship-modal');
    if (modal) {
        modal.classList.add('hidden');
    }
    
    // Limpiar el formulario si existe
    const form = document.getElementById('tracking-friendship-form');
    if (form) {
        form.reset();
    }
    
    // Volver a la primera pestaña por defecto si existe la función
    if (typeof switchTab === 'function') {
        switchTab('attendance');
    }
}

// Función para cerrar el modal de detalles
function closeViewFriendshipModal() {
    const modal = document.getElementById('view-friendship-modal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

// Función para obtener clase de badge según estado
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

// Función para cambiar pestañas (si no existe)
function switchTab(tabName) {
    console.log(`Cambiando a pestaña: ${tabName}`);
    // Implementar lógica de cambio de pestañas según tu diseño
}

// Funciones para manejar el seguimiento (mantener las originales)
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

        // Función auxiliar para establecer contenido HTML
        const setElementHTML = (id, content) => {
            const element = document.getElementById(id);
            if (element) {
                element.innerHTML = content;
            } else {
                console.warn(`Elemento no encontrado: ${id}`);
            }
        };

        // Función auxiliar para establecer contenido de texto
        const setElementText = (id, content) => {
            const element = document.getElementById(id);
            if (element) {
                element.textContent = content;
            } else {
                console.warn(`Elemento no encontrado: ${id}`);
            }
        };

        // Mostrar badges con texto y colores para los campos de progreso
        setElementHTML('buddy_progress_stars', createProgressBadge(followUp.buddy_progress));
        setElementHTML('peer_buddy_progress_stars', createProgressBadge(followUp.peer_buddy_progress));
        setElementHTML('relationship_quality_stars', createProgressBadge(followUp.relationship_quality));
        
        // Llenar campos de texto
        setElementText('goals_achieved', followUp.goals_achieved || 'No especificados');
        setElementText('challenges_faced', followUp.challenges_faced || 'No especificados');
        setElementText('recommendations', followUp.recommendations || 'No especificadas');
        setElementText('next_steps', followUp.next_steps || 'No especificados');
        
        // Información del usuario y fecha
        setElementText('follow_up_user', followUp.user ? followUp.user.name : 'Usuario desconocido');
        setElementText('follow_up_date', new Date(followUp.created_at).toLocaleDateString('es-ES'));
        
        // Próximo seguimiento
        const nextFollowUpInfo = document.getElementById('next_follow_up_info');
        if (nextFollowUpInfo) {
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
        }
    } catch (error) {
        console.error('Error al mostrar detalles del seguimiento:', error);
    }
}
// async function showFriendshipDetails(friendshipId) {
//     const modal = document.getElementById('view-friendship-modal');
    
//     try {
        
//         modal.classList.remove('hidden');
        
//         // Mostrar indicador de carga solo en el contenido, no reemplazar todo el modal
//         const modalContent = document.getElementById('modal-content');
//         const originalContent = modalContent.innerHTML;
        
//         modalContent.innerHTML = `
//             <div class="flex justify-center items-center p-8">
//                 <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
//                 <span class="ml-2">Cargando detalles del emparejamiento...</span>
//             </div>
//         `;

//         // Hacer la petición AJAX
//         const response = await fetch(`/friendships/${friendshipId}/show`, {
//             method: 'GET',
//             headers: {
//                 'Content-Type': 'application/json',
//                 'X-Requested-With': 'XMLHttpRequest',
//                 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
//             }
//         });

//         if (!response.ok) {
//             const errorData = await response.text();
//             console.error('Error en la respuesta:', response.status, errorData);
//             throw new Error(`Error ${response.status}: ${response.statusText}`);
//         }

//         const data = await response.json();
//         console.log('Datos recibidos:', data);

//         // Verificar que los datos sean válidos
//         if (!data || !data.friendship) {
//             throw new Error('Los datos recibidos no son válidos');
//         }

//         // Restaurar contenido original del modal
//         modalContent.innerHTML = originalContent;
        
//         // Poblar el modal con los datos
//         populateModalData(data);
        
//         // Manejar la información de seguimiento
//         handleFollowUpDisplay(data);
//         handleAttendanceDisplay(data);

//     } catch (error) {
//         console.error('Error completo:', error);
        
//         // Solo mostrar error en el contenido del modal, mantener la estructura
//         const modalContent = document.getElementById('modal-content');
//         modalContent.innerHTML = `
//             <div class="p-6 text-center">
//                 <div class="flex justify-between items-center mb-5">
//                     <h2 class="text-xl font-bold text-gray-900">Error</h2>
//                     <button type="button" onclick="closeViewFriendshipModal()" class="text-gray-400 hover:text-gray-500 transition-colors">
//                         <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
//                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
//                         </svg>
//                     </button>
//                 </div>
//                 <div class="text-red-600 mb-4">
//                     <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
//                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z"></path>
//                     </svg>
//                 </div>
//                 <h3 class="text-lg font-medium text-gray-900 mb-2">Error al cargar los datos</h3>
//                 <p class="text-gray-600 mb-4">No se pudieron cargar los detalles del emparejamiento.</p>
//                 <p class="text-sm text-gray-500 mb-4">Error: ${error.message}</p>
//                 <button onclick="closeViewFriendshipModal()" 
//                         class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
//                     Cerrar
//                 </button>
//             </div>
//         `;
//     }
// }

// // Función para poblar los datos en el modal (SIN CAMBIOS - ya está bien)
// function populateModalData(data) {
//     try {
//         console.log('Poblando datos del modal:', data);

//         // Información del Buddy
//         const buddyData = data.buddy || {};
//         document.getElementById('view_buddy_name').textContent = 
//             `${buddyData.first_name || ''} ${buddyData.last_name || ''}`.trim() || 'N/A';
//         document.getElementById('view_buddy_disability').textContent = 
//             buddyData.disability || 'No especificada';
//         document.getElementById('view_buddy_age').textContent = 
//             buddyData.age ? `${buddyData.age} años` : 'N/A';
//         document.getElementById('view_buddy_ci').textContent = 
//             buddyData.ci || 'N/A';
//         document.getElementById('view_buddy_phone').textContent = 
//             buddyData.phone || 'N/A';
//         document.getElementById('view_buddy_email').textContent = 
//             buddyData.email || 'No especificado';
//         document.getElementById('view_buddy_address').textContent = 
//             buddyData.address || 'N/A';
        
//         // Información del PeerBuddy
//         const peerBuddyData = data.peerBuddy || {};
//         document.getElementById('view_peerbuddy_name').textContent = 
//             `${peerBuddyData.first_name || ''} ${peerBuddyData.last_name || ''}`.trim() || 'N/A';
//         document.getElementById('view_peerbuddy_age').textContent = 
//             peerBuddyData.age ? `${peerBuddyData.age} años` : 'N/A';
//         document.getElementById('view_peerbuddy_ci').textContent = 
//             peerBuddyData.ci || 'N/A';
//         document.getElementById('view_peerbuddy_phone').textContent = 
//             peerBuddyData.phone || 'N/A';
//         document.getElementById('view_peerbuddy_email').textContent = 
//             peerBuddyData.email || 'No especificado';
//         document.getElementById('view_peerbuddy_address').textContent = 
//             peerBuddyData.address || 'N/A';
        
//         // Información de líderes
//         const buddyLeader = data.buddyLeader || {};
//         const peerBuddyLeader = data.peerBuddyLeader || {};
        
//         document.getElementById('view_buddy_leader_name').textContent = 
//             buddyLeader.name || 'No asignado';
//         document.getElementById('view_buddy_leader_email').textContent = 
//             buddyLeader.email || 'N/A';
//         document.getElementById('view_peerbuddy_leader_name').textContent = 
//             peerBuddyLeader.name || 'No asignado';
//         document.getElementById('view_peerbuddy_leader_email').textContent = 
//             peerBuddyLeader.email || 'N/A';
        
//         // Información del emparejamiento
//         const friendshipData = data.friendship || {};
//         document.getElementById('view_friendship_id').textContent = 
//             friendshipData.id || 'N/A';
//         document.getElementById('view_start_date').textContent = 
//             friendshipData.start_date ? new Date(friendshipData.start_date).toLocaleDateString('es-ES') : 'N/A';
//         document.getElementById('view_end_date').textContent = 
//             friendshipData.end_date ? new Date(friendshipData.end_date).toLocaleDateString('es-ES') : 'No definida';
//         document.getElementById('view_notes').textContent = 
//             friendshipData.notes || 'Sin notas adicionales';
        
//         // Status badge
//         const statusBadge = document.getElementById('view_status_badge');
//         if (statusBadge) {
//             statusBadge.textContent = friendshipData.status || 'N/A';
//             statusBadge.className = `px-3 py-1 text-sm font-semibold rounded-full ${getStatusBadgeClass(friendshipData.status)}`;
//         }

//         console.log('Datos poblados correctamente');
//     } catch (error) {
//         console.error('Error al poblar los datos:', error);
//     }
// }

// function handleFollowUpDisplay(data) {
//     const statusIndicator = document.getElementById('follow_up_status_indicator');
//     const followUpContent = document.getElementById('follow_up_content');
//     const lastFollowUpSection = document.getElementById('last_follow_up_section');
    
//     if (!statusIndicator || !followUpContent || !lastFollowUpSection) {
//         console.error('Elementos de seguimiento no encontrados');
//         return;
//     }
    
//     try {
//         if (data.hasFollowUps && data.followUps && data.followUps.length > 0) {
//             console.log('Mostrando información de seguimientos:', data.followUps);
            
//             const latestFollowUp = data.followUps[0];
            
//             // Indicador de estado - Con seguimiento
//             statusIndicator.innerHTML = `
//                 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
//                     <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
//                         <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
//                     </svg>
//                     Seguimiento Realizado
//                 </span>
//             `;
            
//             // Contenido del seguimiento
//             followUpContent.innerHTML = `
//                 <div class="text-center">
//                     <p class="text-indigo-800 font-medium mb-2">Se han realizado ${data.followUps.length} seguimiento(s)</p>
//                     <p class="text-sm text-indigo-600">Último seguimiento: ${new Date(latestFollowUp.created_at).toLocaleDateString('es-ES')}</p>
//                 </div>
//             `;
            
//             // Mostrar detalles del último seguimiento
//             showLastFollowUpDetails(latestFollowUp);
//             lastFollowUpSection.classList.remove('hidden');
            
//         } else {
//             console.log('No hay seguimientos para mostrar');
            
//             // Sin seguimientos
//             statusIndicator.innerHTML = `
//                 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
//                     <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
//                         <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
//                     </svg>
//                     Sin Seguimiento
//                 </span>
//             `;
            
//             followUpContent.innerHTML = `
//                 <div class="text-center">
//                     <svg class="mx-auto h-12 w-12 text-indigo-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
//                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
//                     </svg>
//                     <p class="text-indigo-800 font-medium mb-2">No se ha realizado seguimiento</p>
//                     <p class="text-sm text-indigo-600">Este emparejamiento aún no tiene registros de seguimiento</p>
//                 </div>
//             `;
            
//             lastFollowUpSection.classList.add('hidden');
//         }
//     } catch (error) {
//         console.error('Error al manejar información de seguimiento:', error);
//     }
// }

// // Mapeo de valores numéricos a texto y colores
// const progressMapping = {
//     5: { text: 'Excelente', color: 'text-green-600 bg-green-50' },
//     4: { text: 'Bueno', color: 'text-blue-600 bg-blue-50' },
//     3: { text: 'Regular', color: 'text-yellow-600 bg-yellow-50' },
//     2: { text: 'Bajo', color: 'text-orange-600 bg-orange-50' },
//     1: { text: 'Muy Bajo', color: 'text-red-600 bg-red-50' },
//     0: { text: 'No evaluado', color: 'text-gray-600 bg-gray-50' }
// };

// // Función para convertir valor numérico a badge con texto y color
// function createProgressBadge(value) {
//     const progress = progressMapping[value] || progressMapping[0];
//     return `<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${progress.color}">${progress.text}</span>`;
// }

// function showLastFollowUpDetails(followUp) {
//     try {
//         console.log('Mostrando detalles del seguimiento:', followUp);

//         // Mostrar badges con texto y colores para los campos de progreso
//         document.getElementById('buddy_progress_stars').innerHTML = createProgressBadge(followUp.buddy_progress);
//         document.getElementById('peer_buddy_progress_stars').innerHTML = createProgressBadge(followUp.peer_buddy_progress);
//         document.getElementById('relationship_quality_stars').innerHTML = createProgressBadge(followUp.relationship_quality);
        
//         // Llenar campos de texto
//         document.getElementById('goals_achieved').textContent = followUp.goals_achieved || 'No especificados';
//         document.getElementById('challenges_faced').textContent = followUp.challenges_faced || 'No especificados';
//         document.getElementById('recommendations').textContent = followUp.recommendations || 'No especificadas';
//         document.getElementById('next_steps').textContent = followUp.next_steps || 'No especificados';
        
//         // Información del usuario y fecha
//         document.getElementById('follow_up_user').textContent = followUp.user ? followUp.user.name : 'Usuario desconocido';
//         document.getElementById('follow_up_date').textContent = new Date(followUp.created_at).toLocaleDateString('es-ES');
        
//         // Próximo seguimiento
//         const nextFollowUpInfo = document.getElementById('next_follow_up_info');
//         if (followUp.next_follow_up_date) {
//             nextFollowUpInfo.innerHTML = `
//                 <p class="text-sm text-blue-600">
//                     <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
//                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
//                     </svg>
//                     Próximo seguimiento programado: ${new Date(followUp.next_follow_up_date).toLocaleDateString('es-ES')}
//                 </p>
//             `;
//         } else {
//             nextFollowUpInfo.innerHTML = '';
//         }
//     } catch (error) {
//         console.error('Error al mostrar detalles del seguimiento:', error);
//     }
// }

// // Función para restaurar el contenido original del modal
// function restoreModalContent() {
//     const modal = document.getElementById('view-friendship-modal');
//     // Esto restaura el HTML original del modal que definiste en tu blade
//     modal.innerHTML = `
//         <div class="bg-white rounded-lg shadow-xl p-6 max-w-6xl max-h-[90vh] overflow-y-auto">
//             <!-- Aquí va todo el contenido HTML original de tu modal -->
//             ${document.getElementById('view-friendship-modal').innerHTML}
//         </div>
//     `;
// }

// function getStatusBadgeClass(status) {
//     if (!status) return 'bg-gray-100 text-gray-800';
    
//     switch (status) {
//         case 'Emparejado':
//             return 'bg-green-100 text-green-800';
//         case 'Inactivo':
//             return 'bg-yellow-100 text-yellow-800';
//         case 'Finalizado':
//             return 'bg-red-100 text-red-800';
//         default:
//             return 'bg-gray-100 text-gray-800';
//     }
// }

// function closeViewFriendshipModal() {
//     document.getElementById('view-friendship-modal').classList.add('hidden');
// }

// // Función para abrir el modal de seguimiento
// function openTrackingFriendshipModal(friendshipId) {
//     // Hacer petición AJAX para obtener los datos del friendship
//     fetch(`/friendships/${friendshipId}/tracking`)
//         .then(response => response.json())
//         .then(data => {
//             // Poblar el modal con los datos
//             populateTrackingModal(data.friendship);
            
//             // Mostrar el modal
//             document.getElementById('tracking-friendship-modal').classList.remove('hidden');
//         })
//         .catch(error => {
//             console.error('Error:', error);
//             alert('Error al cargar los datos del emparejamiento');
//         });
// }

// // Función para cerrar el modal de seguimiento
// function closeTrackingFriendshipModal() {
//     document.getElementById('tracking-friendship-modal').classList.add('hidden');
//     // Limpiar el formulario
//     document.getElementById('tracking-friendship-form').reset();
//     // Volver a la primera pestaña por defecto
//     switchTab('attendance');
// }


</script>

