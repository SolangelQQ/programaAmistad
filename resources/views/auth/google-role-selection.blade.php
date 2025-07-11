<x-guest-layout>
    <div class="flex items-center justify-center min-h-screen bg-gray-50">
        <div class="flex overflow-hidden rounded-lg shadow-lg w-full max-w-3xl bg-white">
            <!-- Left panel with logo and welcome text - Hidden on mobile -->
            <div class="hidden md:flex md:w-1/2 p-8 text-white flex-col items-center justify-center bg-indigo-600">
                <div class="flex justify-center items-center mb-6">
                    <img src="{{ asset('logo.jpeg') }}" alt="Best Buddies Bolivia Logo" class="border-10 border-white w-32 h-32 object-contain">
                </div>
                <h2 class="text-2xl font-bold text-center">Bienvenido</h2>
                <h3 class="text-xl font-semibold text-center mt-2">Best Buddies Bolivia</h3>
                <p class="text-center mt-2">Programa Amistad</p>
                
                <!-- User info from Google -->
                <div class="mt-8 text-center">
                    @if(isset($googleData['avatar']))
                        <img src="{{ $googleData['avatar'] }}" alt="Avatar" class="w-16 h-16 rounded-full mx-auto mb-3 border-2 border-white">
                    @endif
                    <p class="text-sm opacity-90">{{ $googleData['name'] }}</p>
                    <p class="text-xs opacity-75">{{ $googleData['email'] }}</p>
                </div>
            </div>
            
            <!-- Right panel with role selection form -->
            <div class="w-full md:w-1/2 p-8">
                <div class="max-w-md mx-auto">
                    <h2 class="text-2xl font-bold mb-2 text-gray-800">Seleccionar Rol</h2>
                    <p class="text-gray-600 mb-6">Tu cuenta ha sido creada exitosamente. Solo necesitas seleccionar tu rol en la organización.</p>
                    
                    @if(session('success'))
                        <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

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
                    
                    <!-- Account info summary -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Información de la cuenta:</h3>
                        <div class="space-y-1 text-sm text-gray-600">
                            <p><span class="font-medium">Nombre:</span> {{ $googleData['name'] }}</p>
                            <p><span class="font-medium">Email:</span> {{ $googleData['email'] }}</p>
                            <p><span class="font-medium">Método:</span> Google OAuth</p>
                            <p class="text-green-600"><span class="font-medium">Estado:</span> ✓ Email verificado</p>
                        </div>
                    </div>
                    
                    <form method="POST" action="{{ route('google.complete-registration') }}">
                        @csrf
                        
                        <!-- City Selection -->
                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2 font-medium" for="city">Ciudad*</label>
                            <select id="city" name="city" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="" disabled selected>Seleccione su ciudad</option>
                                <option value="La Paz" {{ old('city') == 'La Paz' ? 'selected' : '' }}>La Paz</option>
                                <option value="Cochabamba" {{ old('city') == 'Cochabamba' ? 'selected' : '' }}>Cochabamba</option>
                            </select>
                        </div>

                        <!-- Role Selection -->
                        <div class="mb-6">
                            <label class="block text-gray-700 mb-3 font-medium" for="role_id">Selecciona tu rol en la organización*</label>
                            <div class="space-y-3">
                                @foreach($roles as $role)
                                    <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                        <input type="radio" id="role_{{ $role->id }}" name="role_id" value="{{ $role->id }}" 
                                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300" 
                                               {{ old('role_id') == $role->id ? 'checked' : '' }} required>
                                        <div class="ml-3">
                                            <span class="block text-sm font-medium text-gray-900">{{ $role->name }}</span>
                                            @if($role->description)
                                                <span class="block text-xs text-gray-500">{{ $role->description }}</span>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Password info -->
                        <div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-400">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">
                                        <strong>Contraseña generada automáticamente:</strong><br>
                                        Se ha generado una contraseña segura para tu cuenta. Podrás cambiarla desde tu perfil después de iniciar sesión.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Submit button -->
                        <button type="submit" class="w-full bg-indigo-600 text-white py-3 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 mb-4 font-medium transition duration-150 ease-in-out">
                            Completar Registro
                        </button>
                    </form>
                    
                    <!-- Additional options -->
                    <div class="text-center space-y-2">
                        <a href="{{ route('google.show-password') }}" class="text-indigo-600 hover:underline text-sm">
                            Ver contraseña generada
                        </a>
                        <p class="text-gray-500 text-sm">
                            ¿Problemas? <a href="{{ route('login') }}" class="text-indigo-600 hover:underline">Volver al inicio de sesión</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>