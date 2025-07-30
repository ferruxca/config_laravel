<?php
require __DIR__.'/../laravel/vendor/autoload.php';
$app = require_once __DIR__.'/../laravel/bootstrap/app.php';

// Función para cambiar el entorno
function setEnvironment($app, $env, $debug) {
    $envFile = __DIR__.'/../laravel/.env';
    $contents = file_get_contents($envFile);
    
    $contents = preg_replace('/APP_ENV=.*/', 'APP_ENV='.$env, $contents);
    $contents = preg_replace('/APP_DEBUG=.*/', 'APP_DEBUG='.$debug, $contents);
    
    file_put_contents($envFile, $contents);
    
    // Limpiar configuración cacheada
    if (file_exists($app->getCachedConfigPath())) {
        unlink($app->getCachedConfigPath());
    }
}

// Mostrar formulario si no se ha enviado
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Configuración Laravel Avanzada</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .form-container { max-width: 800px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
            .form-group { margin-bottom: 15px; padding: 10px; border: 1px solid #eee; border-radius: 4px; }
            .form-group h3 { margin-top: 0; }
            label { display: block; margin-bottom: 5px; }
            input[type="checkbox"], input[type="radio"] { margin-right: 10px; }
            button { background-color: #4CAF50; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
            button:hover { background-color: #45a049; }
            .output { margin-top: 20px; padding: 15px; background-color: #f5f5f5; border-radius: 4px; }
            .command-group { margin-left: 20px; }
            .sub-options { margin-left: 25px; padding-left: 15px; border-left: 2px solid #ddd; }
        </style>
    </head>
    <body>
        <div class="form-container">
            <h2>Configuración Avanzada de Laravel</h2>
            <form method="post">
                
                <div class="form-group">
                    <h3>Opciones de Entorno</h3>
                    <label>
                        <input type="checkbox" name="change_environment" checked> Cambiar temporalmente a entorno local (debug true)
                    </label>
                </div>
                
                <div class="form-group">
                    <h3>Comandos de Limpieza</h3>
                    <div class="command-group">
                        <label><input type="checkbox" name="commands[]" value="cache:clear" checked> cache:clear</label>
                        <label><input type="checkbox" name="commands[]" value="config:clear" checked> config:clear</label>
                        <label><input type="checkbox" name="commands[]" value="view:clear" checked> view:clear</label>
                        <label><input type="checkbox" name="commands[]" value="route:clear" checked> route:clear</label>
                        <label><input type="checkbox" name="commands[]" value="optimize:clear" checked> optimize:clear</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <h3>Generar Clave de Aplicación</h3>
                    <label>
                        <input type="checkbox" name="commands[]" value="key:generate --force"> key:generate --force
                    </label>
                </div>
                
                <div class="form-group">
                    <h3>Opciones de Migración</h3>
                    <label>
                        <input type="radio" name="migration_option" value="none"> No ejecutar migraciones
                    </label>
                    <label>
                        <input type="radio" name="migration_option" value="migrate" checked> Ejecutar migraciones normales
                    </label>
                    <label>
                        <input type="radio" name="migration_option" value="migrate:fresh"> Ejecutar migraciones fresh (eliminará todas las tablas)
                    </label>
                    <label>
                        <input type="radio" name="migration_option" value="migrate:rollback"> Ejecutar rollback de migraciones
                    </label>
                    <label>
                        <input type="radio" name="migration_option" value="migrate:status"> Mostrar estado de migraciones
                    </label>
                    <div class="sub-options" id="migration-options">
                        <label>
                            <input type="checkbox" name="force_migration" checked> Forzar migraciones (ignorar advertencias de producción)
                        </label>
                        <label>
                            <input type="checkbox" name="run_seeders"> Ejecutar seeders después de migrar
                        </label>
                    </div>
                </div>
                
                <div class="form-group">
                    <h3>Optimización</h3>
                    <div class="command-group">
                        <label><input type="checkbox" name="commands[]" value="config:cache" checked> config:cache</label>
                        <label><input type="checkbox" name="commands[]" value="route:cache" checked> route:cache</label>
                        <label><input type="checkbox" name="commands[]" value="view:cache" checked> view:cache</label>
                    </div>
                </div>
                
                <button type="submit">Ejecutar Configuración Seleccionada</button>
            </form>
        </div>
        
        <script>
            // Ocultar opciones de migración cuando se selecciona "No ejecutar migraciones"
            document.querySelectorAll('input[name="migration_option"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    const optionsDiv = document.getElementById('migration-options');
                    optionsDiv.style.display = this.value === 'none' ? 'none' : 'block';
                });
            });
            
            // Inicializar estado
            document.addEventListener('DOMContentLoaded', function() {
                const noneSelected = document.querySelector('input[name="migration_option"][value="none"]').checked;
                document.getElementById('migration-options').style.display = noneSelected ? 'none' : 'block';
            });
        </script>
    </body>
    </html>
    <?php
    exit;
}

// Procesar el formulario
echo '<div class="output">';
echo "<h2>Ejecución de Configuración</h2>";

// Cambiar a modo desarrollo si está seleccionado
if (isset($_POST['change_environment'])) {
    setEnvironment($app, 'local', 'true');
    echo "<p>✅ Modo cambiado a: local (debug true)</p>";
}

// 1. Configurar el AppServiceProvider para evitar el error de longitud
file_put_contents(__DIR__.'/../laravel/app/Providers/AppServiceProvider.php', 
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
echo "<p>✅ AppServiceProvider configurado</p>";

// 2. Ejecutar comandos seleccionados en orden óptimo
$commandGroups = [
    'Limpieza' => [
        'cache:clear',
        'config:clear',
        'view:clear',
        'route:clear',
        'optimize:clear'
    ],
    'Generación' => [
        'key:generate --force'
    ],
    'Migración' => [],
    'Seeders' => [],
    'Optimización' => [
        'config:cache',
        'route:cache',
        'view:cache'
    ]
];

// Procesar migración según selección
if (isset($_POST['migration_option']) && $_POST['migration_option'] !== 'none') {
    $migrationCommand = $_POST['migration_option'];
    $migrationOptions = [];
    
    if (isset($_POST['force_migration'])) {
        $migrationOptions['--force'] = true;
    }
    
    // Configurar seeders si está seleccionado y no es migrate:status o migrate:rollback
    if (isset($_POST['run_seeders']) && $_POST['run_seeders'] && 
        !in_array($migrationCommand, ['migrate:status', 'migrate:rollback'])) {
        $migrationOptions['--seed'] = true;
        $commandGroups['Seeders'] = ['db:seed' => ['--force' => true]];
    }
    
    $commandGroups['Migración'] = [$migrationCommand => $migrationOptions];
}

// Ejecutar todos los comandos seleccionados
foreach ($commandGroups as $groupName => $commands) {
    if (empty($commands)) continue;
    
    echo "<h3>{$groupName}</h3>";
    
    foreach ($commands as $cmd => $options) {
        // Manejar tanto comandos simples como con opciones
        $command = is_string($cmd) ? $cmd : $options;
        $commandOptions = is_array($options) ? $options : [];
        
        // Caso especial para migración y seeders (no están en $_POST['commands'])
        if ($groupName === 'Migración' || $groupName === 'Seeders') {
            try {
                $output = new \Symfony\Component\Console\Output\BufferedOutput();
                $app->make('Illuminate\Contracts\Console\Kernel')->call($cmd, $options, $output);
                $outputText = $output->fetch();
                
                echo "<p><strong>php artisan {$cmd}".(!empty($options) ? ' '.implode(' ', array_map(function($k, $v) {
                    return $v === true ? "--{$k}" : "--{$k}={$v}";
                }, array_keys($options), $options)) : '').":</strong> ✅ ".nl2br(htmlspecialchars($outputText))."</p>";
            } catch (Exception $e) {
                $errorClass = strpos($e->getMessage(), 'SQLSTATE') !== false ? 'error' : 'warning';
                echo "<p class='{$errorClass}'><strong>php artisan {$cmd} ERROR:</strong> ".htmlspecialchars($e->getMessage())."</p>";
                if (strpos($e->getMessage(), 'SQLSTATE') !== false) {
                    echo "<p class='error'><strong>SQL Error:</strong> ".htmlspecialchars($e->getMessage())."</p>";
                }
            }
            continue;
        }
        
        // Verificar si el comando fue seleccionado
        $baseCommand = explode(' ', $command)[0];
        if (!isset($_POST['commands']) && in_array($baseCommand, ['cache:clear','config:clear','view:clear','route:clear','optimize:clear','config:cache','route:cache','view:cache'])) {
            continue;
        }
        if (isset($_POST['commands']) && !in_array($command, $_POST['commands']) && !in_array($baseCommand, $_POST['commands'])) {
            continue;
        }
        
        try {
            $output = new \Symfony\Component\Console\Output\BufferedOutput();
            $app->make('Illuminate\Contracts\Console\Kernel')->call($command, $commandOptions, $output);
            $outputText = $output->fetch();
            
            echo "<p><strong>php artisan {$command}".(!empty($commandOptions) ? ' '.implode(' ', array_map(function($k, $v) {
                return $v === true ? "--{$k}" : "--{$k}={$v}";
            }, array_keys($commandOptions), $commandOptions)) : '').":</strong> ✅ ".nl2br(htmlspecialchars($outputText))."</p>";
        } catch (Exception $e) {
            $errorClass = strpos($e->getMessage(), 'SQLSTATE') !== false ? 'error' : 'warning';
            echo "<p class='{$errorClass}'><strong>php artisan {$command} ERROR:</strong> ".htmlspecialchars($e->getMessage())."</p>";
            if (strpos($e->getMessage(), 'SQLSTATE') !== false) {
                echo "<p class='error'><strong>SQL Error:</strong> ".htmlspecialchars($e->getMessage())."</p>";
            }
        }
    }
}

// Verificación final
echo "<h3>Estado del Sistema</h3>";
echo "APP_ENV: ".$app->environment()."<br>";
echo "Debug mode: ".($app->hasDebugModeEnabled() ? 'true' : 'false')."<br>";
echo "APP_KEY: ".(isset($_ENV['APP_KEY']) ? $_ENV['APP_KEY'] : 'No definida')."<br>";

// Verificar tablas creadas
try {
    $tables = DB::select('SHOW TABLES');
    echo "<p><strong>Tablas en la base de datos:</strong> ".count($tables)."</p>";
    
    // Mostrar estado de migraciones si no se seleccionó migrate:status y no se eligió "none"
    if ((!isset($_POST['migration_option']) || ($_POST['migration_option'] !== 'migrate:status' && $_POST['migration_option'] !== 'none'))) {
        $output = new \Symfony\Component\Console\Output\BufferedOutput();
        $app->make('Illuminate\Contracts\Console\Kernel')->call('migrate:status', [], $output);
        echo "<h4>Estado de Migraciones</h4>";
        echo "<pre>".htmlspecialchars($output->fetch())."</pre>";
    }
} catch (Exception $e) {
    echo "<p class='error'><strong>Error al verificar tablas:</strong> ".$e->getMessage()."</p>";
}

// Restaurar entorno si se cambió
if (isset($_POST['change_environment'])) {
    setEnvironment($app, 'production', 'false');
    echo "<p>✅ Modo restaurado a: production (debug false)</p>";
}

echo "<h3 style='color:green'>✅ Proceso completado exitosamente</h3>";
echo '</div>';

// Estilo para la salida
echo '
<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .output { max-width: 800px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
    h2, h3, h4 { color: #333; }
    p { margin: 5px 0; padding: 5px; }
    pre { background: #f5f5f5; padding: 10px; border-radius: 4px; overflow-x: auto; }
    .success { color: green; background-color: #f0fff0; border-left: 3px solid green; }
    .warning { color: #8a6d3b; background-color: #fcf8e3; border-left: 3px solid #faebcc; }
    .error { color: #a94442; background-color: #f2dede; border-left: 3px solid #ebccd1; }
    .info { color: #31708f; background-color: #d9edf7; border-left: 3px solid #bce8f1; }
</style>';
?>