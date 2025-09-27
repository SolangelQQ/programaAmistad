<?php
// public/debug.php - Para diagnosticar errores específicos

echo "<h2>🔍 Debug Info Laravel</h2>";

try {
    // Cargar Laravel
    require_once __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    
    echo "✅ Laravel cargado correctamente<br>";
    
    // Test de base de datos
    try {
        $pdo = new PDO(
            "pgsql:host=" . getenv('DB_HOST') . ";port=5432;dbname=" . getenv('DB_DATABASE'),
            getenv('DB_USERNAME'),
            getenv('DB_PASSWORD')
        );
        echo "✅ Conexión PostgreSQL exitosa<br>";
    } catch (Exception $e) {
        echo "❌ Error PostgreSQL: " . $e->getMessage() . "<br>";
    }
    
    // Test de directorios
    echo "📁 Storage writeable: " . (is_writable(__DIR__.'/../storage') ? "✅" : "❌") . "<br>";
    echo "📁 Sessions dir exists: " . (is_dir(__DIR__.'/../storage/framework/sessions') ? "✅" : "❌") . "<br>";
    echo "📁 Cache dir exists: " . (is_dir(__DIR__.'/../storage/framework/cache') ? "✅" : "❌") . "<br>";
    
    // Variables importantes
    echo "<h3>🔧 Variables de entorno:</h3>";
    echo "APP_KEY: " . (getenv('APP_KEY') ? "✅ Configurada" : "❌ Faltante") . "<br>";
    echo "SESSION_DRIVER: " . getenv('SESSION_DRIVER') . "<br>";
    echo "CACHE_STORE: " . getenv('CACHE_STORE') . "<br>";
    echo "DB_CONNECTION: " . getenv('DB_CONNECTION') . "<br>";
    
} catch (Exception $e) {
    echo "❌ Error general: " . $e->getMessage() . "<br>";
    echo "Stack trace: <pre>" . $e->getTraceAsString() . "</pre>";
}
?>