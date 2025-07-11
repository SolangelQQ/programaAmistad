<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Test Tailwind Bolivia</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
            <!-- Header con colores de Bolivia -->
            <div class="text-center mb-6">
                <div class="flex justify-center mb-4">
                    <div class="w-8 h-8 bg-red-500 rounded-full mx-1"></div>
                    <div class="w-8 h-8 bg-yellow-500 rounded-full mx-1"></div>
                    <div class="w-8 h-8 bg-green-500 rounded-full mx-1"></div>
                </div>
                <h1 class="text-3xl font-bold text-gray-800">BOLIVIA</h1>
                <p class="text-gray-600">Best Buddies Bolivia</p>
            </div>

            <!-- Formulario de login -->
            <form class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Correo electrónico*
                    </label>
                    <input 
                        type="email" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="tucorreo@ejemplo.com"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Contraseña*
                    </label>
                    <input 
                        type="password" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="••••••••"
                    >
                </div>

                <div class="text-sm text-gray-500">
                    Al menos 8 caracteres, incluyendo mayúsculas, minúsculas, números y símbolos.
                </div>

                <button 
                    type="submit" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-200"
                >
                    Iniciar Sesión
                </button>

                <div class="text-center space-y-2">
                    <a href="#" class="text-blue-600 hover:text-blue-800 text-sm">
                        Recuperar contraseña
                    </a>
                    <div class="text-gray-500 text-sm">
                        ¿No tienes cuenta? 
                        <a href="#" class="text-blue-600 hover:text-blue-800">Recordarme</a>
                    </div>
                </div>
            </form>

            <!-- Test de tu clase personalizada -->
            <div class="mt-6 test-class rounded-md">
                Esta es tu clase personalizada con fondo rojo
            </div>
        </div>
    </div>
</body>
</html>