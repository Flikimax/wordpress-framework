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
    /** @var array $args Argumentos (setting) de la aplicación. */
    protected array $args;

    public function __construct (string $pluginFilePath, array $args = array())
    { 
        # Rutas de la aplicacion.
        $this->paths = new Paths($pluginFilePath);
        # Se establecen argumentos que seran utilizados para extension del Framework.
        $this->args = $this->setArguments($args);

        # Creación de estructuras.
        Structures\BuildStructures::init(['autoload'], [
            'mode' => $this->args['mode'],
            'autoload' => $this->args['autoload'],
            'pluginPath' => $this->paths->pluginPath,
        ]);
        
        # Se carga el autoload del plugin
        if ( file_exists($appAutoload = Paths::buildPath($this->paths->pluginPath, 'autoload', 'autoload.php')) ) {
            include_once $appAutoload;
        }

        # Procesos iniciales
        new Init($this->paths, [
            'loadAssets' => $this->args['loadAssets'],
            'routing' => $this->args['routing'],
        ]);
    }

    /**
     * Se establecen los argumentos de la App.
     * Esto permitira implementar a futuro configuraciones de la App.
     *
     * @param array $args
     * @return array
     **/
    public function setArguments(array $args) : array
    {
        $mode = array_key_exists('mode', $args) ? $args['mode'] : 'dev';
        $pluginSlug = strToSlug( basename($this->paths->pluginFilePath, '.php') );
        $config = array_replace_recursive( array(
            'mode' => $mode,
            'pluginSlug' => strToSlug( basename($this->paths->pluginFilePath, '.php') ),
            'autoload' => array(
                'uniqueName' => Structures\Autoload::createUniqueName(basename($this->paths->pluginFilePath)),
                'psr-4' => [
                    Paths::createNamepace($this->paths->pluginFilePath) . "\\" => 'app/',
                ],
                'files' => Paths::listFiles($this->paths->pluginPath, Paths::buildPath($this->paths->helpers), '*.php'),
            ),
            'routing' => array(
                'force' => false,
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
                    'frameworks' => [
                        'bootstrap' => true
                    ],
                    'in_footer' => false
                ]
            ),
        ), $args );

        # Se movera la vieja configuración ($config) a la nueva (Singleton).
        Apps::getInstance()::setApp(
            $pluginSlug, 
            array(
                'config' => (object) $config,
                'paths' => (object) $this->paths,
            )
        );

        # Este return es temporal hasta que se implemente la nueva configuracion de la App (Singleton).
        return $config;
    }

}

