<!-- Edit Friendship Modal (Solo datos básicos) -->
<div id="edit-friendship-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg p-6 max-w-3xl w-full max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-medium text-gray-900">Editar Emparejamiento</h2>
            <button type="button" onclick="closeEditFriendshipModal()" class="text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <form id="edit-friendship-form" action="" method="POST">
            @csrf
            @method('PUT')

            <!-- Información de participantes (solo lectura) -->
            <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                <h3 class="text-md font-medium text-gray-800 mb-3">Participantes del Emparejamiento</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="border-l-4 border-blue-500 pl-4">
                        <h4 class="font-medium text-blue-700">Buddy</h4>
                        <p id="edit-buddy-name" class="text-sm text-gray-600"></p>
                        <p id="edit-buddy-disability" class="text-xs text-gray-500"></p>
                    </div>
                    <div class="border-l-4 border-green-500 pl-4">
                        <h4 class="font-medium text-green-700">PeerBuddy</h4>
                        <p id="edit-peer-buddy-name" class="text-sm text-gray-600"></p>
                        <p id="edit-peer-buddy-contact" class="text-xs text-gray-500"></p>
                    </div>
                </div>
            </div>

            <!-- Leaders Section -->
            <div class="mb-6">
                <h3 class="text-md font-medium text-gray-800 mb-4 border-b pb-2">Líderes Asignados</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Buddy Leader Selection -->
                    <div>
                        <label for="edit_buddy_leader_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Líder de Buddy <span class="text-red-500">*</span>
                        </label>
                        <select id="edit_buddy_leader_id" name="buddy_leader_id" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Seleccionar Líder de Buddy</option>
                            @foreach($availableBuddyLeaders as $buddyLeader)
                                <option value="{{ $buddyLeader->id }}">{{ $buddyLeader->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- PeerBuddy Leader Selection -->
                    <div>
                        <label for="edit_peer_buddy_leader_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Líder de PeerBuddy <span class="text-red-500">*</span>
                        </label>
                        <select id="edit_peer_buddy_leader_id" name="peer_buddy_leader_id" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Seleccionar Líder de PeerBuddy</option>
                            @foreach($availablePeerBuddyLeaders as $peerBuddyLeader)
                                <option value="{{ $peerBuddyLeader->id }}">{{ $peerBuddyLeader->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Status and Dates Section -->
            <div class="mb-6">
                <h3 class="text-md font-medium text-gray-800 mb-4 border-b pb-2">Estado y Fechas</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Start Date -->
                    <div>
                        <label for="edit_start_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Fecha de Inicio <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="edit_start_date" name="start_date" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    
                    <!-- End Date -->
                    <div>
                        <label for="edit_end_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Fecha de Fin
                        </label>
                        <input type="date" id="edit_end_date" name="end_date" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    
                    <!-- Status -->
                    <div>
                        <label for="edit_status" class="block text-sm font-medium text-gray-700 mb-2">
                            Estado <span class="text-red-500">*</span>
                        </label>
                        <select id="edit_status" name="status" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="Emparejado">Emparejado</option>
                            <option value="Inactivo">Inactivo</option>
                            <option value="Finalizado">Finalizado</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Notes -->
            <div class="mb-6">
                <label for="edit_notes" class="block text-sm font-medium text-gray-700 mb-2">Notas Generales</label>
                <textarea id="edit_notes" name="notes" rows="3" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Observaciones generales sobre el emparejamiento..."></textarea>
            </div>
            
            <div class="flex justify-end space-x-3 pt-4 border-t">
                <button type="button" onclick="closeEditFriendshipModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Function to populate the edit modal with friendship data
function populateEditModal(friendship) {
    // Set form action URL
    document.getElementById('edit-friendship-form').action = `/friendships/${friendship.id}`;
    
    // Populate participant info (display only)
    document.getElementById('edit-buddy-name').textContent = `${friendship.buddy.first_name} ${friendship.buddy.last_name}`;
    document.getElementById('edit-buddy-disability').textContent = `Discapacidad: ${friendship.buddy.disability || 'N/A'}`;
    
    document.getElementById('edit-peer-buddy-name').textContent = `${friendship.peerBuddy.first_name} ${friendship.peerBuddy.last_name}`;
    document.getElementById('edit-peer-buddy-contact').textContent = `Contacto: ${friendship.peerBuddy.phone} | ${friendship.peerBuddy.email || 'Sin email'}`;
    
    // Populate form fields
    document.getElementById('edit_start_date').value = friendship.start_date;
    document.getElementById('edit_end_date').value = friendship.end_date || '';
    document.getElementById('edit_status').value = friendship.status;
    document.getElementById('edit_notes').value = friendship.notes || '';
    
    // Select leaders
    if (friendship.buddy_leader_id) {
        document.getElementById('edit_buddy_leader_id').value = friendship.buddy_leader_id;
    }
    if (friendship.peer_buddy_leader_id) {
        document.getElementById('edit_peer_buddy_leader_id').value = friendship.peer_buddy_leader_id;
    }
}

// Function to open the edit modal
function openEditFriendshipModal(friendshipId) {
    fetch(`/friendships/${friendshipId}`)
        .then(response => response.json())
        .then(data => {
            populateEditModal(data.friendship);
            document.getElementById('edit-friendship-modal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar los datos del emparejamiento');
        });
}

// Function to close the edit modal
function closeEditFriendshipModal() {
    document.getElementById('edit-friendship-modal').classList.add('hidden');
}
</script>