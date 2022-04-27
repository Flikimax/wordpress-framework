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
     * Determina si se debe cargar todo o un conjunto de assets en especifico.
     *
     * @return void
     **/
    public function loadAsset() : void
    {
        # Carga de archivos
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
                    'mode' => $args['mode'],
                ));
                break;
            
            default:
                self::loadAll($this->args);
                break;
        }

        # Cargar Frameworks.
        if ( array_key_exists('frameworks', $this->args) ) {
            self::loadFrameworks(array (
                'mode' => $this->args['mode'],
                'path' => $this->args['path'],
                'frameworks' => $this->args['frameworks'],
            ));
        }
    }

    /**
     * Carga los frameworks especificados.
     *
     * @param array $args Arreglo con los parámetros.
     * @return void
     **/
    public static function loadFrameworks(array $args)
    {
        $handle = basename( Paths::trimPath($args['path'], 2) );
        $handle = str_replace(' ', '-', $handle);

        # Version.
        $mode = $args['mode'] . 'Mode';
        if ( method_exists(static::class, $mode) ) {
            $version = self::{$mode}();
        }
        $version = self::productionMode();

        foreach ($args['frameworks'] as $framework => $enable) {
            if ( !$enable ) {
                continue;
            }

            switch ($framework) {
                case 'bootstrap':
                    wp_enqueue_style ( $handle . 'bootstrapCss', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css', array(), $version );
                    wp_enqueue_script( $handle . 'bootstrapJs', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js', array(), $version) ;
                    break;
                
                default:
                    break;
            }
        }
    }

    /**
     * Carga todos los assets (js y css).
     *
     * @param array $args Recibe el mismo array que el constructor, esto para un uso desaclopado.
     * @return void
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
            'mode' => $args['mode'],
        ));
    }
    /**
     * Carga los archivos js.
     *
     * @param array $args 
     * array (
     * 'path' => $args['path'],
     * 'argsJs' => $args['argsJs'], // optional
     * 'mode' => $args['mode']
     * )
     * 
     * @return void
     **/
    public static function loadJs(array $args) : void
    {
        # Se obtiene el array con las rutas de los archivos
        if ( !$fileList = self::validateFiles(Paths::buildPath($args['path'], 'js'), 'js') ) {
            return;
        }

        # Validación de datos y retorno de la version
        if ( !$version = self::preloadValidation($fileList, $args)) {
            return;
        }
        
        # Validación de los parametros a enviar a los js
        if ( !array_key_exists('argsJs', $args) ) {
            $args['argsJs'] = false;
        }

        # Si cargar en el footer
        if ( !array_key_exists('in_footer', $args) ) {
            $args['in_footer'] = false;
        }
        
        # Registro
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
     * Carga los archivos css.
     *
     * @param array $args 
     * array (
     * 'path' => $args['path'],
     * 'mode' => $args['mode']
     * )
     * 
     * @return void
     **/
    public static function loadCss(array $args) : void
    {
        # Se obtiene el array con las rutas de los archivos
        if ( !$fileList = self::validateFiles(Paths::buildPath($args['path'], 'css'), 'css') ) {
            return;
        }

        # Validación de datos y retorno de la version
        if ( !$version = self::preloadValidation($fileList, $args)) {
            return;
        }

        # Registro
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
     * Validación de datos y retorno de la versión a utilizar por los assets.
     *
     * @param array $fileList Array con los paths de los assets
     * @param array $args Array con el modo a correr
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
     * Valida que existan archivos dentro de la ruta pasada.
     *
     * @param string $path Ruta a validar
     * @param string $type Tipo de archivo a buscar, ejemplo: 'js'
     * @return Mixes
     **/
    public static function validateFiles(string $path, string $type)
    {
        if ( file_exists($path) ) {
            if ( $fileList = glob( Paths::buildPath($path, "*.$type")) ) {
                return $fileList;
            }
        }

        return false;
    }

    
    /**
     * Modo desarrollo, se retorna time() para mantener actualizada la versión de los scrips.
     *
     * @return int
     **/
    public static function devMode() : int
    {
        return (int) time();
    }

    /**
     * Modo producción, se WPFW_VERSION para mantener fija la versión de los scrips.
     *
     * @return string
     **/
    public static function productionMode() : string
    {
        return WPFW_VERSION;
    }

}
