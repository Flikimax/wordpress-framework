<?php
/**
 * Clase encargada de las rutas.
 * 
 */

namespace Fw;

use Fw\Config\Apps;

class Paths  
{
    /** @var string $pluginFilePath Ruta del archivo principal de la aplicación. */
    public string $pluginFilePath;

    public function __construct($pluginFilePath) {
        $this->pluginFilePath = $pluginFilePath;
        $this->setPaths();
    }

    /**
     * Construye una ruta con el separador de directorio apropiado.
     *
     * @param string $segments,... Número ilimitado de segmentos de ruta.
     * @return string Path
     **/
    public static function buildPath(string ...$segments) : string
    {
        return join(DIRECTORY_SEPARATOR, $segments);
    }

    /**
     * Construye una ruta namespace.
     *
     * @param string $segments Número ilimitado de segmentos de ruta.
     * @return string
     **/
    public static function buildNamespacePath(string ...$segments) : string
    {
        return join('\\', $segments);
    }

    /**
     * Crea y retorna el namespace usando basename del path pasado.
     *
     * @param string $name Ruta|Nombre para retornar para usar como namespace.
     * @return string
     **/
    public static function createNamespace(string $name) : string 
    {
        $namespace = Apps::getConfig( $name, 'config' )?->namespace;
        if ( $namespace ) {
            return $namespace;
        }
        
        $namespace = basename($name, '.php');
        $namespace = ucwords($namespace, '-');
        $namespace = str_replace(' ', '', $namespace);
        $namespace = str_replace('-', '', $namespace);

        return $namespace;
    }

    /**
     * Lista en un array los archivos que esten en la ruta especificada.
     *
     * @param string $pluginPath Ruta de la aplicación.
     * @param string $path Ruta los archivos a listar.
     * @param string $match Coincidencia para los archivos.
     * @return array
     **/
    public static function listFiles(string $pluginPath, string $path, string $match = '*') : array
    {   # Archivos para autocargar
        $files = array();
        foreach (glob(self::buildPath($path, $match)) as $index => $file) {
            $file = str_replace($pluginPath, '', $file);
            $files[] = ltrim($file, '/');
        }
        return $files;
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
     * Encuentra la ruta raiz de la aplicación.
     *
     * @param string $path Cualquier ruta interna de la aplicación.
     * @return string|null
     **/
    public static function findPluginPath(string $path) : ?string
    {
        $pluginsPath = self::buildPath('wp-content', 'plugins', '');
        $pluginPath = strstr($path, $pluginsPath);

        if ( $pluginPath ) {
            $pluginPath = str_replace($pluginsPath, '', $pluginPath);
            $pluginPath = explode(DIRECTORY_SEPARATOR, $pluginPath);

            return self::buildPath(WP_PLUGIN_DIR, $pluginPath[0], 'app');
        }

        return null;
    }

    /**
     * Encuentra la ruta raiz de la aplicación (URL).
     *
     * @param string $path Cualquier ruta interna de la aplicación.
     * @return string|null
     **/
    public static function findPluginUrl(string $path) : ?string
    {
        $path = self::parsePath($path);
        $pluginsPath = self::buildPath('wp-content', 'plugins', '');
        $pluginPath = strstr($path, $pluginsPath);

        if ( $pluginPath ) {
            $pluginPath = str_replace($pluginsPath, '', $pluginPath);
            $pluginPath = explode(DIRECTORY_SEPARATOR, $pluginPath);

            return plugins_url($pluginPath[0] . '/app');
        }

        return null;
    }

    /**
     * Convierte una ruta o url con su respectivo separador.
     *
     * @param string $path Ruta a convertir.
     * @param string $isUrl Determinará si se usa '/' ó . DIRECTORY_SEPARATOR
     * @return string
     **/
    public static function parsePath(string $path, bool $isUrl = false) : string
    {
        $separator = DIRECTORY_SEPARATOR;
        if ( $isUrl ) {
            $separator = '/';
        }

        $path = str_replace('/', $separator, $path);
        $path = str_replace('\\', $separator, $path);

        return $path;
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
            'routes' => self::buildPath($this->pluginPath, 'app', 'Controllers', 'Routes'),
            'menuPages' => self::buildPath($this->pluginPath, 'app', 'Controllers', 'MenuPages'),
            'shortcodes' => self::buildPath($this->pluginPath, 'app', 'Controllers', 'Shortcodes'),
        ];

        $this->views = self::buildPath($this->pluginPath, 'app', 'views');

        $this->helpers = self::buildPath($this->app, 'helpers');
    }
    
}

