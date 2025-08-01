<?php
/**
 * SCRIPT DE CONFIGURACIÓN LARAVEL - ADVERTENCIA DE SEGURIDAD
 * 
 * Este script es una herramienta poderosa que puede modificar tu entorno Laravel.
 * POR SEGURIDAD, DEBES ELIMINAR ESTE ARCHIVO DESPUÉS DE USARLO para evitar
 * posibles accesos no autorizados a tu sistema.
 * 
 */

 // --- INICIO DE LA SECCIÓN DE SEGURIDAD ---
 // Descomentar la siguiente línea para detener la ejecución
/*  header("Location: https://ferruzca.com/");
 die(); */
 // --- FIN DE LA SECCIÓN DE SEGURIDAD ---

$path_project = realpath(__DIR__.'/../').'/';
$path_public = realpath(__DIR__.'/../').'/public/';

/*
// Ejemplo de uso 
    $path_project = realpath(__DIR__.'/../').'/laravel/';
    $path_public = realpath(__DIR__.'/../').'/public_html/'; 
*/

// Cargar Laravel
require $path_project.'vendor/autoload.php';
$app = require_once $path_project.'bootstrap/app.php';

// Definir comandos con estado inicial (checked)
$commandGroups = [
    'Limpieza' => [
        'cache:clear' => ['description' => 'Limpiar cache de aplicación', 'checked' => true],
        'config:clear' => ['description' => 'Limpiar configuración cacheada', 'checked' => true],
        'view:clear' => ['description' => 'Limpiar vistas compiladas', 'checked' => true],
        'route:clear' => ['description' => 'Limpiar cache de rutas', 'checked' => true],
        'optimize:clear' => ['description' => 'Limpiar archivos optimizados', 'checked' => true]
    ],
    'Optimización' => [
        'config:cache' => ['description' => 'Cachear configuración', 'checked' => true],
        'route:cache' => ['description' => 'Cachear rutas', 'checked' => true],
        'view:cache' => ['description' => 'Cachear vistas', 'checked' => true]
    ],
    'Generación' => [
        'key:generate --force' => ['description' => 'Generar clave de aplicación', 'checked' => false]
    ]
];

$migrationOptions = [
    'none' => 'No ejecutar migraciones',
    'migrate' => '<b>(migrate)</b> Ejecutar migraciones normales',
    'migrate --seed' => '<b>(migrate --seed)</b> Ejecutar migraciones normales y seed',
    'migrate:fresh' => '<b>(migrate:fresh)</b> Ejecutar migraciones fresh (eliminará todas las tablas)',
    'migrate:rollback' => '<b>(migrate:rollback)</b> Ejecutar rollback de migraciones',
    'migrate:status' => '<b>(migrate:status)</b> Mostrar estado de migraciones',
    'migrate:refresh' => '<b>(migrate:refresh)</b> Ejecutar migraciones refresh (eliminará todas las tablas)',
    'migrate:reset' => '<b>(migrate:reset)</b> Ejecutar migraciones reset (eliminará todas las tablas)',
    'migrate:refresh --seed' => '<b>(migrate:refresh --seed)</b> Ejecutar migraciones refresh y seed (eliminará todas las tablas)',
    'migrate:refresh --seed --force' => '<b>(migrate:refresh --seed --force)</b> Ejecutar migraciones refresh y seed (eliminará todas las tablas y forzará)',
];

// Función para cambiar el entorno
function setEnvironment($path_project, $env, $debug) {
    $envFile = $path_project.'.env';
    if (!file_exists($envFile)) {
        throw new Exception("Archivo .env no encontrado");
    }
    
    $contents = file_get_contents($envFile);
    if ($contents === false) {
        throw new Exception("No se pudo leer el archivo .env");
    }
    
    $contents = preg_replace('/APP_ENV=.*/', 'APP_ENV='.$env, $contents);
    $contents = preg_replace('/APP_DEBUG=.*/', 'APP_DEBUG='.$debug, $contents);
    
    if (file_put_contents($envFile, $contents) === false) {
        throw new Exception("No se pudo escribir en el archivo .env");
    }
}

