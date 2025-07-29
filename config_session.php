<?php

// Cargar configuración centralizada
$config = require __DIR__.'/config.php';

require __DIR__.'/../'.$config['carpeta_laravel'].'/vendor/autoload.php';
$app = require_once __DIR__.'/../'.$config['carpeta_laravel'].'/bootstrap/app.php';

echo "<h2>Configuración del Sistema de Sesiones</h2>";

// 1. Configurar el driver de sesión en el .env
file_put_contents(__DIR__.'/../'.$config['carpeta_laravel'].'/.env', preg_replace(
    '/SESSION_DRIVER=.*/',
    'SESSION_DRIVER=database',
    file_get_contents(__DIR__.'/../'.$config['carpeta_laravel'].'/.env')
));

// 2. Crear la tabla de sesiones si no existe
try {
    $output = new \Symfony\Component\Console\Output\BufferedOutput();
    $app->make('Illuminate\Contracts\Console\Kernel')->call('session:table', [], $output);
    echo "<p><strong>php artisan session:table:</strong> ✅ ".nl2br(htmlspecialchars($output->fetch()))."</p>";
    
    // 3. Ejecutar las migraciones
    $output = new \Symfony\Component\Console\Output\BufferedOutput();
    $app->make('Illuminate\Contracts\Console\Kernel')->call('migrate', ['--force' => true], $output);
    echo "<p><strong>php artisan migrate --force:</strong> ✅ ".nl2br(htmlspecialchars($output->fetch()))."</p>";
    
    // 4. Verificar la configuración
    echo "<h3>Verificación de Configuración</h3>";
    echo "SESSION_DRIVER: ".env('SESSION_DRIVER')."<br>";
    echo "SESSION_LIFETIME: ".env('SESSION_LIFETIME', 120)." minutos<br>";
    
    // 5. Verificar que la tabla de sesiones existe
    try {
        $tables = DB::select('SHOW TABLES');
        $sessionTableExists = false;
        foreach ($tables as $table) {
            if (isset($table->Tables_in_system) && 
                $table->Tables_in_system === 'sessions') {
                $sessionTableExists = true;
                break;
            }
        }
        echo $sessionTableExists ? "✅ Tabla de sesiones existe" : "❌ Tabla de sesiones no encontrada";
    } catch (Exception $e) {
        echo "<p style='color:red'>Error al verificar tablas: ".$e->getMessage()."</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color:red'><strong>Error:</strong> ".htmlspecialchars($e->getMessage())."</p>";
}

// 6. Configuración adicional recomendada
$sessionConfig = <<<'EOD'
<?php

return [
    'driver' => env('SESSION_DRIVER', 'database'),
    'lifetime' => env('SESSION_LIFETIME', 120),
    'expire_on_close' => false,
    'encrypt' => true,
    'files' => storage_path('framework/sessions'),
    'connection' => env('SESSION_CONNECTION', null),
    'table' => 'sessions',
    'store' => env('SESSION_STORE', null),
    'lottery' => [2, 100],
    'cookie' => env('SESSION_COOKIE', 'laravel_session'),
    'path' => '/',
    'domain' => env('SESSION_DOMAIN', null),
    'secure' => env('SESSION_SECURE_COOKIE', true),
    'http_only' => true,
    'same_site' => 'lax',
];
EOD;

file_put_contents(__DIR__.'/../'.$config['carpeta_laravel'].'/config/session.php', $sessionConfig);

echo "<h3 style='color:green'>✅ Configuración de sesión completada</h3>";
?>