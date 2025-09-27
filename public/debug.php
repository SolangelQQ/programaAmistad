<?php
// public/debug.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Debug Best Buddies Bolivia</h1>";

try {
    echo "1. PHP funcionando...<br>";
    
    // Test database
    echo "2. Probando base de datos...<br>";
    $dsn = "pgsql:host=dpg-d1ok3smr433s73cg0dlg-a.oregon-postgres.render.com;port=5432;dbname=programa_amistad;sslmode=require";
    $pdo = new PDO($dsn, 'programa_amistad_user', 'YOKEBKzPQ08RtZXiJHVtSq3z2HUeGZOz');
    echo "✅ Base de datos conectada<br>";
    
    // Test Laravel bootstrap
    echo "3. Probando Laravel...<br>";
    require_once '../vendor/autoload.php';
    
    $app = require_once '../bootstrap/app.php';
    echo "✅ Laravel bootstrap OK<br>";
    
    // Test routes
    echo "4. Verificando rutas...<br>";
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    echo "✅ Kernel cargado<br>";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "<br>";
    echo "📁 Archivo: " . $e->getFile() . "<br>";
    echo "📍 Línea: " . $e->getLine() . "<br>";
    echo "<details><summary>Stack trace</summary><pre>" . $e->getTraceAsString() . "</pre></details>";
}
?>