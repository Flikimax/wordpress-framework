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
use Fw\Structures\BuildStructures;

class Framework  
{ 
    /** @var Paths $paths Objecto de rutas de la aplicación. */
    public Paths $paths;
    
    /** @var object $instance */
    private object $instance;

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
        $this->setConfig();
        $this->createStructures();
        
        # Procesos iniciales
        new Init( $this->instance->config->pluginSlug );
    }
    
    /**
     * Creación de estructuras.
     *
     * @return void
     */
    protected function createStructures(): void
    {
        BuildStructures::init(['composer'], [
            'mode' => $this->instance->config->mode,
            'composer' => $this->instance->config->autoload,
            'pluginPath' => $this->instance->paths->pluginPath,
        ]);
    }
    
    /**
     * Se establecen los argumentos de la App.
     * Esto permitira implementar a futuro configuraciones de la App.
     *
     * @return void
     **/
    public function setConfig(): void
    {
        # Rutas de la aplicacion.
        $paths = new Paths( $this->pluginFilePath );

        $mode = array_key_exists('mode', $this->args) ? $this->args['mode'] : 'dev';
        $pluginSlug = strToSlug( basename($paths->pluginFilePath, '.php') );
        $config = array_replace_recursive( array(
            'mode' => $mode,
            'pluginSlug' => strToSlug( basename($paths->pluginFilePath, '.php') ),
            'namespace' => Paths::createNamespace($paths->pluginPath),
            'autoload' => array(
                'psr-4' => [
                    Paths::createNamespace($paths->pluginFilePath) . "\\" => 'app/',
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

        $this->instance = Apps::getInstance()::setApp( $pluginSlug, [
                'config' => (object) $config,
                'paths' => (object) $paths,
            ]
        );
    }

}

