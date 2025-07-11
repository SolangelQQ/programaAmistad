@extends('layouts.app')

@section('title', 'Configuración')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center pb-6">
        <h1 class="text-2xl font-bold text-gray-800 pb-4">Configuración</h1>
        <a href="{{ route('dashboard') }}" class="p-4 bg-gray-200 rounded-md hover:bg-gray-300 text-gray-700 transition duration-200">
            Volver al Dashboard
        </a>
    </div>
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Cabecera -->
        <div class="p-4" style="background: linear-gradient(to right, #93C5FD, #C4B5FD)">
            <p class="text-black">Personaliza tu experiencia en la plataforma</p>
        </div>

        <div x-data="{ activeTab: 'password' }">
            <!-- Tabs -->
            <!-- <div class="bg-gray-50 px-6 border-b">
                <nav class="flex space-x-1">
                    <button 
                        @click="activeTab = 'password'"
                        :class="{
                            'bg-purple-500 border-t-2 border-l-2 border-r-2 border-purple-400 rounded-t-lg text-white p-2': activeTab === 'password', 
                            'text-gray-500 hover:text-gray-700 hover:bg-gray-100': activeTab !== 'password'
                        }"
                        class="py-3 px-4 font-medium text-sm focus:outline-none transition-colors duration-200 relative">
                        Cambiar Contraseña
                        <div x-show="activeTab === 'password'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-purple-500"></div>
                    </button>
                    <button 
                        @click="activeTab = 'notifications'" 
                        :class="{
                            'bg-purple-500 border-t-2 border-l-2 border-r-2 border-purple-400 rounded-t-lg text-white p-2': activeTab === 'notifications', 
                            'text-gray-500 hover:text-gray-700 hover:bg-gray-100': activeTab !== 'notifications'
                        }"
                        class="py-3 px-4 font-medium text-sm focus:outline-none transition-colors duration-200 relative">
                        Notificaciones
                        <div x-show="activeTab === 'notifications'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-purple-500"></div>
                    </button>
                </nav>
            </div> -->

            <form action="{{ route('settings.update') }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Panel de cambiar contraseña -->
                <div x-show="activeTab === 'password'" class="p-6">
                    <form action="{{ route('profile.update-password') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="space-y-6">
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-gray-700">Contraseña actual</label>
                                <div class="mt-1">
                                    <input type="password" name="current_password" id="current_password"
                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                                @error('current_password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700">Nueva contraseña</label>
                                <div class="mt-1">
                                    <input type="password" name="password" id="password"
                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar contraseña</label>
                                <div class="mt-1">
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Panel de notificaciones -->
                <!-- <div x-show="activeTab === 'notifications'" class="p-6">
                    <div class="space-y-6">
                        <h3 class="text-lg font-medium text-gray-900">Preferencias de notificación</h3>
                        <p class="mt-1 text-sm text-gray-500">Decide cómo quieres recibir notificaciones.</p>
                        
                        <div class="mt-4 space-y-4">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="notifications_push" name="notifications_push" type="checkbox" value="1"
                                        class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                                        {{ $settings['notifications_push'] ? 'checked' : '' }}>
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="notifications_push" class="font-medium text-gray-700">Notificaciones</label>
                                    <p class="text-gray-500">Recibe notificaciones.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->

                <!-- Botón de guardar (visible en ambas pestañas) -->
                <div class="px-6 py-4 bg-gray-50 border-t">
                    <button type="submit" 
                        class="w-full sm:w-auto flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" style="background: linear-gradient(to right, #3B82F6, #8B5CF6)">
                        Guardar configuración
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection