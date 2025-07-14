<div id="photos-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg p-6 max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-medium text-gray-900">Gestión de Fotos</h2>
            <button type="button" onclick="closePhotosModal()" class="text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <div class="mb-6">
            <h3 id="photos-activity-title" class="font-semibold text-gray-900 mb-2">Cargando...</h3>
            <p id="photos-activity-date" class="text-sm text-gray-600">Cargando fecha...</p>
        </div>
        
        <!-- Sección de Subida -->
        <div class="mb-6 p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-blue-400 transition-colors">
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <div class="mt-4">
                    <label for="photo-upload" class="cursor-pointer">
                        <span class="mt-2 block text-sm font-medium text-gray-900">
                            Haz clic aquí o arrastra fotos para subirlas
                        </span>
                        <input id="photo-upload" name="photos[]" type="file" class="sr-only" multiple accept="image/*">
                    </label>
                    <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF hasta 2MB cada una</p>
                </div>
            </div>
        </div>
        
        <!-- Progreso de Subida -->
        <div id="upload-progress" class="hidden mb-4">
            <div class="bg-blue-200 rounded-full h-2">
                <div id="upload-progress-bar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
            </div>
            <p id="upload-status" class="text-sm text-gray-600 mt-2">Subiendo fotos...</p>
        </div>
        
        <!-- Galería de Fotos -->
        <div id="photos-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4"></div>
        
        <!-- Estado Vacío -->
        <div id="photos-empty" class="text-center py-8 text-gray-500">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <p class="mt-2 font-medium">No hay fotos para esta actividad</p>
            <p class="text-sm">Sube algunas fotos para documentar la actividad</p>
        </div>
        
        <div class="flex justify-end mt-6">
            <button type="button" onclick="closePhotosModal()" 
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                Cerrar
            </button>
        </div>
    </div>
</div>

<!-- Modal de Vista Previa de Foto -->
<div id="photo-preview-modal" class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center hidden z-60">
    <div class="relative max-w-4xl max-h-full p-4">
        <button onclick="closePhotoPreview()" class="absolute top-4 right-4 text-white hover:text-gray-300 z-10 bg-black bg-opacity-50 rounded-full p-2">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        <img id="photo-preview-img" src="" alt="Preview" class="max-w-full max-h-full object-contain rounded-lg">
        <div class="absolute bottom-4 right-4">
            <button id="delete-photo-btn" onclick="deleteCurrentPhoto()" 
                    class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Eliminar Foto
            </button>
        </div>
    </div>
</div>

