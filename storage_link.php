<?php

// Cargar configuración centralizada
$config = require __DIR__.'/config.php';

// Crear enlace simbólico para el almacenamiento
$target = '/home/'.$config['usuario_home'].'/'.$config['carpeta_laravel'].'/storage/app/public';
$link = '/home/'.$config['usuario_home'].'/'.$config['carpeta_public'].'/storage';

// Intentar eliminar el enlace si ya existe
if (file_exists($link) || is_link($link)) {
    unlink($link);
}

// Crear el enlace simbólico
if (symlink($target, $link)) {
    echo "Enlace simbólico creado exitosamente de $target a $link";
} else {
    echo "Error al crear el enlace simbólico";
}

echo "<h3 style='color:green'>✅ Proceso completado exitosamente</h3>";
?>
