<?php
/**
 * Clase encargada de las rutas
 * 
 */

namespace Fw;

class Paths  
{
    public string $pluginFilePath;

    public function __construct($pluginFilePath) {
        $this->pluginFilePath = $pluginFilePath;
        $this->setPaths();
    }

    /**
     * Construye una ruta con el separador de directorio apropiado
     *
     * @param String $segments,... Número ilimitado de segmentos de ruta
     * @return String Path
     **/
    public static function buildPath(string ...$segments) : string
    {
        return join(DIRECTORY_SEPARATOR, $segments);
    }

    /**
     * Crear una carpeta en una ruta especifica
     *
     * @param String $path Ruta del directorio
     * @param Int $permissions Permisos para el directorio
     * @return Void
     **/
    public static function createFolder(string $path, int $permissions) : void
    {
        $old_umask = umask(0);
        mkdir($path, $permissions);
        umask($old_umask);
    }

    /**
     * Crear un file en una ruta especifica
     *
     * @param String $path Ruta del directorio
     * @param String $content contenido para el file
     * @return Mixes
     **/
    public static function createFile(string $path, string $content)
    {
        # Se crea el archivo si no existe
        $file = fopen($path, "w+b");
        if ( $file == false ) {
            echo "Error al crear el archivo: " . basename($path);
        } else {
            # Se escribe el contenido
            fwrite($file, $content);
            # Fuerza a que se escriban los datos pendientes en el buffer
            fflush($file);
        }
        # Cerrar el archivo
        fclose($file);
    }

    /**
     * Copia un file en una ruta especifica
     *
     * @param String $file Ruta del file que se copiara
     * @param String $copy Ruta para el nuevo file
     * @return Mixes
     **/
    public static function copyFile(string $file, string $copy)
    {
        if ( !copy($file, $copy) ) {
            echo "Error al copiar: $file";
        }
    }

    /**
     * Se establecen las rutas de la App
     * 
     * @return Void
     **/
    public function setPaths() : void
    {
        $this->pluginPath = dirname($this->pluginFilePath);
        $this->app = self::buildPath($this->pluginPath, 'app');

        $this->assets = self::buildPath($this->app, 'assets');
        $this->adminAssets = self::buildPath($this->assets, 'admin');
        
        // $this->controllers = self::buildPath($this->pluginPath, 'app', 'assets', 'admin');
        
        $this->helpers = self::buildPath($this->app, 'helpers');
    }
}

