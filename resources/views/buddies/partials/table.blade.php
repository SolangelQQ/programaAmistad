@if($buddies && $buddies->count() > 0)
<div class="overflow-x-auto">
    <table class="w-full max-w-5xl rounded-lg h-full">
        <thead class="bg-gray-50 sticky top-0 z-10">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avatar</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CI</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($buddies as $buddy)
            <tr class="border-b border-gray-300 hover:bg-gray-50">
                <td class="p-2 whitespace-nowrap center">
                    <img class="h-10 w-10 rounded-full object-cover" src="https://i.pravatar.cc/300?u={{ $buddy->email ?? $buddy->id }}" alt="{{ $buddy->full_name }}">
                </td>
                <td class="p-2 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ $buddy->full_name }}</div>
                    <div class="text-sm text-gray-500">{{ $buddy->age }} años</div>
                </td>
                <td class="p-2 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                        {{ $buddy->type == 'buddy' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                        {{ $buddy->type == 'buddy' ? 'Buddy' : 'PeerBuddy' }}
                    </span>
                    @if($buddy->type == 'buddy')
                    <div class="text-xs text-gray-500 mt-1">{{ $buddy->disability }}</div>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $buddy->ci }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-2 justify-center">
                        <!-- Ver Detalles -->
                        <button onclick="viewBuddyDetails({{ $buddy->id }})" 
                                class="inline-flex items-center px-3 py-1 bg-blue-100 hover:bg-blue-200 text-blue-700 text-xs font-medium rounded-md transition-colors duration-150"
                                title="Ver detalles">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Ver
                        </button>
                        
                        <!-- Editar -->
                        <button onclick="editBuddy({{ $buddy->id }})" 
                                class="inline-flex items-center px-3 py-1 bg-yellow-100 hover:bg-yellow-200 text-yellow-700 text-xs font-medium rounded-md transition-colors duration-150"
                                title="Editar">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Editar
                        </button>
                        
                        <!-- Eliminar -->
                        <button onclick="confirmDeleteBuddy({{ $buddy->id }})" 
                                class="inline-flex items-center px-3 py-1 bg-red-100 hover:bg-red-200 text-red-700 text-xs font-medium rounded-md transition-colors duration-150"
                                title="Eliminar">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Eliminar
                        </button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $buddies->links() }}
</div>

@else
<!-- Mensaje cuando no hay buddies -->
<div class="bg-white rounded-lg shadow p-8 text-center">
    <div class="max-w-md mx-auto">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.196-2.12l-.174.174a6 6 0 01-8.485 0l-.174-.174A3 3 0 002 18v2h5m10 0v-2a3 3 0 00-3-3m3 3zm-3-3a3 3 0 00-3-3m3 3zm-3-3V9a3 3 0 013-3m-3 3a3 3 0 013-3m-3 3V9a3 3 0 013-3z" />
        </svg>
        <h3 class="mt-4 text-lg font-medium text-gray-900">No hay buddies registrados</h3>
        <p class="mt-2 text-sm text-gray-500">
            Aún no se han registrado buddies o peerbuddies en el sistema.
        </p>
        <div class="mt-6">
            <button type="button"
                    class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    onclick="window.location='{{ route('buddies.create') }}'">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Agregar primer buddy
            </button>
        </div>
    </div>
</div>
@endif