<?php
// 2. CREAR: resources/views/auth/reset-password.blade.php  
?>
<x-guest-layout>
    <div class="flex items-center justify-center min-h-screen bg-gray-50">
        <div class="flex overflow-hidden rounded-lg shadow-lg w-full max-w-3xl bg-white">
            <!-- Left panel - Logo -->
            <div class="hidden md:flex md:w-1/2 p-8 text-white flex-col items-center justify-center bg-indigo-600">
                <div class="flex justify-center items-center mb-6">
                    <img src="{{ asset('logo.jpeg') }}" alt="Best Buddies Bolivia Logo" class="border-4 border-white w-32 h-32 object-contain rounded-lg">
                </div>
                <h2 class="text-2xl font-bold text-center">Restablecer</h2>
                <h3 class="text-xl font-semibold text-center mt-2">Contraseña</h3>
                <p class="text-center mt-2">Programa Amistad</p>
            </div>
            
            <!-- Right panel - Form -->
            <div class="w-full md:w-1/2 p-8">
                <div class="max-w-md mx-auto">
                    <h2 class="text-2xl font-bold mb-2 text-gray-800">Nueva Contraseña</h2>
                    <p class="text-gray-600 mb-6">Ingresa tu nueva contraseña segura</p>
                    
                    @if($errors->any())
                        <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-700">
                                        @foreach($errors->all() as $error)
                                            {{ $error }}<br>
                                        @endforeach
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.store') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">

                        <!-- Email field -->
                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2" for="email">Correo electrónico*</label>
                            <input type="email" id="email" name="email" 
                                   value="{{ old('email', $request->email) }}" 
                                   required readonly
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-600 cursor-not-allowed">
                        </div>

                        <!-- New Password field -->
                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2" for="password">Nueva Contraseña*</label>
                            <x-password-field 
                                id="password" 
                                name="password" 
                                label="" 
                                required
                                autocomplete="new-password"
                                helpText="Mínimo 8 caracteres con mayúsculas, minúsculas, números y símbolos."
                            />
                        </div>

                        <!-- Confirm Password field -->
                        <div class="mb-6">
                            <label class="block text-gray-700 mb-2" for="password_confirmation">Confirmar Contraseña*</label>
                            <x-password-field 
                                id="password_confirmation" 
                                name="password_confirmation" 
                                label="" 
                                required
                                autocomplete="new-password"
                            />
                        </div>

                        <!-- Submit button -->
                        <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 mb-4 font-medium transition duration-150 ease-in-out">
                            Actualizar Contraseña
                        </button>
                    </form>
                    
                    <!-- Back to login -->
                    <div class="text-center">
                        <a href="{{ route('login') }}" class="text-indigo-600 hover:underline font-medium">
                            ← Volver al inicio de sesión
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>