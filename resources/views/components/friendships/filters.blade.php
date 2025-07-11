<form action="{{ route('friendships.filter') }}" method="GET" class="mb-6">
    <div class="overflow-x-auto flex-col grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
            <select id="status" name="status" class="block w-full h-10 pl-3 pr-10 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                <option value="Todos">Todos</option>
                <option value="Emparejado" {{ request('status') == 'Emparejado' ? 'selected' : '' }}>Emparejado</option>
                <option value="Inactivo" {{ request('status') == 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                <option value="Finalizado" {{ request('status') == 'Finalizado' ? 'selected' : '' }}>Finalizado</option>
            </select>
        </div>
        
        <div>
            <label for="buddy_search" class="block text-sm font-medium text-gray-700 mb-1">Buddy</label>
            <input type="text" id="buddy_search" name="buddy_search" value="{{ request('buddy_search') }}" 
                   class="block w-full h-10 px-3 text-base border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm sm:text-sm rounded-md" 
                   placeholder="Buscar por nombre...">
        </div>
        
        <div>
            <label for="peer_buddy_search" class="block text-sm font-medium text-gray-700 mb-1">PeerBuddy</label>
            <input type="text" id="peer_buddy_search" name="peer_buddy_search" value="{{ request('peer_buddy_search') }}" 
                   class="block w-full h-10 px-3 text-base border-gray-300 focus:ring-blue-500 focus:border-blue-500 shadow-sm sm:text-sm rounded-md" 
                   placeholder="Buscar por nombre...">
        </div>
        
        <div class="flex flex-col">
            <div class="h-6 "></div> <!-- Spacer para alinear con los labels -->
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 h-10 inline-flex items-center justify-center px-4 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Filtrar
                </button>
                <a href="{{ route('friendships.index') }}" class=" flex-1 h-10 inline-flex items-center justify-center px-4 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Limpiar
                </a>
            </div>
        </div>
    </div>
</form>