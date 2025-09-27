<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Debug Nueva BD</h1>";

try {
    echo "1. Probando nueva base de datos...<br>";
    
    $dsn = "pgsql:host=dpg-d3bu14vdiees738uqcv0-a.oregon-postgres.render.com;port=5432;dbname=programa_amistad_ib50;sslmode=require";
    $pdo = new PDO($dsn, 'programa_amistad_user', 'QF3h1vrWO463L9ZRMu8nPzqfgvPp1Rtk');
    echo "✅ Conexión exitosa<br>";
    
    // Probar tabla users
    $stmt = $pdo->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_name = 'users'");
    $exists = $stmt->fetchColumn() > 0;
    echo $exists ? "✅ Tabla users existe<br>" : "❌ Tabla users NO existe<br>";
    
    if ($exists) {
        $stmt = $pdo->query("SELECT COUNT(*) FROM users");
        $count = $stmt->fetchColumn();
        echo "👥 Total usuarios: $count<br>";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "<br>";
}

echo "<h2>Variables de entorno:</h2>";
echo "DB_HOST: " . ($_ENV['DB_HOST'] ?? 'NO DEFINIDO') . "<br>";
echo "DB_DATABASE: " . ($_ENV['DB_DATABASE'] ?? 'NO DEFINIDO') . "<br>";
echo "DB_USERNAME: " . ($_ENV['DB_USERNAME'] ?? 'NO DEFINIDO') . "<br>";
echo "FRESH_INSTALL: " . ($_ENV['FRESH_INSTALL'] ?? 'NO DEFINIDO') . "<br>";
?>