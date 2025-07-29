<?php

// Cargar configuración centralizada
$config = require __DIR__.'/config.php';

require __DIR__.'/../'.$config['carpeta_laravel'].'/vendor/autoload.php';
$app = require_once __DIR__.'/../'.$config['carpeta_laravel'].'/bootstrap/app.php';

echo "<h2>Configuración Inicial</h2>";
// 1. Configurar el AppServiceProvider para evitar el error de longitud

file_put_contents(__DIR__.'/../'.$config['carpeta_laravel'].'/app/Providers/AppServiceProvider.php', 

'<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Schema::defaultStringLength(191); // Soluciona el error de longitud
    }
}
');


// 2. Limpieza básica (ignorando errores de tablas faltantes)
$commands = [
    'config:clear',
    'view:clear',
    'route:clear',
    'key:generate --force',
];

foreach ($commands as $cmd) {
    try {
        $output = new \Symfony\Component\Console\Output\BufferedOutput();
        $app->make('Illuminate\Contracts\Console\Kernel')->call($cmd, [], $output);
        echo "<p><strong>php artisan $cmd:</strong> ✅ ".nl2br(htmlspecialchars($output->fetch()))."</p>";
    } catch (Exception $e) {
        echo "<p style='color:orange'><strong>php artisan $cmd WARNING:</strong> ".htmlspecialchars($e->getMessage())."</p>";
    }
}



// 3. Ejecutar migraciones con seeds (forzando)

try {

    $output = new \Symfony\Component\Console\Output\BufferedOutput();

    $app->make('Illuminate\Contracts\Console\Kernel')->call('migrate:fresh', [
        '--seed' => true,
        '--force' => true
    ], $output);

    echo "<p><strong>php artisan migrate:fresh --seed --force:</strong> ✅ ".nl2br(htmlspecialchars($output->fetch()))."</p>";

} catch (Exception $e) {

    echo "<p style='color:red'><strong>php artisan migrate:fresh ERROR:</strong> ".htmlspecialchars($e->getMessage())."</p>";

    // Mostrar SQL error si está disponible
    if (strpos($e->getMessage(), 'SQLSTATE') !== false) {
        echo "<p style='color:red'><strong>SQL Error:</strong> ".htmlspecialchars($e->getMessage())."</p>";
    }
}

// 4. Optimización final
$optimizeCommands = [
    'config:cache',
    'route:cache',
    'view:cache'
];

foreach ($optimizeCommands as $cmd) {
    try {
        $output = new \Symfony\Component\Console\Output\BufferedOutput();
        $app->make('Illuminate\Contracts\Console\Kernel')->call($cmd, [], $output);
        echo "<p><strong>php artisan $cmd:</strong> ✅ ".nl2br(htmlspecialchars($output->fetch()))."</p>";
    } catch (Exception $e) {
        echo "<p style='color:red'><strong>php artisan $cmd ERROR:</strong> ".htmlspecialchars($e->getMessage())."</p>";
    }
}

// 5. Verificación final
echo "<h3>Estado del Sistema</h3>";
echo "APP_ENV: ".$app->environment()."<br>";
echo "Debug mode: ".($app->hasDebugModeEnabled() ? 'true' : 'false')."<br>";
echo "APP_KEY: ".$_ENV['APP_KEY']."<br>";

// Verificar tablas creadas
try {
    $tables = DB::select('SHOW TABLES');
    echo "<p><strong>Tablas en la base de datos:</strong> ".count($tables)."</p>";
} catch (Exception $e) {
    echo "<p style='color:red'><strong>Error al verificar tablas:</strong> ".$e->getMessage()."</p>";
}

// 6. Enlace Simbólico para storage
echo "<h3>Enlace simbólico creado para storage</h3>";
// symlink('/home/'.$config['usuario_home'].'/'.$config['carpeta_laravel'].'/storage/app/public', '/home/'.$config['usuario_home'].'/'.$config['carpeta_public'].'/storage');
symlink(__DIR__.'/../'.$config['carpeta_laravel'].'/storage/app/public', __DIR__.'/storage');
echo "<h3 style='color:green'>✅ Proceso completado exitosamente</h3>";
echo "<h1 style='color:red'>IMPORTANTE: ELIMINA ESTE SCRIPT DE TU SERVIDOR!</h1>";
?>