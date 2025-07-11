@extends('layouts.app')

@section('title', 'Perfil de Usuario')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center pb-6">
        <h1 class="text-2xl font-bold text-gray-800">Mi Perfil</h1>
        <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300 text-gray-700 transition duration-200">
            Volver al Dashboard
        </a>
    </div>
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Cabecera del perfil -->
        <div class="bg-blue-50 p-6">
            <div class="flex flex-col md:flex-row items-center">
                <div class="flex-shrink-0 mb-4 md:mb-0">
                    <img class="h-24 w-24 rounded-full border-4 object-cover" 
                        src="https://i.pravatar.cc/300" alt="{{ $user->name }}">
                </div>
                <div class="ml-0 md:ml-6 text-center md:text-left">
                    <h1 class="text-2xl font-bold text-black">{{ $user->name }}</h1>
                    <p class="text-blue-100">{{ $user->email }}</p>
                    <p class="text-blue-100">Miembro desde {{ $user->created_at->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>

        <div x-data="{ activeTab: 'info' }">
            <!-- Tabs -->
            <!-- <div class="bg-gray-50 px-6 border-b">
                <nav class="flex space-x-1">
                    <button 
                        @click="activeTab = 'info'" 
                        :class="{
                            'rounded-t-lg text-black p-2': activeTab === 'info', 
                            'text-gray-500 hover:text-gray-700 hover:bg-gray-100': activeTab !== 'info'
                        }"
                        class="py-3 px-4 font-medium text-sm focus:outline-none transition-colors duration-200 relative">
                        Información Personal
                        <div x-show="activeTab === 'info'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-purple-500"></div>
                    </button>
                </nav>
            </div> -->

            <!-- Panel de información personal -->
            <div x-show="activeTab === 'info'" class="p-6">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
                            <div class="mt-1">
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                                    class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Correo electrónico</label>
                            <div class="mt-1">
                                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                                    class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Botón de guardar -->
                        <div>
                            <button type="submit" 
                                style="background: linear-gradient(to right, #3B82F6, #8B5CF6)"
                                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200"
                                onmouseover="this.style.background='linear-gradient(to right, #2563EB, #7C3AED)'"
                                onmouseout="this.style.background='linear-gradient(to right, #3B82F6, #8B5CF6)'">
                                Guardar cambios
                            </button>
                        </div>
                    </div>
                </form>
            </div>            
        </div>
    </div>
</div>
@endsection