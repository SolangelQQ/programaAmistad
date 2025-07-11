@extends('layouts.app')
@section('content')
<div class="mx-auto" style="max-width: 75%">
    <div class="flex justify-between items-center py-6">
        <h1 class="text-2xl font-bold">Detalles del Usuario</h1>
        <a href="{{ route('roles.index') }}" class="px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300 text-gray-700">Volver</a>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h2 class="text-lg font-semibold mb-4">Información Personal</h2>
                <div class="mb-4">
                    <p class="text-md">Nombre</p>
                    <p class="text-base text-gray-500">{{ $user->name }}</p>
                </div>
                <div class="mb-4">
                    <p class="text-md">Correo electrónico</p>
                    <p class="text-gray-500">{{ $user->email }}</p>
                </div>
                <div class="mb-4">
                    <p class="text-md">Fecha de registro</p>
                    <p class="text-gray-500">{{ $user->created_at->format('d/m/Y') }}</p>
                </div>
                <div class="mb-4">
                    <p class="text-md">Ciudad</p>
                    <p class="text-gray-500">{{ $user->city }}</p>
                </div>
            </div>
            
            <div>
                <h2 class="text-lg font-semibold mb-4">Información del Rol</h2>
                <div class="mb-4">
                    <p class="text-md">Rol asignado</p>
                    <p class="text-gray-500">{{ $user->role ? $user->role->name : 'Sin rol asignado' }}</p>
                </div>
                <div>
                    <p class="text-md">Descripción del rol</p>
                    <p class="text-gray-500">{{ $roleDescription }}</p>
                </div>
            </div>
        </div>
        
        @if(auth()->user()->role && auth()->user()->role->name === 'Encargado del Programa Amistad')
        <div class="mt-8 p-4 border-t border-gray-200 flex justify-end gap-4">

            <a href="{{ route('roles.edit', $user->id) }}" 
            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Editar usuario
            </a>
    
            <div>
                <form action="{{ route('roles.destroy', $user->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario?')"
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Eliminar usuario
                    </button>
                </form>
            </div>
        </div>

        @endif
    </div>
</div>
@endsection