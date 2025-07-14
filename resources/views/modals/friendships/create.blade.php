<!-- Create Friendship Modal -->
<div id="new-friendship-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg p-6 max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-medium text-gray-900">Nuevo Emparejamiento</h2>
            <button type="button" onclick="closeNewFriendshipModal()" class="text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <form id="new-friendship-form" method="POST" action="{{ route('friendships.store') }}">
            @csrf
            
            <!-- Sección de Participantes -->
            <div class="mb-6">
                <h3 class="text-md font-medium text-gray-800 mb-4 border-b pb-2">Participantes del Emparejamiento</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Buddy Selection -->
                    <div>
                        <label for="buddy_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Buddy <span class="text-red-500">*</span>
                        </label>
                        <select id="buddy_id" name="buddy_id" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Seleccionar Buddy</option>
                            @foreach($availableBuddies as $buddy)
                                <option value="{{ $buddy->id }}">{{ $buddy->full_name }} ({{ $buddy->disability }})</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Persona con discapacidad</p>
                    </div>
                    
                    <!-- PeerBuddy Selection -->
                    <div>
                        <label for="peer_buddy_id" class="block text-sm font-medium text-gray-700 mb-2">
                            PeerBuddy <span class="text-red-500">*</span>
                        </label>
                        <select id="peer_buddy_id" name="peer_buddy_id" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Seleccionar PeerBuddy</option>
                            @foreach($availablePeerBuddies as $peerBuddy)
                                <option value="{{ $peerBuddy->id }}">{{ $peerBuddy->full_name }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Persona sin discapacidad</p>
                    </div>
                </div>
            </div>

            <!-- Sección de Líderes -->
            <div class="mb-6">
                <h3 class="text-md font-medium text-gray-800 mb-4 border-b pb-2">Líderes Asignados</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Buddy Leader Selection - CORREGIDO -->
                    <div>
                        <label for="buddy_leader_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Líder de Buddy <span class="text-red-500">*</span>
                        </label>
                        <select id="buddy_leader_id" name="buddy_leader_id" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Seleccionar Líder de Buddy</option>
                            @if(isset($availableBuddyLeaders) && count($availableBuddyLeaders) > 0)
                                @foreach($availableBuddyLeaders as $buddyLeader)
                                    <option value="{{ $buddyLeader->id }}">{{ $buddyLeader->name }}</option>
                                @endforeach
                            @else
                                <option value="" disabled>No hay líderes de buddy disponibles</option>
                            @endif
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Responsable de manejar la comunicación con todos los buddies y evaluar su experiencia en el programa</p>
                    </div>
                    
                    <!-- PeerBuddy Leader Selection - CORREGIDO -->
                    <div>
                        <label for="peer_buddy_leader_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Líder de PeerBuddy <span class="text-red-500">*</span>
                        </label>
                        <select id="peer_buddy_leader_id" name="peer_buddy_leader_id" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Seleccionar Líder de PeerBuddy</option>
                            @if(isset($availablePeerBuddyLeaders) && count($availablePeerBuddyLeaders) > 0)
                                @foreach($availablePeerBuddyLeaders as $peerBuddyLeader)
                                    <option value="{{ $peerBuddyLeader->id }}">{{ $peerBuddyLeader->name }}</option>
                                @endforeach
                            @else
                                <option value="" disabled>No hay líderes de peer buddy disponibles</option>
                            @endif
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Responsable de coordinación y control de cumplimiento de las actividades que realizan los peerbuddies</p>
                    </div>
                </div>
            </div>

            <!-- Sección de Detalles del Emparejamiento -->
            <div class="mb-6">
                <h3 class="text-md font-medium text-gray-800 mb-4 border-b pb-2">Detalles del Emparejamiento</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Start Date -->
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Fecha de Inicio <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="start_date" name="start_date" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    
                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Estado <span class="text-red-500">*</span>
                        </label>
                        <select id="status" name="status" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="Emparejado">Emparejado</option>
                            <option value="Inactivo">Inactivo</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Notes -->
            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notas</label>
                <textarea id="notes" name="notes" rows="3" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Observaciones adicionales sobre el emparejamiento..."></textarea>
            </div>
            
            <div class="flex justify-end space-x-3 pt-4 border-t">
                <button type="button" onclick="closeNewFriendshipModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Crear Emparejamiento
                </button>
            </div>
        </form>
    </div>
</div>

<!-- <script>
    document.addEventListener('DOMContentLoaded', function() {
    // Funcionalidad para abrir/cerrar modales de FRIENDSHIPS
    window.openNewFriendshipModal = function() {
        document.getElementById('new-friendship-modal').classList.remove('hidden');
    };
    
    window.closeNewFriendshipModal = function() {
        document.getElementById('new-friendship-modal').classList.add('hidden');
    };
    
    // Función para ver detalles de un emparejamiento
    window.viewFriendshipDetails = function(friendshipId) {
        fetch(`/friendships/${friendshipId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error al cargar los datos del emparejamiento');
                }
                return response.json();
            })
            .then(data => {
                // Información del Friendship
                document.getElementById('view_friendship_id').textContent = data.friendship.id;
                document.getElementById('view_start_date').textContent = data.friendship.start_date;
                document.getElementById('view_end_date').textContent = data.friendship.end_date || 'N/A';
                document.getElementById('view_notes').textContent = data.friendship.notes || 'Sin notas';
                
                // Configurar el badge de estado
                const statusBadge = document.getElementById('view_status_badge');
                statusBadge.textContent = data.friendship.status;
                
                // Aplicar colores según el estado
                if (data.friendship.status === 'Emparejado') {
                    statusBadge.classList.add('bg-green-100', 'text-green-800');
                    statusBadge.classList.remove('bg-red-100', 'text-red-800', 'bg-yellow-100', 'text-yellow-800');
                } else if (data.friendship.status === 'Inactivo') {
                    statusBadge.classList.add('bg-red-100', 'text-red-800');
                    statusBadge.classList.remove('bg-green-100', 'text-green-800', 'bg-yellow-100', 'text-yellow-800');
                } else {
                    statusBadge.classList.add('bg-yellow-100', 'text-yellow-800');
                    statusBadge.classList.remove('bg-green-100', 'text-green-800', 'bg-red-100', 'text-red-800');
                }
                
                // Información del Buddy
                document.getElementById('view_buddy_name').textContent = data.buddy.first_name + ' ' + data.buddy.last_name;
                document.getElementById('view_buddy_disability').textContent = data.buddy.disability || 'N/A';
                document.getElementById('view_buddy_age').textContent = `${data.buddy.age} años`;
                document.getElementById('view_buddy_ci').textContent = data.buddy.ci;
                document.getElementById('view_buddy_phone').textContent = data.buddy.phone;
                document.getElementById('view_buddy_email').textContent = data.buddy.email || 'N/A';
                document.getElementById('view_buddy_address').textContent = data.buddy.address;
                
                // Información del PeerBuddy
                document.getElementById('view_peerbuddy_name').textContent = data.peerBuddy.first_name + ' ' + data.peerBuddy.last_name;
                document.getElementById('view_peerbuddy_age').textContent = `${data.peerBuddy.age} años`;
                document.getElementById('view_peerbuddy_ci').textContent = data.peerBuddy.ci;
                document.getElementById('view_peerbuddy_phone').textContent = data.peerBuddy.phone;
                document.getElementById('view_peerbuddy_email').textContent = data.peerBuddy.email || 'N/A';
                document.getElementById('view_peerbuddy_address').textContent = data.peerBuddy.address;
                
                // Mostrar el modal
                document.getElementById('view-friendship-modal').classList.remove('hidden');
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al cargar los datos. Por favor, inténtalo de nuevo.');
            });
    };
    
    window.closeViewFriendshipModal = function() {
        document.getElementById('view-friendship-modal').classList.add('hidden');
    };
    
    window.openEditFriendshipModal = function(friendshipId, startDate, endDate, status, notes) {
        const form = document.getElementById('edit-friendship-form');
        form.action = `/friendships/${friendshipId}`;
        
        document.getElementById('edit_start_date').value = startDate;
        document.getElementById('edit_end_date').value = endDate || '';
        document.getElementById('edit_status').value = status;
        document.getElementById('edit_notes').value = notes || '';
        
        document.getElementById('edit-friendship-modal').classList.remove('hidden');
    };
    
    window.closeEditFriendshipModal = function() {
        document.getElementById('edit-friendship-modal').classList.add('hidden');
    };
    
    // Funcionalidad de confirmación de eliminación de FRIENDSHIPS
    window.confirmDelete = function(friendshipId) {
        const form = document.getElementById('delete-friendship-form');
        form.action = `/friendships/${friendshipId}`;
        document.getElementById('delete-confirmation-modal').classList.remove('hidden');
    };
    
    window.closeDeleteModal = function() {
        document.getElementById('delete-confirmation-modal').classList.add('hidden');
    };
    
    // Funcionalidad para eliminar buddies
    window.confirmDeleteBuddy = function(buddyId) {
        const form = document.getElementById('delete-buddy-form');
        form.action = `/buddies/${buddyId}`;
        document.getElementById('delete-buddy-modal').classList.remove('hidden');
    };
    
    window.closeDeleteBuddyModal = function() {
        document.getElementById('delete-buddy-modal').classList.add('hidden');
    };
    
    // Funcionalidad del sistema de tabs
    const switchToTab = function(targetSectionId) {
        console.log('Switching to tab:', targetSectionId);
        
        // Lista de todas las secciones posibles
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
    };
    
    // Event listeners para las pestañas
    document.querySelectorAll('.tab-link').forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('data-target');
            console.log('Tab clicked, target:', targetId);
            switchToTab(targetId);
        });
    });
    
    // Inicialización: mostrar la pestaña de amistades por defecto
    setTimeout(() => {
        switchToTab('friendships-section');
    }, 100);
    
    
    // Validación de formularios de FRIENDSHIPS
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
});
</script> -->