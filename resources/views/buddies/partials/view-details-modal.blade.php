<div id="view-details-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg p-6 max-w-2xl w-full m-4 max-h-screen overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Detalles de la Persona</h2>
            <button type="button" onclick="closeViewDetailsModal()" class="text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <div id="buddy-details-content">
            <!-- Avatar y Nombre -->
            <div class="flex items-center mb-6">
                <img id="detail-avatar" class="h-20 w-20 rounded-full object-cover mr-4" src="" alt="">
                <div>
                    <h3 id="detail-name" class="text-2xl font-bold text-gray-900"></h3>
                    <p id="detail-type" class="text-lg text-gray-600"></p>
                </div>
            </div>
            
            <!-- Información Personal -->
            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                <h4 class="text-lg font-semibold text-gray-900 mb-3">Información Personal</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Cédula de Identidad</label>
                        <p id="detail-ci" class="text-sm text-gray-900 font-medium"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Edad</label>
                        <p id="detail-age" class="text-sm text-gray-900 font-medium"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Teléfono</label>
                        <p id="detail-phone" class="text-sm text-gray-900 font-medium"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Email</label>
                        <p id="detail-email" class="text-sm text-gray-900 font-medium"></p>
                    </div>
                </div>
            </div>
            
            <!-- Información Específica del Tipo -->
            <div id="buddy-specific-info" class="bg-blue-50 rounded-lg p-4 mb-4">
                <h4 class="text-lg font-semibold text-gray-900 mb-3">Información Específica</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div id="disability-info" class="hidden">
                        <label class="block text-sm font-medium text-gray-500">Discapacidad</label>
                        <p id="detail-disability" class="text-sm text-gray-900 font-medium"></p>
                    </div>
                    <div id="experience-info" class="hidden">
                        <label class="block text-sm font-medium text-gray-500">Experiencia</label>
                        <p id="detail-experience" class="text-sm text-gray-900 font-medium"></p>
                    </div>
                </div>
            </div>
            
            <!-- Fechas -->
            <div class="bg-green-50 rounded-lg p-4 mb-4">
                <h4 class="text-lg font-semibold text-gray-900 mb-3">Fechas</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Fecha de Registro</label>
                        <p id="detail-created-at" class="text-sm text-gray-900 font-medium"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Última Actualización</label>
                        <p id="detail-updated-at" class="text-sm text-gray-900 font-medium"></p>
                    </div>
                </div>
            </div>
            
            <!-- Estado de Emparejamiento -->
            <div id="friendship-status" class="bg-purple-50 rounded-lg p-4 mb-4">
                <h4 class="text-lg font-semibold text-gray-900 mb-3">Estado de Emparejamiento</h4>
                <div id="friendship-info">
                    <p class="text-sm text-gray-600">Cargando información de emparejamiento...</p>
                </div>
            </div>
        </div>
        
        <div class="flex justify-end mt-6">
            <button type="button" onclick="closeViewDetailsModal()" 
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                Cerrar
            </button>
        </div>
    </div>
</div>