<?php
/**
 * Clase principal del Framework.
 * 
 * Carga las rutas.
 * Setea los argumentos.
 * Construcción de estrucruas.
 * Procesos iniciales.
 * 
 */

namespace Fw;

use Fw\Paths;
use Fw\Init\Init;
use Fw\Config\Apps;

class Framework  
{ 
    /** @var Paths $paths Objecto de rutas de la aplicación. */
    public object $paths;

    /**
     * @param string $pluginFilePath Ruta del archivo principal del plugin.
     * @param array $args Argumentos (setting) de la aplicación.
     **/
    public function __construct (
        public string $pluginFilePath,
        public array $args = array()
    )
    {
        # Se establecen argumentos de la aplicación.
        $instance = $this->setArguments();

        # Creación de estructuras.
        Structures\BuildStructures::init(['autoload'], [
            'mode' => $instance->config->mode,
            'autoload' => $instance->config->autoload,
            'pluginPath' => $instance->paths->pluginPath,
        ]);
        
        # Se carga el autoload del plugin
        if ( file_exists($appAutoload = Paths::buildPath($instance->paths->pluginPath, 'autoload', 'autoload.php')) ) {
            include_once $appAutoload;
        }

        # Procesos iniciales
        new Init( $instance->config->pluginSlug );
    }

    /**
     * Se establecen los argumentos de la App.
     * Esto permitira implementar a futuro configuraciones de la App.
     *
     * @return object
     **/
    public function setArguments() : object
    {
        # Rutas de la aplicacion.
        $paths = new Paths( $this->pluginFilePath );

        $mode = array_key_exists('mode', $this->args) ? $this->args['mode'] : 'dev';
        $pluginSlug = strToSlug( basename($paths->pluginFilePath, '.php') );
        $config = array_replace_recursive( array(
            'mode' => $mode,
            'pluginSlug' => strToSlug( basename($paths->pluginFilePath, '.php') ),
            'namespace' => Paths::createNamepace($paths->pluginPath),
            'autoload' => array(
                'uniqueName' => Structures\Autoload::createUniqueName(basename($paths->pluginFilePath)),
                'psr-4' => [
                    Paths::createNamepace($paths->pluginFilePath) . "\\" => 'app/',
                ],
                'files' => Paths::listFiles($paths->pluginPath, Paths::buildPath($paths->helpers), '*.php'),
            ),
            'routing' => array(
                'force' => false,
            ),
            'loadAssets' => array(
                'admin' => [
                    'is_admin' => true,
                    'load' => 'all',
                    'mode' => $mode,
                    'path' => $paths->adminAssets,
                    'argsJs' => [
                        'ajaxurl' => admin_url('admin-ajax.php')
                    ],
                    'in_footer' => false
                ], 
                'public' => [
                    'load' => 'all',
                    'mode' => $mode,
                    'path' => $paths->assets,
                    'argsJs' => [
                        'ajaxurl' => admin_url('admin-ajax.php')
                    ],
                    'frameworks' => [
                        'bootstrap' => true
                    ],
                    'in_footer' => false
                ]
            ),
        ), $this->args );

        return $instance = Apps::getInstance()::setApp(
            $pluginSlug, 
            array(
                'config' => (object) $config,
                'paths' => (object) $paths,
            )
        );
    }

}