// Función para ejecutar comandos
function executeCommand($app, $command, $options = []) {
    try {
        $output = new \Symfony\Component\Console\Output\BufferedOutput();
        $app->make('Illuminate\Contracts\Console\Kernel')->call($command, $options, $output);
        return [
            'success' => true,
            'output' => $output->fetch()
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

// Función para configurar AppServiceProvider
function configureAppServiceProvider($path_project) {
    $content = '<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Schema::defaultStringLength(191);
    }
}';

    $filePath = $path_project.'app/Providers/AppServiceProvider.php';
    if (file_put_contents($filePath, $content) === false) {
        throw new Exception("No se pudo escribir en AppServiceProvider.php");
    }
}

// Función para crear enlace simbólico
function createStorageLink($path_project, $path_public) {
    $target = $path_project.'storage/app/public';
    $link = $path_public.'storage';
    
    // Verificar si ya existe el enlace
    if (file_exists($link)) {
        if (is_link($link)) {
            unlink($link); // Eliminar enlace existente
        } else {
            // Eliminar carpeta
            if (!rmdir($link)) {
                throw new Exception("No se pudo eliminar el directorio 'storage'");
            }else{
                echo "Carpeta 'storage' eliminada exitosamente";
            }
        }
    }
    
    // Crear el enlace simbólico
    if (!symlink($target, $link)) {
        throw new Exception("No se pudo crear el enlace simbólico");
    }else{
        echo "Enlace simbólico creado exitosamente";
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
            body { font-family: Arial, sans-serif; margin: 20px; background-color: #f9f9f9; }
            .form-container { max-width: 800px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
            .form-group { margin-bottom: 15px; padding: 15px; border: 1px solid #eee; border-radius: 4px; background: #fafafa; }
            .form-group h3 { margin-top: 0; color: #2c3e50; }
            label { display: block; margin-bottom: 8px; cursor: pointer; }
            input[type="checkbox"], input[type="radio"] { margin-right: 10px; }
            select, input[type="text"] { padding: 8px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 10px; }
            button { background-color: #3498db; color: white; padding: 12px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; transition: background-color 0.3s; }
            button:hover { background-color: #2980b9; }
            .output { margin-top: 20px; padding: 20px; background-color: white; border-radius: 5px; border: 1px solid #ddd; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
            .command-group { margin-left: 15px; }
            .command-item { margin-bottom: 8px; padding: 8px; background: white; border-radius: 3px; border-left: 3px solid #3498db; }
            .success { color: #27ae60; }
            .error { color: #e74c3c; }
            .warning { color: #f39c12; }
            .info { color: #3498db; }
            .security-warning { 
                background-color: #fff3cd; 
                border-left: 4px solid #ffc107; 
                padding: 15px; 
                margin-bottom: 20px;
                border-radius: 4px;
            }
            .sub-group { margin-left: 20px; padding: 10px; background: #f0f0f0; border-radius: 4px; }
        </style>
    </head>
    <body>
        <div class="form-container">
            <div class="security-warning">
                <h3>⚠️ ADVERTENCIA DE SEGURIDAD</h3>
                <p>Este script puede realizar cambios importantes en tu aplicación Laravel.</p>
                <p><strong>Por seguridad, elimina este archivo después de usarlo</strong> para evitar accesos no autorizados.</p>
            </div>
            
            <h2>Configuración Avanzada de Laravel</h2>
            <form method="post">
                
                <div class="form-group">
                    <h3>Configuración del Entorno</h3>
                    <div class="sub-group">
                        <label>
                            <input type="checkbox" name="change_environment" id="changeEnvCheckbox"> Cambiar configuración del entorno
                        </label>
                        
                        <div id="envOptions" style="display: none; margin-top: 10px;">
                            <div>
                                <label for="app_env">Entorno:</label>
                                <select name="app_env" id="app_env">
                                    <option value="local">Local</option>
                                    <option value="production">Production</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="app_debug">Debug:</label>
                                <select name="app_debug" id="app_debug">
                                    <option value="true">True</option>
                                    <option value="false">False</option>
                                </select>
                            </div>
                            
                            <label>
                                <input type="checkbox" name="restore_environment" checked> Restaurar configuración original al finalizar
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <h3>Configuración de AppServiceProvider</h3>
                    <label class="command-item">
                        <input type="checkbox" name="configure_app_provider"> Configurar AppServiceProvider (Schema::defaultStringLength)
                    </label>
                    <small class="info">Recomendado para evitar errores con índices en MySQL</small>
                </div>
                
                <div class="form-group">
                    <h3>Enlace de Almacenamiento</h3>
                    <label class="command-item">
                        <input type="checkbox" name="create_storage_link"> Crear enlace simbólico de storage
                    </label>
                    <small class="info">Creará un enlace de storage/app/public en la carpeta public</small>
                    <div class="sub-group">
                        <p><strong>Ruta del proyecto:</strong> <?php echo htmlspecialchars($path_project); ?></p>
                        <p><strong>Ruta pública:</strong> <?php echo htmlspecialchars($path_public); ?></p>
                    </div>
                </div>
                
                <?php foreach ($commandGroups as $groupName => $commands): ?>
                <div class="form-group">
                    <h3><?php echo htmlspecialchars($groupName); ?></h3>
                    <div class="command-group">
                        <?php foreach ($commands as $command => $data): ?>
                        <label class="command-item">
                            <input type="checkbox" name="commands[]" value="<?php echo htmlspecialchars($command); ?>" <?php echo $data['checked'] ? 'checked' : ''; ?>> 
                            <strong><?php echo explode(' ', $command)[0]; ?></strong> - 
                            <?php echo htmlspecialchars($data['description']); ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <div class="form-group">
                    <h3>Opciones de Migración</h3>
                    <div class="command-group">
                    <?php foreach ($migrationOptions as $value => $label): ?>
                    <label class="command-item">
                        <input type="radio" name="migration_option" value="<?php echo htmlspecialchars($value); ?>" <?php echo $value === 'none' ? 'checked' : ''; ?>> 
                        <?php echo $label; ?>
                    </label>
                    <?php endforeach; ?>
                    </div>
                </div>
                
                <button type="submit">Ejecutar Configuración Seleccionada</button>
            </form>
        </div>
        
        <script>
            // Mostrar/ocultar opciones de entorno
            document.getElementById('changeEnvCheckbox').addEventListener('change', function() {
                document.getElementById('envOptions').style.display = this.checked ? 'block' : 'none';
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

// Guardar configuración original del entorno
$originalEnv = $_ENV['APP_ENV'] ?? 'production';
$originalDebug = $_ENV['APP_DEBUG'] ?? 'false';

try {
    // Cambiar configuración del entorno si está seleccionado
    if (isset($_POST['change_environment'])) {
        $newEnv = $_POST['app_env'] ?? 'local';
        $newDebug = $_POST['app_debug'] ?? 'true';
        
        setEnvironment($path_project, $newEnv, $newDebug);
        echo "<p class='success'>✅ Entorno cambiado a: {$newEnv} (debug {$newDebug})</p>";
    }

    // Configurar el AppServiceProvider si está seleccionado
    if (isset($_POST['configure_app_provider'])) {
        configureAppServiceProvider($path_project);
        echo "<p class='success'>✅ AppServiceProvider configurado</p>";
    } else {
        echo "<p class='info'>⏩ Configuración de AppServiceProvider omitida</p>";
    }

    // Crear enlace simbólico si está seleccionado
    if (isset($_POST['create_storage_link'])) {
        try {
            createStorageLink($path_project, $path_public);
            echo "<p class='success'>✅ Enlace simbólico creado en: {$path_public}storage</p>";
            echo "<p class='info'>→ Apunta a: {$path_project}storage/app/public</p>";
        } catch (Exception $e) {
            echo "<p class='error'><strong>Error al crear enlace simbólico:</strong> ".$e->getMessage()."</p>";
        }
    }

    // Ejecutar comandos seleccionados
    $selectedCommands = $_POST['commands'] ?? [];
    
    foreach ($commandGroups as $groupName => $commands) {
        $executedCommands = false;
        
        foreach ($commands as $command => $data) {
            if (in_array($command, $selectedCommands)) {
                if (!$executedCommands) {
                    echo "<h3>{$groupName}</h3>";
                    $executedCommands = true;
                }
                
                $result = executeCommand($app, $command);
                
                if ($result['success']) {
                    echo "<p class='success'><strong>php artisan {$command}:</strong> ✅ ".nl2br(htmlspecialchars($result['output']))."</p>";
                } else {
                    echo "<p class='error'><strong>php artisan {$command} ERROR:</strong> ".htmlspecialchars($result['error'])."</p>";
                }
            }
        }
    }

    // Procesar migración según selección
    if (isset($_POST['migration_option']) && $_POST['migration_option'] !== 'none') {
        echo "<h3>Migración</h3>";
        $migrationCommand = $_POST['migration_option'];
        $result = executeCommand($app, $migrationCommand);
        
        if ($result['success']) {
            echo "<p class='success'><strong>php artisan {$migrationCommand}:</strong> ✅ ".nl2br(htmlspecialchars($result['output']))."</p>";
        } else {
            echo "<p class='error'><strong>php artisan {$migrationCommand} ERROR:</strong> ".htmlspecialchars($result['error'])."</p>";
        }
    }

    // Verificación final
    echo "<h3>Estado del Sistema</h3>";
    echo "<p><strong>APP_ENV:</strong> ".$app->environment()."</p>";
    echo "<p><strong>Debug mode:</strong> ".($app->hasDebugModeEnabled() ? 'true' : 'false')."</p>";
    echo "<p><strong>APP_KEY:</strong> ".(isset($_ENV['APP_KEY']) ? 'Definida' : 'No definida')."</p>";

    // Verificar tablas creadas
    try {
        $tables = DB::select('SHOW TABLES');
        echo "<p><strong>Tablas en la base de datos:</strong> ".count($tables)."</p>";
        
        if ((!isset($_POST['migration_option']) || ($_POST['migration_option'] !== 'migrate:status' && $_POST['migration_option'] !== 'none'))) {
            $result = executeCommand($app, 'migrate:status');
            echo "<h4>Estado de Migraciones</h4>";
            echo "<pre>".htmlspecialchars($result['output'])."</pre>";
        }
    } catch (Exception $e) {
        echo "<p class='error'><strong>Error al verificar tablas:</strong> ".$e->getMessage()."</p>";
    }

    // Restaurar entorno si se cambió y está seleccionado
    if (isset($_POST['change_environment']) && isset($_POST['restore_environment'])) {
        setEnvironment($path_project, $originalEnv, $originalDebug);
        echo "<p class='success'>✅ Entorno restaurado a: {$originalEnv} (debug {$originalDebug})</p>";
    }

    echo "<h3 style='color:#27ae60'>✅ Proceso completado exitosamente</h3>";

} catch (Exception $e) {
    echo "<p class='error'><strong>Error crítico:</strong> ".$e->getMessage()."</p>";
}

echo '</div>';