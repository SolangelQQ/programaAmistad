<x-guest-layout>
    <div class="flex items-center justify-center min-h-screen bg-gray-50">
        <div class="flex overflow-hidden rounded-lg shadow-lg w-full max-w-3xl bg-white">

            <div class="hidden md:flex md:w-1/2 p-8 text-white flex-col items-center justify-center bg-indigo-600">
                <div class="flex justify-center items-center mb-6">
                    <img src="{{ asset('logo.jpeg') }}" alt="Best Buddies Bolivia Logo" class="border-10 border-white w-32 h-32 object-contain">
                </div>
                <h2 class="text-2xl font-bold text-center">Bienvenido</h2>
                <h3 class="text-xl font-semibold text-center mt-2">Best Buddies Bolivia</h3>
                <p class="text-center mt-2">Programa Amistad</p>
            </div>
            
            <!-- derecho-->
            <div class="w-full md:w-1/2 p-8">
                <div class="max-w-md mx-auto">
                    <h2 class="text-2xl font-bold mb-2 text-gray-800">Registro de Usuario</h2>
                    <p class="text-gray-600 mb-6">Ingresa tus datos para crear una cuenta</p>
                    
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
                    
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2" for="name">Nombre Completo*</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Ej: Juan Pérez" required autofocus
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2" for="email">Correo electrónico*</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="tucorreo@ejemplo.com" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2" for="city">Ciudad*</label>
                            <select id="city" name="city" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="" disabled selected>Seleccione su ciudad</option>
                                <option value="La Paz" {{ old('city') == 'La Paz' ? 'selected' : '' }}>La Paz</option>
                                <option value="Cochabamba" {{ old('city') == 'Cochabamba' ? 'selected' : '' }}>Cochabamba</option>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2" for="role_id">Rol en la Organización*</label>
                            <select id="role_id" name="role_id" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="" disabled selected>Seleccione un rol</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <x-password-field 
                            id="password" 
                            name="password" 
                            label="Contraseña" 
                            required="true"
                            helpText="Al menos 8 caracteres, incluyendo mayúsculas, minúsculas, números y símbolos."
                        />
                        
                        <!-- Confirm Password field -->
                        <x-password-field 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            label="Confirmar Contraseña" 
                            required="true"
                            autocomplete="new-password"
                        />

                        <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 mb-4 font-medium transition duration-150 ease-in-out">
                            Registrar Usuario
                        </button>
                    </form>

                    <div class="relative my-6 pb-3">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                    </div>

                    <div class="text-center">
                        <p class="text-gray-600">¿Ya tienes cuenta? <a href="{{ route('login') }}" class="text-indigo-600 hover:underline font-medium">Inicia sesión</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePasswordVisibility(inputId, eyeIconId, eyeOffIconId) {
            const passwordInput = document.getElementById(inputId);
            const eyeIcon = document.getElementById(eyeIconId);
            const eyeOffIcon = document.getElementById(eyeOffIconId);
            
            if (passwordInput && eyeIcon && eyeOffIcon) {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                eyeIcon.classList.toggle('hidden');
                eyeOffIcon.classList.toggle('hidden');
            }
        }
    </script>
</x-guest-layout>