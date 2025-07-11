@extends('layouts.app')

@section('content')
<div class="mx-auto" style="max-width: 95%">
    <div class="flex justify-between items-center py-6">
        <h1 class="text-2xl font-bold">Roles</h1>
    </div>
    
    
    
    <div class="bg-white rounded-lg shadow overflow-hidden w-full">
        <div class="overflow-y-auto max-h-[70vh]">
            <table class="w-full rounded-lg h-full">
                <thead class="sticky top-0 z-10">
                    <tr>
                        <th class="px-12 py-6 bg-gray-50 text-left text-md font-semibold text-gray-800">Nombre</th>
                        <th class="px-12 py-6 bg-gray-50 text-left text-md font-semibold text-gray-800">Email</th>
                        <th class="px-12 py-6 bg-gray-50 text-left text-md font-semibold text-gray-800">Rol</th>
                        <th class="px-12 py-6 bg-gray-50 text-left text-md font-semibold text-gray-800">Ciudad</th>
                        <th class="px-12 py-6 bg-gray-50 text-left text-md font-semibold text-gray-800">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                @foreach ($users as $user)
                <tr class="border-b border-gray-300">
                    <td class="p-8 whitespace-nowrap text-sm">{{ $user->name }}</td>
                    <td class="p-8 whitespace-nowrap text-sm">{{ $user->email }}</td>
                    <td class="p-8 whitespace-nowrap text-sm">{{ $user->role ? $user->role->name : 'Sin rol' }}</td>
                    <td class="p-8 whitespace-nowrap text-sm">{{ $user->city }}</td>
                    <td class="p-8 whitespace-nowrap text-sm relative">
                        @if(auth()->user()->role && auth()->user()->role->name === 'Encargado del Programa Amistad')
                        <div class="dropdown-container">
                            <button onclick="toggleDropdown({{ $user->id }})" class="text-gray-400 hover:text-gray-600" type="button">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                </svg>
                            </button>
                            <div id="dropdown-menu-{{ $user->id }}" class="dropdown-menu hidden">
                                <div class="py-1 text-left text-sm text-gray-700 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5">
                                    <a href="{{ route('roles.show', $user->id) }}" class="block px-4 py-2 hover:bg-gray-100">Ver detalles</a>
                                    <a href="{{ route('roles.edit', $user->id) }}" class="block px-4 py-2 hover:bg-gray-100">Editar</a>
                                    @if(auth()->user()->id !== $user->id)
                                    <form action="{{ route('roles.destroy', $user->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                onclick="return confirm('¿Estás seguro de que deseas eliminar al usuario {{ $user->name }}?')"
                                                class="w-full text-left px-4 py-2 hover:bg-gray-100 text-red-600">
                                            Eliminar
                                        </button>
                                    </form>
                                    @else
                                    <span class="block px-4 py-2 text-gray-400 cursor-not-allowed">No puedes eliminarte</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @else
                        <span class="text-gray-400">Acciones restringidas</span>
                        @endif
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Funciones para el dropdown menu
function toggleDropdown(userId) {
    const menu = document.getElementById(`dropdown-menu-${userId}`);
    
    // Cerrar todos los otros menús
    document.querySelectorAll('.dropdown-menu').forEach(element => {
        if (element.id !== `dropdown-menu-${userId}`) {
            element.classList.add('hidden');
        }
    });
    
    // Toggle el menú actual
    menu.classList.toggle('hidden');
    
    if (!menu.classList.contains('hidden')) {
        // Posicionar el menú
        const button = event.currentTarget;
        const buttonRect = button.getBoundingClientRect();
        
        menu.style.position = 'fixed';
        menu.style.top = `${buttonRect.bottom + 5}px`;
        menu.style.left = `${buttonRect.left - 100}px`;
        menu.style.zIndex = '9999';
        menu.style.minWidth = '150px';
    }
}

// Cierra el dropdown al hacer clic fuera
document.addEventListener('click', function(event) {
    if (!event.target.closest('.dropdown-container')) {
        document.querySelectorAll('.dropdown-menu').forEach(element => {
            element.classList.add('hidden');
        });
    }
});

// Prevenir que el menú se cierre al hacer clic dentro de él
document.querySelectorAll('.dropdown-menu').forEach(menu => {
    menu.addEventListener('click', function(event) {
        event.stopPropagation();
    });
});
</script>
@endpush

<style>
.dropdown-menu {
    position: fixed;
    z-index: 9999;
    min-width: 150px;
}
</style>
@endsection