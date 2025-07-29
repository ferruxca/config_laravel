<?php
/**
 * Archivo de configuración centralizado para los scripts de configuración de Laravel
 * 
 * Este archivo contiene las variables de entorno comunes utilizadas por los scripts
 * de configuración. Modifica estos valores según tu entorno de despliegue.
 */

return [
    // Configuración de directorios
    'carpeta_laravel' => 'laravel',      // Nombre de la carpeta de la app Laravel
    'carpeta_public' => 'public_html',   // Carpeta donde se copió el contenido de public/
    'usuario_home' => 'myuser',          // Usuario del sistema donde se despliega
];
