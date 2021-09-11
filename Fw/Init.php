<?php
/**
 * Procesos iniciales.
 * Creación de:
 * 
 * Routing
 * Menu Pages
 * Shortcodes
 * 
 */

namespace Fw;

use Fw\Init\LoadAssets;
use Fw\Init\Routing\RoutingManager;

class Init  
{
    public object $paths;

    public function __construct(Paths $paths, array $args) {
        $this->paths = $paths;
        $this->args = $args;
        
        # Cargar assets.
        $this->loadAssets();

        # Routing.
        RoutingManager::initialize(
            Paths::createNamepace($this->paths->pluginPath),
            $this->paths->controllers->public,
            $this->args['routing']
        );

        add_action('init', [$this, 'init']);
    }

    /**
     * Procesos iniciales del framework y la aplicacion.
     *
     * @return void
     **/
    public function init()
    {
        add_action('admin_init', [$this, 'adminInit']);
    }

    public function adminInit()
    {
        
    }


    /**
     * Carga los assets (css y js) publicos y admin.
     *
     * @return void
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
}
