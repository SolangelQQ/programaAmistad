<div id="edit-activity-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg p-6 max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-medium text-gray-900">Editar Actividad</h2>
            <button type="button" @click="closeEditModal()" class="text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <form id="edit-activity-form" @submit.prevent="submitEditForm()">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div class="md:col-span-2">
                    <label for="edit_activity_title" class="block text-sm font-medium text-gray-700">Título</label>
                    <input type="text" id="edit_activity_title" name="title" required class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>
                
                <div>
                    <label for="edit_activity_date" class="block text-sm font-medium text-gray-700">Fecha</label>
                    <input type="date" id="edit_activity_date" name="date" required class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>
                
                <div>
                    <label for="edit_activity_status" class="block text-sm font-medium text-gray-700">Estado</label>
                    <select id="edit_activity_status" name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                        <option value="programada">Programada</option>
                        <option value="en_progreso">En Progreso</option>
                        <option value="completada">Completada</option>
                        <option value="cancelada">Cancelada</option>
                    </select>
                </div>
                
                <div>
                    <label for="edit_activity_start_time" class="block text-sm font-medium text-gray-700">Hora de inicio</label>
                    <input type="time" id="edit_activity_start_time" name="start_time" required class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>
                
                <div>
                    <label for="edit_activity_end_time" class="block text-sm font-medium text-gray-700">Hora de fin</label>
                    <input type="time" id="edit_activity_end_time" name="end_time" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>
            </div>
            
            <div class="mb-4">
                <label for="edit_activity_location" class="block text-sm font-medium text-gray-700">Ubicación</label>
                <input type="text" id="edit_activity_location" name="location" required class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
            </div>
            
            <div class="mb-4">
                <label for="edit_activity_description" class="block text-sm font-medium text-gray-700">Descripción</label>
                <textarea id="edit_activity_description" name="description" rows="3" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="edit_max_buddies" class="block text-sm font-medium text-gray-700">Máx. Buddies</label>
                    <input type="number" id="edit_max_buddies" name="max_buddies" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>
                
                <div>
                    <label for="edit_max_peerbuddies" class="block text-sm font-medium text-gray-700">Máx. PeerBuddies</label>
                    <input type="number" id="edit_max_peerbuddies" name="max_peerbuddies" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" @click="closeEditModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Actualizar Actividad
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function submitEditForm() {
    const form = document.getElementById('edit-activity-form');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    
    const activityId = document.querySelector('[x-data]').__x.$data.editingActivity.id;
    document.querySelector('[x-data]').__x.$data.updateActivity(activityId, data);
}

function handlePhotoUpload(event) {
    const files = event.target.files;
    const activityId = document.querySelector('[x-data]').__x.$data.editingActivity.id;
    
    if (files.length > 0) {
        const formData = new FormData();
        for (let file of files) {
            formData.append('photos[]', file);
        }
        
        uploadPhotos(activityId, formData);
    }
}

async function uploadPhotos(activityId, formData) {
    try {
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        
        const response = await fetch(`/activities/${activityId}/photos`, {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        if (result.success) {
            document.querySelector('[x-data]').__x.$data.showNotification('Fotos subidas exitosamente', 'success');
            displayPhotoPreview(result.photos);
        }
    } catch (error) {
        console.error('Error uploading photos:', error);
        document.querySelector('[x-data]').__x.$data.showNotification('Error al subir las fotos', 'error');
    }
}

function displayPhotoPreview(photos) {
    const preview = document.getElementById('photo-preview');
    preview.innerHTML = '';
    
    photos.forEach(photo => {
        const img = document.createElement('img');
        img.src = `/storage/${photo}`;
        img.className = 'w-full h-20 object-cover rounded-md';
        preview.appendChild(img);
    });
}
</script>