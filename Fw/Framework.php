<?php
/**
 * Clase principal del Framework
 * 
 * Carga las rutas
 * Setea los argumentos
 * Valida, crea y carga el autoload de la aplicación
 * Procesos iniciales
 * 
 */

namespace Fw;

use Fw\Paths;
use Fw\Autoload;
use Fw\Init\LoadAssets;

class Framework  
{ 
    public object $paths;
    protected array $args;

    public function __construct (string $pluginFilePath, array $args = array())
    { 
        # Rutas de la aplicacion
        $this->paths = new Paths($pluginFilePath);
        # Se establecen argumentos que seran utilizados para extension del Framework
        $this->args = $this->setArguments($args);

        # Validación y generador del autoload de la App
        $this->appAutoload();
        
        # Se carga el autoload del plugin
        include_once ( Paths::buildPath($this->paths->pluginPath, 'autoload', 'autoload.php') );

        $this->init();
    }


    /**
     * Procesos iniciales del framework y la aplicacion
     *
     * @return Void
     **/
    public function init() : void
    {
        # Cargar assets 
        $this->loadAssets();
        
        // add_action('init', [new Init($this->paths), 'init']);
    }

    /**
     * Carga los assets (css y js) publicos y admin
     *
     * @return Void
     **/
    public function loadAssets() : void
    {
        # Cargar assets admin
        if (is_admin()) {
            new LoadAssets($this->args['loadAssets']['admin']);
        } else {
            new LoadAssets($this->args['loadAssets']['public']);
        }
    }

    /**
     * Generador del autoload de la App
     *
     * @return Void
     **/
    public function appAutoload() : void
    {
        Autoload::buildAutoload([
            'composerPath' => Paths::buildPath($this->paths->pluginPath, 'autoload'),
            'uniqueName' => $this->args['autoload']['uniqueName'],
            'autoload' => [
                'psr-4' => $this->args['autoload']['psr-4'],
                'files' => $this->args['autoload']['files']
            ],
            'mode' => $this->args['mode']
        ]);
    }
    
    /**
     * Se establecen los argumentos de la App
     * Esto permitira implementar a futuro configuraciones de la App
     *
     * @param Array $args
     * @return Array
     **/
    public function setArguments(array $args) : array
    {
        $mode = array_key_exists('mode', $args) ? $args['mode'] : 'production';

        return array_replace_recursive( array(
            'mode' => $mode,
            'autoload' => array(
                'uniqueName' => Autoload::createUniqueName(basename($this->paths->pluginFilePath)),
                'psr-4' => [
                    Autoload::createNamepace($this->paths->pluginFilePath) . "\\" => 'app/',
                ],
                'files' => $this->listHelperFiles(),
            ),
            'loadAssets' => array(
                'admin' => [
                    'is_admin' => true,
                    'load' => 'all',
                    'mode' => $mode,
                    'path' => $this->paths->adminAssets,
                    'argsJs' => [
                        'ajaxurl' => admin_url('admin-ajax.php')
                    ],
                    'in_footer' => false
                ], 
                'public' => [
                    'load' => 'all',
                    'mode' => $mode,
                    'path' => $this->paths->assets,
                    'argsJs' => [
                        'ajaxurl' => admin_url('admin-ajax.php')
                    ],
                    'in_footer' => false
                ]
            ),
        ), $args );
    }

    /**
     * Lista en un array los archivos helpers de la aplicación
     *
     * @return Array
     **/
    public function listHelperFiles() : array
    {   # Archivos para autocargar
        $files = array();
        foreach (glob(Paths::buildPath($this->paths->helpers, '*.php')) as $index => $file) {
            $file = str_replace($this->paths->pluginPath, '', $file);
            $files[] = ltrim($file, '/');
        }
        return $files;
    }

}

