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