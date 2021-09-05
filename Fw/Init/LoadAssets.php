<?php
/**
 * Carga assets Js y/o css para el admin o para la parte pública
 * 
 */

namespace Fw\Init;

use Fw\Paths;

class LoadAssets  
{
    public array $args;

    /**
     * @param Type $args Recibe un array de la siguiente forma: 
     * 
     * array (
     * 'is_admin' => true, // optional
     * 'load' => 'all|css|js',
     * 'mode' => 'dev|production',
     * 'path' => $path,
     * 'argsJs' => array(), // optional
     * );
     *
     **/
    public function __construct(array $args) 
    {
        $this->args = $args;

        if ( array_key_exists('is_admin', $this->args) ) {
            add_action('admin_enqueue_scripts', [$this, 'loadAsset']); 
        } else {
            add_action('wp_enqueue_scripts', array($this, 'loadAsset')); 
        }
    }

    /**
     * Determina si se debe cargar todo o un conjunto de assets en especifico
     *
     * @return Void
     **/
    public function loadAsset() : void
    {
        switch ($this->args['load']) {
            case 'js':
                self::loadJs(array (
                    'path' => $args['path'],
                    'argsJs' => $args['argsJs'],
                    'mode' => $args['mode']
                ));
                break;

            case 'css':
                self::loadCss(array (
                    'path' => $args['path'],
                    'mode' => $args['mode']
                ));
                break;
            
            default:
                self::loadAll($this->args);
                break;
        }
    }

    /**
     * Carga todos los assets (js y css)
     *
     * @param Array $args Recibe el mismo array que el constructor, esto para un uso desaclopado
     * @return Void
     **/
    public static function loadAll(array $args)
    {
        # CARGAR JS
        self::loadJs(array (
            'path' => $args['path'],
            'argsJs' => $args['argsJs'],
            'mode' => $args['mode']
        ));

        # CARGAR CSS
        self::loadCss(array (
            'path' => $args['path'],
            'mode' => $args['mode']
        ));
    }

    /**
     * Carga los archivos js
     *
     * @param Array $args 
     * array (
     * 'path' => $args['path'],
     * 'argsJs' => $args['argsJs'], // optional
     * 'mode' => $args['mode']
     * )
     * 
     * @return Void
     **/
    public static function loadJs(array $args) : void
    {
        # SE OBTIENE EL ARRAY CON LAS RUTAS DE LOS ARCHIVOS
        if ( !$fileList = self::validateFiles(Paths::buildPath($args['path'], 'js'), 'js') ) {
            return;
        }

        # VALIDACION DE DATOS Y RETORNO DE LA VERSION
        if ( !$version = self::preloadValidation($fileList, $args)) {
            return;
        }
        
        # VALIDACIÓN DE LOS PARAMETROS A ENVIAR A LOS JS
        if ( !array_key_exists('argsJs', $args) ) {
            $args['argsJs'] = false;
        }

        # SI CARGAR EN FOOTER
        if ( !array_key_exists('in_footer', $args) ) {
            $args['in_footer'] = false;
        }
        
        # REGISTRO
        $pluginPath = Paths::buildPath('wp-content', 'plugins');
        foreach ($fileList as $js) {
            $appPath = strstr($js, $pluginPath);
            $path = str_replace($pluginPath, '', $appPath);

            $handle = 'wpFw_' . basename($js, '.js');
            wp_register_script(
                $handle, 
                plugins_url($path),
                array(), 
                $version, 
                $args['in_footer']
            );
            wp_enqueue_script($handle);

            if ($args['argsJs']) {
                wp_localize_script(
                    $handle, 
                    'WPFW_AJAX', 
                    $args['argsJs']
                );
            }
        }
    }

     /**
     * Carga los archivos css
     *
     * @param Array $args 
     * array (
     * 'path' => $args['path'],
     * 'mode' => $args['mode']
     * )
     * 
     * @return Void
     **/
    public static function loadCss(array $args) : void
    {
        # SE OBTIENE EL ARRAY CON LAS RUTAS DE LOS ARCHIVOS
        if ( !$fileList = self::validateFiles(Paths::buildPath($args['path'], 'css'), 'css') ) {
            return;
        }

        # VALIDACION DE DATOS Y RETORNO DE LA VERSION
        if ( !$version = self::preloadValidation($fileList, $args)) {
            return;
        }

        # REGISTRO
        $pluginPath = Paths::buildPath('wp-content', 'plugins');
        foreach ($fileList as $css) {
            $appPath = strstr($css, $pluginPath);
            $path = str_replace($pluginPath, '', $appPath);

            $handle = 'wpFw_' . basename($css, '.css');
            wp_register_style(
                $handle, 
                plugins_url($path),
                array(), 
                $version, 
            );
            wp_enqueue_style($handle);
        }
    }


    /**
     * Validación de datos y retorno de la versión a utilizar por los assets
     *
     * @param Array $fileList Array con los paths de los assets
     * @param Array $args Array con el modo a correr
     * @return Mixes
     **/
    public static function preloadValidation($fileList, $args)
    {
        # VALIDACION DE DATOS
        if ( !is_array($fileList) || !array_key_exists('mode', $args)) {
            return false;
        }

        # MODO
        $mode = $args['mode'] . 'Mode';
        if ( method_exists(static::class, $mode) ) {
            return self::{$mode}();
        }

        return self::productionMode();
    }

    /**
     * Valida que existan archivos dentro de la ruta pasada
     *
     * @param String $path Ruta a validar
     * @param String $type Tipo de archivo a buscar, ejemplo: 'js'
     * @return Mixes
     **/
    public static function validateFiles(string $path, string $type)
    {
        if ( file_exists($path) ) {
            # CARGAR JS
            if ( $fileList = glob( Paths::buildPath($path, "*.$type")) ) {
                return $fileList;
            }
        }

        return false;
    }

    
    /**
     * Modo desarrollo, se retorna time() para mantener actualizada la versión de los scrips
     *
     * @return Int
     **/
    public static function devMode() : int
    {
        return (int) time();
    }

    /**
     * Modo producción, se WPFW_VERSION para mantener fija la versión de los scrips
     *
     * @return Int
     **/
    public static function productionMode() : int
    {
        return (int) WPFW_VERSION;
    }

}
