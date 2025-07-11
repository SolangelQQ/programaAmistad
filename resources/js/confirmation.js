// Función para mostrar un modal de confirmación personalizado
function showConfirmationModal(action, type, userId) {
    // Crear el elemento del modal
    const modal = document.createElement('div');
    modal.id = 'confirmationModal';
    modal.className = 'fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50';
    
    // Definir el mensaje según la acción
    let message = '¿Estás seguro?';
    let confirmButtonText = 'Confirmar';
    let confirmButtonClass = 'bg-blue-600 hover:bg-blue-700';
    
    if (type === 'delete') {
        message = '¿Estás seguro de que deseas eliminar este usuario?';
        confirmButtonText = 'Eliminar';
        confirmButtonClass = 'bg-red-600 hover:bg-red-700';
    }
    
    // Contenido del modal
    modal.innerHTML = `
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <div class="mb-4">
                <h3 class="text-lg font-medium text-gray-900">${message}</h3>
                <p class="text-sm text-gray-500 mt-2">Esta acción no se puede deshacer.</p>
            </div>
            <div class="flex justify-end space-x-3">
                <button id="cancelButton" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md">
                    Cancelar
                </button>
                <button id="confirmButton" class="px-4 py-2 ${confirmButtonClass} text-white rounded-md">
                    ${confirmButtonText}
                </button>
            </div>
        </div>
    `;
    
    // Añadir el modal al documento
    document.body.appendChild(modal);
    
    // Manejar los eventos de los botones
    return new Promise((resolve) => {
        document.getElementById('cancelButton').addEventListener('click', () => {
            document.body.removeChild(modal);
            resolve(false);
        });
        
        document.getElementById('confirmButton').addEventListener('click', () => {
            document.body.removeChild(modal);
            resolve(true);
        });
    });
}

// Para usar en los formularios de eliminación
async function confirmDelete(event, formId) {
    event.preventDefault();
    const confirmed = await showConfirmationModal('eliminar', 'delete');
    if (confirmed) {
        document.getElementById(formId).submit();
    }
}

// Para manejar automáticamente todas las notificaciones
document.addEventListener('DOMContentLoaded', function() {
    // Auto ocultar notificaciones después de 5 segundos
    setTimeout(function() {
        const notifications = document.querySelectorAll('#notification, #error-notification');
        notifications.forEach(notification => {
            if (notification) {
                notification.style.opacity = '0';
                notification.style.transition = 'opacity 1s';
                setTimeout(function() {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 1000);
            }
        });
    }, 5000);
});