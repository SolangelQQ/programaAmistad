<div id="edit-buddy-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg p-6 max-w-2xl w-full m-4 max-h-screen overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Editar Persona</h2>
            <button type="button" onclick="closeEditBuddyModal()" class="text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <form id="edit-buddy-form" method="POST" action="">
            @csrf
            @method('PUT')
            
            <!-- Información Personal -->
            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                <h4 class="text-lg font-semibold text-gray-900 mb-3">Información Personal</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="edit-first-name" class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                        <input type="text" id="edit-first-name" name="first_name" required 
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="edit-last-name" class="block text-sm font-medium text-gray-700 mb-1">Apellido</label>
                        <input type="text" id="edit-last-name" name="last_name" required 
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="edit-ci" class="block text-sm font-medium text-gray-700 mb-1">Cédula de Identidad</label>
                        <input type="text" id="edit-ci" name="ci" required 
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="edit-age" class="block text-sm font-medium text-gray-700 mb-1">Edad</label>
                        <input type="number" id="edit-age" name="age" required min="1" max="120"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="edit-phone" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                        <input type="tel" id="edit-phone" name="phone"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="edit-email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" id="edit-email" name="email"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                </div>
            </div>
            
            <!-- Tipo de Persona -->
            <div class="bg-blue-50 rounded-lg p-4 mb-4">
                <h4 class="text-lg font-semibold text-gray-900 mb-3">Tipo de Persona</h4>
                <div class="mb-4">
                    <label for="edit-type" class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                    <select id="edit-type" name="type" required onchange="toggleEditTypeFields()"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="buddy">Buddy</option>
                        <option value="peer_buddy">PeerBuddy</option>
                    </select>
                </div>
                
                <!-- Campos específicos para Buddy -->
                <div id="edit-buddy-fields" class="grid grid-cols-1 gap-4">
                    <div>
                        <label for="edit-disability" class="block text-sm font-medium text-gray-700 mb-1">Discapacidad</label>
                        <input type="text" id="edit-disability" name="disability"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                </div>
                
                <!-- Campos específicos para PeerBuddy -->
                <div id="edit-peer-buddy-fields" class="grid grid-cols-1 gap-4 hidden">
                    <div>
                        <label for="edit-experience" class="block text-sm font-medium text-gray-700 mb-1">Experiencia</label>
                        <textarea id="edit-experience" name="experience" rows="3"
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeEditBuddyModal()" 
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    Cancelar
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>