<!-- <script src="/js/photo-manager.js"></script> -->
<script>
    class PhotoManager {

    static MAX_PHOTOS_PER_ACTIVITY = 10;

    constructor() {
        this.currentActivityId = null;
        this.photos = [];
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.setupDragAndDrop();
    }

    setupEventListeners() {
        const photoUpload = document.getElementById('photo-upload');
        if (photoUpload) {
            photoUpload.addEventListener('change', (e) => this.handlePhotoUpload(e));
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeAllModals();
            }
        });
    }

    setupDragAndDrop() {
        const dropZone = document.querySelector('#photo-upload')?.parentElement;
        if (!dropZone) return;

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, this.preventDefaults, false);
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => this.highlight(dropZone), false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => this.unhighlight(dropZone), false);
        });

        dropZone.addEventListener('drop', (e) => this.handleDrop(e), false);
    }

    preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    highlight(dropZone) {
        dropZone.classList.add('border-blue-500', 'bg-blue-50');
    }

    unhighlight(dropZone) {
        dropZone.classList.remove('border-blue-500', 'bg-blue-50');
    }

    handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        
        if (files.length > 0 && this.currentActivityId) {
            this.uploadPhotos(files);
        }
    }

    async openPhotosModal(activityId) {
        this.currentActivityId = activityId;
        
        try {
            const response = await fetch(`/activities/${activityId}`);
            if (!response.ok) throw new Error('Error al cargar la actividad');
            
            const activity = await response.json();
            
            // Actualizar información en el modal
            const titleElement = document.getElementById('photos-activity-title');
            const dateElement = document.getElementById('photos-activity-date');
            
            if (titleElement) titleElement.textContent = activity.title || 'Actividad';
            if (dateElement) {
                const formattedDate = activity.formatted_date || 
                    (activity.date ? new Date(activity.date).toLocaleDateString('es-ES') : 'Sin fecha');
                dateElement.textContent = formattedDate;
            }
            
            // Cargar fotos
            this.photos = activity.photos || [];
            this.displayPhotosGallery();
            
            // Mostrar modal
            const modal = document.getElementById('photos-modal');
            if (modal) {
                modal.classList.remove('hidden');
            }
            
        } catch (error) {
            console.error('Error opening photos modal:', error);
            this.showNotification('Error al abrir el modal de fotos', 'error');
        }
    }

    closePhotosModal() {
        const modal = document.getElementById('photos-modal');
        if (modal) {
            modal.classList.add('hidden');
        }
        this.currentActivityId = null;
        this.photos = [];
    }

    closePhotoPreview() {
        const modal = document.getElementById('photo-preview-modal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    closeAllModals() {
        this.closePhotosModal();
        this.closePhotoPreview();
    }

    async handlePhotoUpload(event) {
        const files = event.target.files;
        if (files.length > 0 && this.currentActivityId) {
            await this.uploadPhotos(files);
        }
    }


    async uploadPhotos(files) {
        if (!this.currentActivityId) {
            this.showNotification('No hay actividad seleccionada', 'error');
            return;
        }
        

        if (this.photos.length + files.length > PhotoManager.MAX_PHOTOS_PER_ACTIVITY) {
            this.showNotification(`Máximo ${PhotoManager.MAX_PHOTOS_PER_ACTIVITY} fotos por actividad`, 'error');
            return;
        }

        // Validar archivos
        const validFiles = Array.from(files).filter(file => {
            if (!file.type.startsWith('image/')) {
                this.showNotification(`${file.name} no es una imagen válida`, 'error');
                return false;
            }
            if (file.size > 2 * 1024 * 1024) { // 2MB
                this.showNotification(`${file.name} es muy grande (máximo 2MB)`, 'error');
                return false;
            }
            return true;
        });

        if (validFiles.length === 0) return;

        this.showUploadProgress(true);

        const formData = new FormData();
        validFiles.forEach(file => {
            formData.append(`photos[]`, file);
        });

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            
            const response = await fetch(`/activities/${this.currentActivityId}/photos`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            
            if (result.success) {
                this.showNotification(`${validFiles.length} foto(s) subida(s) exitosamente`, 'success');
                // Recargar las fotos
                await this.reloadPhotos();
                this.clearFileInputs();
            } else {
                throw new Error(result.message || 'Error al subir las fotos');
            }

        } catch (error) {
            console.error('Error uploading photos:', error);
            this.showNotification('Error al subir las fotos: ' + error.message, 'error');
        } finally {
            this.showUploadProgress(false);
        }
    }

    async reloadPhotos() {
        if (!this.currentActivityId) return;
        
        try {
            const response = await fetch(`/activities/${this.currentActivityId}`);
            const activity = await response.json();
            this.photos = activity.photos || [];
            this.displayPhotosGallery();
        } catch (error) {
            console.error('Error reloading photos:', error);
        }
    }

    displayPhotosGallery() {
        const gallery = document.getElementById('photos-grid');
        const emptyState = document.getElementById('photos-empty');
        
        if (!gallery) return;

        gallery.innerHTML = '';

        if (this.photos.length === 0) {
            if (emptyState) emptyState.classList.remove('hidden');
            return;
        }

        if (emptyState) emptyState.classList.add('hidden');

        this.photos.forEach((photoPath, index) => {
            const photoContainer = document.createElement('div');
            photoContainer.className = 'relative group cursor-pointer';
            
            // Construir la URL correcta para la imagen
            const imageUrl = `/storage/${photoPath}`;
            
            photoContainer.innerHTML = `
                <img src="${imageUrl}" 
                     alt="Foto ${index + 1}" 
                     class="w-full h-32 object-cover rounded-lg hover:opacity-75 transition-opacity"
                     onclick="photoManager.openPhotoPreview('${photoPath}', ${index})"
                     onerror="this.src='/images/no-image.png'; this.onerror=null;">
                <button onclick="photoManager.deletePhoto('${photoPath}', ${index})" 
                        class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            `;
            
            gallery.appendChild(photoContainer);
        });
    }

    openPhotoPreview(photoPath, index) {
    const modal = document.getElementById('photo-preview-modal');
    const img = document.getElementById('photo-preview-img');
    
    if (modal && img) {
        const imageUrl = `/storage/${photoPath}`;
        img.src = imageUrl;
        img.dataset.photoPath = photoPath;
        img.dataset.photoIndex = index;
        
        // Asegurar z-index máximo
        modal.style.zIndex = '9999';
        modal.classList.remove('hidden');
        
        // Prevenir scroll del body
        document.body.classList.add('modal-open');
        
        if (deleteBtn) {
            deleteBtn.onclick = () => this.deleteCurrentPhoto();
        }
    }
}

    async deletePhoto(photoPath, index) {
        if (!confirm('¿Estás seguro de que quieres eliminar esta foto?')) return;

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            
            const response = await fetch(`/activities/${this.currentActivityId}/photos`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    photo_path: photoPath
                })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            
            if (result.success) {
                this.showNotification('Foto eliminada exitosamente', 'success');
                // Recargar fotos
                await this.reloadPhotos();
                this.closePhotoPreview();
            } else {
                throw new Error(result.message || 'Error al eliminar la foto');
            }

        } catch (error) {
            console.error('Error deleting photo:', error);
            this.showNotification('Error al eliminar la foto: ' + error.message, 'error');
        }
    }

    deleteCurrentPhoto() {
        const img = document.getElementById('photo-preview-img');
        if (img && img.dataset.photoPath) {
            const photoPath = img.dataset.photoPath;
            const index = parseInt(img.dataset.photoIndex);
            this.deletePhoto(photoPath, index);
        }
    }

    showUploadProgress(show) {
        const progressContainer = document.getElementById('upload-progress');
        const progressBar = document.getElementById('upload-progress-bar');
        const statusText = document.getElementById('upload-status');

        if (progressContainer) {
            if (show) {
                progressContainer.classList.remove('hidden');
                if (progressBar) progressBar.style.width = '0%';
                if (statusText) statusText.textContent = 'Subiendo fotos...';
                
                let progress = 0;
                const interval = setInterval(() => {
                    progress += Math.random() * 30;
                    if (progress > 90) progress = 90;
                    if (progressBar) progressBar.style.width = progress + '%';
                    
                    if (progress >= 90) {
                        clearInterval(interval);
                    }
                }, 200);
                
            } else {
                if (progressBar) progressBar.style.width = '100%';
                if (statusText) statusText.textContent = 'Completado';
                
                setTimeout(() => {
                    progressContainer.classList.add('hidden');
                }, 1000);
            }
        }
    }

    clearFileInputs() {
        const photoUpload = document.getElementById('photo-upload');
        if (photoUpload) photoUpload.value = '';
    }

    showNotification(message, type = 'info') {
        const existing = document.getElementById('photo-notification');
        if (existing) existing.remove();

        const notification = document.createElement('div');
        notification.id = 'photo-notification';
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 ${
            type === 'success' ? 'bg-green-500 text-white' : 
            type === 'error' ? 'bg-red-500 text-white' : 
            'bg-blue-500 text-white'
        }`;
        notification.textContent = message;

        document.body.appendChild(notification);

        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }
}

// Inicializar
let photoManager;

document.addEventListener('DOMContentLoaded', function() {
    photoManager = new PhotoManager();

    window.photoManager = photoManager;
    window.openPhotosModal = (activityId) => photoManager.openPhotosModal(activityId);
    window.closePhotosModal = () => photoManager.closePhotosModal();
    window.closePhotoPreview = () => photoManager.closePhotoPreview();
    window.deleteCurrentPhoto = () => photoManager.deleteCurrentPhoto();
});

// Conectar con el calendario
document.addEventListener('click', function(e) {
    if (e.target.textContent.trim() === 'Fotos' && e.target.tagName === 'BUTTON') {
        const activityCard = e.target.closest('[data-activity-id]');
        if (activityCard && photoManager) {
            const activityId = activityCard.getAttribute('data-activity-id');
            photoManager.openPhotosModal(activityId);
        }
    }
});
</script>
<style>
.activity-card {
    transition: all 0.2s ease;
}

.activity-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

#photos-grid img {
    transition: all 0.3s ease;
}

#photos-grid img:hover {
    transform: scale(1.05);
}

#photo-preview-modal img {
    max-height: 85vh;
    max-width: 85vw;
}

.border-blue-500 {
    border-color: #3b82f6 !important;
}

.bg-blue-50 {
    background-color: #eff6ff !important;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.loading {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>