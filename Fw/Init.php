<?php
/**
 * Procesos iniciales
 * Creación de:
 * Menu Pages
 * Shortcodes
 * 
 */

namespace Fw;

class Init  
{
    public object $paths;

    public function __construct(Paths $paths) {
        $this->paths = $paths;
    }

    public function init()
    {
        // SHORTCODES

        
        add_action('admin_init', [$this, 'adminInit']);
    }

    public function adminInit()
    {
        // REGISTRAR MENU PAGES
    }
}
