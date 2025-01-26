<?php
/**
 * Generador de los forlders y archivos para un Plugin Base.
 */

namespace Fw\Structures;

use Fw\Paths;

class BasePlugin  
{
    /**
     * Se construye el Plugin Base.
     *
     * @param array $data
     * @return bool
     **/
    public static function buildBasePlugin(array $data) : bool
    {
        # Se sanitizan los datos.
        $data = self::sanitizeTextOfArray($data);
        $data['slug'] = strToSlug($data['pluginName']);

        $data['pluginPath'] = Paths::buildPath(WP_PLUGIN_DIR, $data['slug']);
        # Se valida que no existe el plugin
        if ( file_exists($data['pluginPath']) ) {
            return false;
        }
        if ( self::pluginNameExists($data['pluginName']) ) {
            return false; 
        } 

        $data['namespace'] = Paths::createNamespace($data['pluginName']);
        
        # Se copia la estructura base del plugin
        if ( !\Fw\Structures\BuildStructures::copyStructure(Paths::buildPath(__DIR__, 'templateApp'), $data['pluginPath']) ) {
            return false;
        }
        
        # Se renombran archivos.
        self::renameFiles($data);
        
        # Se Reemplazan los tags en los archivos requeridos,
        if ( !self::replaceTags($data) ) {
            return false;
        }

        return true;
    }

    public static function renameFiles($data)
    {
        # Se renombra el archivo principal del plugin.
        rename(
            Paths::buildPath($data['pluginPath'], 'plugin-ejemplo.php'),
            Paths::buildPath($data['pluginPath'], $data['slug'] . '.php')
        );

        # Se renombran archivos assets.
        $assetsPath = Paths::buildPath($data['pluginPath'], 'app', 'assets');
        $assets = [
            # Public
            'style.css' => Paths::buildPath( $assetsPath, 'css', 'style.css' ),
            'script.js' => Paths::buildPath( $assetsPath, 'js', 'script.js' ),
            # Admin
            'adminStyle.css' => Paths::buildPath( $assetsPath, 'admin', 'css', 'adminStyle.css' ),
            'adminScript.js' => Paths::buildPath( $assetsPath, 'admin', 'js', 'adminScript.js' ),
        ];

        foreach ($assets as $asset => $path) {
            $newPath = str_replace($asset, "{$data['slug']}-{$asset}", $path);
            rename( $path, $newPath );
        }

    }

    /**
     * Reemplaza los Tags en los archivos del nuevo plugin.
     *
     * @param array $data Información del plugin.
     * @return bool
     **/
    public static function replaceTags (array $data) : bool
    {
        extract($data);
        try {
            # Archivo principal del plugin
            $file = Paths::buildPath( $pluginPath, basename($pluginPath) . '.php' );

            ob_start();
            echo <<<EOT
            * Plugin Name:    		$pluginName
            * Plugin URI: 			$pluginUri
            * Description:    		$description
            * Version:        		$version
            * Author: 				$author
            * Author URI: 			$authorUri
            EOT;
            $content = str_replace(
                '# Data',
                ob_get_clean(),
                file_get_contents( $file )
            );
            file_put_contents($file, $content);

            # Reemplazo de tags
            self::iterateControllers( Paths::buildPath($pluginPath, 'app', 'Controllers'), $data );
            self::iterateControllers( Paths::buildPath($pluginPath, 'app', 'Models'), $data );
            
            return true;
        } catch (\Exception $e) {
            if (WP_DEBUG) {
                echo "<strong>Details:</strong> {$e->getMessage()}. <br>";
            }
            return false;
        }
    }


    /**
     * Se recorre recursivamente los controladores para el reemplazo de tags.
     *
     * @param string $source Ruta de la estructura que se copiara .
     * @param string $data Información del plugin.
     * @return bool
     * 
     **/
    public static function iterateControllers(string $source, array $data ) : void
    {
        $dir = opendir($source);
        while(( $file = readdir($dir)) ) {
            if ( ( $file != '.' ) && ( $file != '..' ) ) {
                # Se verifica si es un folder.
                if ( is_dir(Paths::buildPath($source, $file) ) ) {
                    self::iterateControllers(
                        Paths::buildPath($source, $file),
                        $data
                    );
                } else if ( pathinfo($file, PATHINFO_EXTENSION) === 'php' ) { 
                    $namespace = array();
                    $file = Paths::buildPath($source, $file);

                    # NameSpace
                    $path = explode( DIRECTORY_SEPARATOR, dirname($file) );
                    $path = array_reverse($path);

                    foreach ($path as $key => $name) {
                        # Si en la ruta del archivo se encuentra la palabra "app"
                        # Seguido de $data['slug']
                        if ( $name == 'app' && $path[$key+1] == $data['slug'] ) {
                            break;
                        }
                        $namespace[] = $name;
                    }
                    $namespace = array_reverse($namespace);
                    $namespace = $data['namespace'] . '\\' . implode('\\', $namespace);

                    $search  = [
                        '# namespace', 
                        '# use namespace',
                    ];

                    $replace = [
                        "namespace {$namespace}", 
                        'use',
                    ];

                    $content = str_replace(
                        $search, 
                        $replace, 
                        file_get_contents( $file )
                    );

                    file_put_contents($file, $content);
                }
            }
        }
        closedir($dir);
    }


    /**
     * Comprueba si un plugin existe en base a su Nombre
     *
     * @param string $pluginName
     * @return bool
     **/
    public static function pluginNameExists(string $pluginName) : bool
    {
        $pluginName = trim($pluginName);
        $plugins = array_column(get_plugins(), 'Name');

        foreach ($plugins as $name) {
            if ( strtolower($name) == strtolower($pluginName) ) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Sanitiza los values de un array.
     *
     * @param array $args
     * @return array
     **/
    public static function sanitizeTextOfArray(array $args) : array
    {
        foreach ($args as $key => $arg) {
            $args[$key] = sanitize_text_field($arg);
        }

        return $args;
    }

}
