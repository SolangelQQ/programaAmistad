<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Test Tailwind</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="min-h-screen bg-blue-500 flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-lg">
            <h1 class="text-4xl font-bold text-blue-600 mb-4">Test Tailwind CSS</h1>
            <p class="text-gray-600 mb-4">Si ves este texto con estilos, Tailwind está funcionando.</p>
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Botón de prueba
            </button>
        </div>
    </div>
</body>
</html>