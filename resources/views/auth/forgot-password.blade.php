?>
<x-guest-layout>
    <div class="flex items-center justify-center min-h-screen bg-gray-50">
        <div class="flex overflow-hidden rounded-lg shadow-lg w-full max-w-3xl bg-white">
            <!-- Logo -->
            <div class="hidden md:flex md:w-1/2 p-8 text-white flex-col items-center justify-center bg-indigo-600">
                <div class="flex justify-center items-center mb-6">
                    <img src="{{ asset('logo.jpeg') }}" alt="Best Buddies Bolivia Logo" class="w-32 h-32 object-contain rounded-lg">
                </div>
                <h2 class="text-2xl font-bold text-center">Recuperar</h2>
                <h3 class="text-xl font-semibold text-center mt-2">Contraseña</h3>
                <p class="text-center mt-2">Programa Amistad</p>
            </div>
            
            <!-- derecha -->
            <div class="w-full md:w-1/2 p-8">
                <div class="max-w-md mx-auto">
                    <h2 class="text-2xl font-bold mb-2 text-gray-800">Recuperar contraseña</h2>
                    <p class="text-gray-600 mb-6">Te enviaremos un enlace para restablecer tu contraseña</p>
                    
                    @if(session('status'))
                        <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-green-700">
                                        ✅ {{ session('status') }}
                                        <br><small>Revisa tu bandeja de entrada y carpeta de spam</small>
                                    </p>
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

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf
                        
                        <!-- Email field -->
                        <div class="mb-6">
                            <label class="block text-gray-700 mb-2" for="email">Correo electrónico*</label>
                            <input type="email" id="email" name="email" 
                                   value="{{ old('email') }}"
                                   placeholder="tucorreo@ejemplo.com" 
                                   required autofocus
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        
                        <!-- Buttons -->
                        <div class="space-y-3">
                            <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 font-medium transition duration-150 ease-in-out">
                                📧 Enviar enlace de recuperación
                            </button>
                            
                            <a href="{{ route('login') }}" class="w-full block text-center bg-gray-100 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-200 transition duration-150 ease-in-out">
                                ← Volver a iniciar sesión
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>