<?php
/**
 * Clase encargada de las rutas.
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
     * Construye una ruta con el separador de directorio apropiado.
     *
     * @param string $segments,... Número ilimitado de segmentos de ruta
     * @return string Path
     **/
    public static function buildPath(string ...$segments) : string
    {
        return join(DIRECTORY_SEPARATOR, $segments);
    }

    /**
     * Crea y retorna el namespace usando basename del path pasado.
     *
     * @param string $name Ruta|Nombre para retornar para usar como namespace.
     * @return string
     **/
    public static function createNamepace(string $name) : string 
    {
        $namespace = basename($name, '.php');
        $namespace = ucwords($namespace, '-');
        $namespace = str_replace('-', '', $namespace);

        return $namespace;
    }

    /**
     * Recorta una ruta determinados niveles
     *
     * @param string $path
     * @param int $levels
     * @return string
     **/
    public static function trimPath(string $path, int $levels)
    {
        $path = explode(DIRECTORY_SEPARATOR, $path);
        if ( $levels <= 0 || $levels > count($path) ) {
            return implode(DIRECTORY_SEPARATOR, $path);
        }

        for ($i=0; $i < $levels; $i++) { 
            array_pop($path);
        }

        return implode(DIRECTORY_SEPARATOR, $path);
    }

    /**
     * Se establecen las rutas de la App.
     * 
     * @return void
     **/
    public function setPaths() : void
    {
        $this->pluginPath = dirname($this->pluginFilePath);
        $this->app = self::buildPath($this->pluginPath, 'app');

        $this->assets = self::buildPath($this->app, 'assets');
        $this->adminAssets = self::buildPath($this->assets, 'admin');

        $this->controllers = (object) [
            'public' => self::buildPath($this->pluginPath, 'app', 'Controllers', 'Web'),
            'menuPage' => self::buildPath($this->pluginPath, 'app', 'Controllers', 'MenuPage'),
            'shortcode' => self::buildPath($this->pluginPath, 'app', 'Controllers', 'shortcode'),
        ];

        $this->helpers = self::buildPath($this->app, 'helpers');
    }
}

