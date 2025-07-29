# Configuración Inicial de Servidor Laravel

Este repositorio contiene scripts de configuración automatizados para desplegar una aplicación Laravel en un servidor.

> Desarrollado por Jesús Ferruzca Anaya

Estos script fueron creados con la idea de automatizar el despliegue de una app laravel en producción en entornos donde no se tiene acceso a la consola del servidor vía SSH.

> En entornos de producción se recomienda eliminar los archivos `autoconfig.php`, `config_session.php` y `storage_link.php` una vez completada la configuración.

## 📋 Archivos Principales

### 1. `autoconfig.php`
El script principal que automatiza la configuración inicial de la aplicación Laravel:
- Configura el AppServiceProvider con la longitud de cadena predeterminada
- Limpia las cachés de configuración, vistas y rutas
- Genera una nueva clave de aplicación
- Ejecuta migraciones con seeders
- Optimiza la aplicación almacenando en caché configuraciones, rutas y vistas
- Crea un enlace simbólico para el almacenamiento
- Verifica el estado del sistema

### 2. `config_session.php`
Configura el sistema de sesiones de Laravel:
- Establece el controlador de sesión a 'database'
- Crea y ejecuta migraciones para la tabla de sesiones
- Verifica la configuración de sesión

### 3. `storage_link.php`
Crea un enlace simbólico desde el directorio de almacenamiento al directorio público.

## 🚀 Instrucciones de Uso

### Requisitos Previos
- Servidor con PHP 8.2 o superior
- Composer instalado
- Base de datos configurada
- Aplicación Laravel desplegada en el servidor

### Pasos para la Configuración

1. **Preparación del Entorno**
   - Asegúrate de que tu aplicación Laravel esté en el directorio `/home/myuser/laravel` o donde se despliega la app.
   - Los archivos públicos deben estar en `/home/myuser/public_html` o donde se despliega la carpeta public de la app.
   - Copia el script `autoconfig.php` a la carpeta `/home/myuser/public_html` o donde se despliega la carpeta public de la app.
   - Actualiza el usuario del sistema en `config.php` si es necesario

   Ejemplo de configuración:
   ```php
    return [
        'carpeta_laravel' => 'laravel',      // Nombre de la carpeta de la app Laravel
        'carpeta_public' => 'public_html',   // Carpeta donde se copió el contenido de public/
        'usuario_home' => 'myuser',          // Usuario del sistema donde se despliega
    ];
   ```

2. **Configuración Inicial**
   Si tienes acceso vía SSH ejecuta el script principal de configuración:
   ```bash
   php autoconfig.php
   ```
   Si no tienes acceso vía SSH ejecuta el script principal de configuración desde el navegador:
   ```
   http://localhost/autoconfig.php
   ```

3. **Configuración de Sesiones**
   Si tienes acceso vía SSH ejecuta el script principal de configuración:
   ```bash
   php config_session.php
   ```
   Si no tienes acceso vía SSH ejecuta el script principal de configuración desde el navegador:
   ```
   http://localhost/config_session.php
   ```

4. **Enlace de Almacenamiento**
   Si tienes acceso vía SSH ejecuta el script principal de configuración:
   ```bash
   php storage_link.php
   ```
   Si no tienes acceso vía SSH ejecuta el script principal de configuración desde el navegador:
   ```
   http://localhost/storage_link.php
   ```

## Importante de Seguridad

**ELIMINA ESTOS ARCHIVOS DEL SERVIDOR UNA VEZ COMPLETADA LA CONFIGURACIÓN**

Estos scripts contienen información sensible y no deben permanecer en el servidor después de su uso en producción.

## Verificación

Después de ejecutar los scripts, verifica que:
- Las migraciones se hayan ejecutado correctamente
- El enlace simbólico de almacenamiento funcione
- La aplicación sea accesible y funcione como se espera

## Notas Adicionales

- Asegúrate de tener permisos de escritura en los directorios necesarios
- Revisa los logs de Laravel si encuentras algún problema
- Personaliza los valores en `config.php` según tu configuración de servidor