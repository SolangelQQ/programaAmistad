<div id="edit-activity-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg p-6 max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-medium text-gray-900">Editar Actividad</h2>
            <button type="button" onclick="closeEditActivityModal()" class="text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <form id="edit-activity-form" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit_activity_id" name="activity_id">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Título -->
                <div class="md:col-span-2">
                    <label for="edit_activity_title" class="block text-sm font-medium text-gray-700">Título *</label>
                    <input type="text" id="edit_activity_title" name="title" required 
                           class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>
                
                <!-- Fecha y Horas -->
                <div>
                    <label for="edit_activity_date" class="block text-sm font-medium text-gray-700">Fecha *</label>
                    <input type="date" id="edit_activity_date" name="date" required 
                           class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>
                <div>
                    <label for="edit_activity_start_time" class="block text-sm font-medium text-gray-700">Hora Inicio *</label>
                    <input type="time" id="edit_activity_start_time" name="start_time" required 
                           class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>
                <div>
                    <label for="edit_activity_end_time" class="block text-sm font-medium text-gray-700">Hora Fin</label>
                    <input type="time" id="edit_activity_end_time" name="end_time" 
                           class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>
                
                <!-- Tipo y Estado -->
                <div>
                    <label for="edit_activity_type" class="block text-sm font-medium text-gray-700">Tipo *</label>
                    <select id="edit_activity_type" name="type" required 
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                        <option value="">Seleccionar tipo</option>
                        <option value="recreational">Recreativa</option>
                        <option value="educational">Educativa</option>
                        <option value="cultural">Cultural</option>
                        <option value="sports">Deportiva</option>
                        <option value="social">Social</option>
                    </select>
                </div>
                <div>
                    <label for="edit_activity_status" class="block text-sm font-medium text-gray-700">Estado *</label>
                    <select id="edit_activity_status" name="status" required 
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                        <option value="scheduled">Programada</option>
                        <option value="in_progress">En Progreso</option>
                        <option value="completed">Completada</option>
                        <option value="cancelled">Cancelada</option>
                    </select>
                </div>
                
                <!-- Ubicación -->
                <div class="md:col-span-2">
                    <label for="edit_activity_location" class="block text-sm font-medium text-gray-700">Ubicación *</label>
                    <input type="text" id="edit_activity_location" name="location" required 
                           class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>
                
              
                
                <!-- Asignación de Buddy/PeerBuddies -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Participantes Asignados</label>
                    
                    <!-- Buddy -->
                    <div class="mb-4">
                        <label for="edit_activity_buddy_id" class="block text-sm font-medium text-gray-700 mb-1">Buddy</label>
                        <select id="edit_activity_buddy_id" name="buddy_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="">Sin Buddy asignado</option>
                            @foreach($availableBuddies as $buddy)
                                <option value="{{ $buddy->id }}">{{ $buddy->full_name }} ({{ $buddy->disability }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- PeerBuddies -->
                    <div class="mb-4">
                        <label for="edit_activity_peer_buddy_id" class="block text-sm font-medium text-gray-700 mb-1">PeerBuddy 1</label>
                        <select id="edit_activity_peer_buddy_id" name="peer_buddy_ids[]" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="">Sin PeerBuddy asignado</option>
                            @foreach($availablePeerBuddies as $peerBuddy)
                                <option value="{{ $peerBuddy->id }}">{{ $peerBuddy->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label for="edit_activity_peer_buddy_id_2" class="block text-sm font-medium text-gray-700 mb-1">PeerBuddy 2 (opcional)</label>
                        <select id="edit_activity_peer_buddy_id_2" name="peer_buddy_ids[]" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="">Sin segundo PeerBuddy</option>
                            @foreach($availablePeerBuddies as $peerBuddy)
                                <option value="{{ $peerBuddy->id }}">{{ $peerBuddy->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
           
                
                <!-- Descripción -->
                <div class="md:col-span-2">
                    <label for="edit_activity_description" class="block text-sm font-medium text-gray-700">Descripción</label>
                    <textarea id="edit_activity_description" name="description" rows="3" 
                              class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="closeEditActivityModal()" 
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    Cancelar
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Actualizar Actividad
                </button>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript para manejar la edición -->
<script>
function loadActivityData(activityId) {
    fetch(`/activities/${activityId}/edit`)
        .then(response => response.json())
        .then(data => {
            // Llenar campos básicos
            document.getElementById('edit_activity_id').value = data.id;
            document.getElementById('edit_activity_title').value = data.title;
            document.getElementById('edit_activity_date').value = data.date;
            document.getElementById('edit_activity_start_time').value = data.start_time;
            document.getElementById('edit_activity_end_time').value = data.end_time;
            document.getElementById('edit_activity_type').value = data.type;
            document.getElementById('edit_activity_status').value = data.status;
            document.getElementById('edit_activity_location').value = data.location;
          
            document.getElementById('edit_activity_description').value = data.description;
            
            // Asignar participantes
            document.getElementById('edit_activity_buddy_id').value = data.buddy_id || '';
            
            // PeerBuddies (pueden ser 1 o 2)
            const peerBuddySelects = [
                document.getElementById('edit_activity_peer_buddy_id'),
                document.getElementById('edit_activity_peer_buddy_id_2')
            ];
            
            data.peer_buddies.forEach((peerBuddy, index) => {
                if (index < 2) {
                    peerBuddySelects[index].value = peerBuddy.id;
                }
            });
        })
            
}
</script>