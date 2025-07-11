@extends('layouts.app')
@section('content')
<div class="mx-auto" style="max-width: 75%">
    <div class="flex justify-between items-center py-6">
        <h1 class="text-2xl font-bold">Editar Usuario</h1>
        <a href="{{ route('roles.index') }}" class="px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300 text-gray-700">Volver</a>
    </div>
    
    @include('components.notification')
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('roles.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" 
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Correo electrónico</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" 
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="city" class="block text-sm font-medium text-gray-700">Ciudad</label>
                <select name="city" id="city" 
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="">Seleccionar ciudad...</option>
                    <option value="La Paz" {{ (old('city', $user->city) == 'La Paz') ? 'selected' : '' }}>La Paz</option>
                    <option value="Cochabamba" {{ (old('city', $user->city) == 'Cochabamba') ? 'selected' : '' }}>Cochabamba</option>
                </select>
                @error('city')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-6">
                <label for="role_id" class="block text-sm font-medium text-gray-700">Rol</label>
                <select name="role_id" id="role_id" 
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="">Seleccionar rol...</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" 
                                {{ (old('role_id', $user->role_id) == $role->id) ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
                @error('role_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endsection