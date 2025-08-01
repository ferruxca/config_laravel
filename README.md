# Configuración Inicial de Servidor Laravel

Este repositorio contiene un script de autoconfiguración automatizado para desplegar una aplicación Laravel en un servidor.

> Desarrollado por Jesús Ferruzca Anaya

Este script fué creado con la idea de automatizar el despliegue de una app laravel en producción en entornos donde no se tiene acceso a la consola del servidor vía SSH.

> En entornos de producción se recomienda eliminar los archivos `autoconfig.php` una vez completada la configuración.

## Archivos Principales

> `autoconfig.php`

### Descripción

> Este script PHP proporciona una interfaz web para ejecutar operaciones comunes de configuración y mantenimiento en proyectos Laravel. Está diseñado para facilitar las tareas de despliegue y mantenimiento, especialmente en entornos de desarrollo y staging.

### Características principales:

- **Gestión del entorno**: Cambia entre entornos local/production y modo debug
- **Limpieza de cachés**: Clear de cachés de aplicación, configuración, vistas y rutas
- **Optimización**: Cachear configuración, rutas y vistas
- **Migraciones**: Ejecuta migraciones con diversas opciones (fresh, refresh, seed, etc.)
- **Seguridad**: Generación de clave de aplicación
- **Configuración inicial**: Configuración automática de AppServiceProvider
- **Enlaces simbólicos**: Creación del enlace de storage en public

### Requisitos

- PHP 7.4 o superior
- Laravel 8.x o superior
- Permisos de escritura en los directorios del proyecto
- Acceso a la línea de comandos (para ejecutar los comandos artisan)

### Instalación

1. Copia el archivo `autoconfig.php` en la raíz de tu proyecto Laravel
2. Asegúrate de que el archivo tenga permisos de ejecución adecuados

### Uso

### Acceso al script:

Accede al script a través de tu navegador web en la ruta donde lo hayas colocado, por ejemplo:
`https://tudominio.com/autoconfig.php`

### Interfaz de usuario:

El script presenta una interfaz web con las siguientes secciones:

1. **Configuración del Entorno**:
   - Cambiar entre entornos local/production
   - Activar/desactivar debug mode
   - Opción para restaurar configuración original

2. **Configuración de AppServiceProvider**:
   - Opción para configurar automáticamente el defaultStringLength

3. **Enlace de Almacenamiento**:
   - Crear enlace simbólico de storage en public

4. **Comandos de Limpieza y Optimización**:
   - Clear de varios tipos de caché
   - Cache de configuración, rutas y vistas

5. **Opciones de Migración**:
   - Variantes de migración (fresh, refresh, seed, etc.)

### Proceso de ejecución:

1. Selecciona las opciones deseadas
2. Haz clic en "Ejecutar Configuración Seleccionada"
3. El script mostrará el resultado de cada operación
4. Revisa los mensajes de éxito/error

### Opciones avanzadas

### Configuración del entorno:

- **APP_ENV**: Cambia entre 'local' y 'production'
- **APP_DEBUG**: Activa/desactiva el modo debug
- **Restaurar configuración**: Vuelve a la configuración original al finalizar

### Migraciones:

- **migrate**: Ejecuta migraciones pendientes
- **migrate --seed**: Ejecuta migraciones y seeders
- **migrate:fresh**: Elimina todas las tablas y vuelve a migrar
- **migrate:refresh**: Resetea y vuelve a migrar
- **migrate:rollback**: Revierte la última migración

### Seguridad

⚠️ **ADVERTENCIA IMPORTANTE**:

Este script tiene acceso completo a tu aplicación Laravel. Por seguridad: