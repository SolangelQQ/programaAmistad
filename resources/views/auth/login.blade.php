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
            
            <!-- derecho -->
            <div class="w-full md:w-1/2 p-8">
                <div class="max-w-md mx-auto">
                    <h2 class="text-2xl font-bold mb-2 text-gray-800">Iniciar sesión</h2>
                    <p class="text-gray-600 mb-6">Ingresa tus datos para continuar</p>
                    
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
                    
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        
                        <!-- Email field -->
                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2" for="email">Correo electrónico*</label>
                            <input type="email" id="email" name="email" placeholder="tucorreo@ejemplo.com" required autofocus
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        
                        <!-- Password field using component -->
                        <div class="mb-4">
                            <div class="flex justify-between mb-2">
                                <label class="block text-gray-700" for="password">Contraseña*</label>
                            </div>
                            
                            <x-password-field 
                                id="password" 
                                name="password" 
                                label="" 
                               
                                autocomplete="current-password"
                                helpText="Al menos 8 caracteres, incluyendo mayúsculas, minúsculas, números y símbolos."
                            />

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-indigo-600 text-sm hover:underline">Recuperar contraseña</a>
                            @endif
                        </div>
                        
                        <!-- Remember me checkbox -->
                        <div class="mb-6">
                            <label class="flex items-center cursor-pointer">
                                <input id="remember_me" type="checkbox" name="remember" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded cursor-pointer">
                                <span class="ml-2 text-gray-700">Recordarme</span>
                            </label>
                        </div>
                        
                        <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 mb-4 font-medium transition duration-150 ease-in-out">
                            Iniciar Sesión
                        </button>
                    </form>
                    
                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-white text-gray-500">O continuar con</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <a href="{{ route('login.google.redirect') }}" class="w-full flex items-center justify-center bg-white border border-gray-300 rounded-md py-2 px-4 text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                            <svg class="h-5 w-5 mr-2" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                            Continuar con Google
                        </a>
                    </div>

                    <div class="text-center">
                        <p class="text-gray-600">¿No tienes cuenta? <a href="{{ route('register') }}" class="text-indigo-600 hover:underline font-medium">Regístrate</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
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