<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Best Buddies Bolivia</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full space-y-8 p-8">
        <div class="text-center">
            <!-- Título principal -->
            <h1 class="text-4xl font-bold text-gray-900 mb-2">BOLIVIA</h1>
            <p class="text-lg text-gray-600 mb-8">Best Buddies Bolivia</p>
        </div>

        <!-- Formulario de login -->
        <div class="bg-white p-8 rounded-lg shadow-lg">
            <form class="space-y-6">
                <!-- Campo Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Correo electrónico*
                    </label>
                    <input 
                        type="email" 
                        id="email"
                        name="email"
                        value="tucorreo@ejemplo.com"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="tucorreo@ejemplo.com"
                    >
                </div>

                <!-- Campo Contraseña -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Contraseña*
                    </label>
                    <input 
                        type="password" 
                        id="password"
                        name="password"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="••••••••"
                    >
                </div>

                <!-- Mensaje de ayuda -->
                <div class="text-sm text-gray-500">
                    Al menos 8 caracteres, incluyendo mayúsculas, minúsculas, números y símbolos.
                </div>

                <!-- Botón de submit -->
                <button 
                    type="submit" 
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200"
                >
                    Iniciar Sesión
                </button>

                <!-- Enlaces -->
                <div class="text-center space-y-2">
                    <div>
                        <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            Recuperar contraseña
                        </a>
                    </div>
                    <div class="text-sm text-gray-600">
                        ¿No tienes cuenta? 
                        <a href="#" class="text-blue-600 hover:text-blue-800 font-medium">
                            Recordarme
                        </a>
                    </div>
                </div>
            </form>

            <!-- Tu clase personalizada de prueba -->
            <div class="test-class rounded-md mt-6">
                Esta es tu clase personalizada con fondo rojo
            </div>
        </div>
    </div>
</body>
</html>