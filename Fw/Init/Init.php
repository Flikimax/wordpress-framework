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

namespace Fw\Init;

use Fw\Init\MenuPage\MenuPagesManager;
use Fw\Init\Shortcode\ShortcodesManager;
use Fw\Init\LoadAssets;
use Fw\Init\Routing\RoutingManager;
use Fw\Config\Apps;
use Fw\Paths;

class Init  
{
    /** @var Paths $paths Objecto de rutas de la App. */
    public object $paths;
    /** @var string $namespace Namespace de la App. */
    public string $namespace;
    
    /** @var string $pluginSlug Slug de la App. */
    public function __construct( public string $pluginSlug ) {
        $this->paths = Apps::getConfig( $this->pluginSlug, 'paths' );

        # Namespace
        $namespace = Apps::getConfig( $this->pluginSlug, 'config' )?->namespace;
        if ( !$namespace ) {
            $namespace = Paths::createNamepace( $this->pluginSlug );
        }
        $this->namespace = $namespace;

        # Cargar assets.
        $this->loadAssets();

        # Routing.
        // TODO: Implementar nueva configuración.
        $routing = Apps::getConfig( $this->pluginSlug, 'config' )?->routing;
        if ( !$routing ) {
            $routing = [
                'force' => false,
            ];
        }
        RoutingManager::initialize(
            $this->namespace,
            $this->paths->controllers->routers,
            $routing
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

        # Menu Pages
        add_action('admin_menu', [
            new MenuPagesManager(
                $this->namespace,
                $this->paths->controllers->menuPages
            ), 
            'prepare'
        ]);

        # Shortcodes
        ShortcodesManager::initialize( $this->pluginSlug );
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
        $loadAsset = Apps::getConfig( $this->pluginSlug, 'config' )
        ?->loadAssets;

        if ( !$loadAsset ) {
            return;
        }

        # Cargar assets admin
        if (is_admin()) {
            new LoadAssets($loadAsset['admin']);
        } else {
            new LoadAssets($loadAsset['public']);
        }
    }

}