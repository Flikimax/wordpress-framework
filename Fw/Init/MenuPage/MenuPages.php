<?php
/**
 * Crea las Menu Pages y las Sub Menu Page.
 * 
 */

namespace Fw\Init\MenuPage;

use Fw\Core\Request\RequestMenuPage;

class MenuPages 
{
    /**
     * Se crean las (Sub) Menu Page
     *
     * @param array $menuPages Información necesaria para la creación de Menu Page y Sub Menu Page.
     **/
    public static function createMenuPages(array $menuPages)
    {
        if ( !$menuPages || !is_array($menuPages) ) {
            return;
        }

        $pluginPath = $menuPages['path'];
        unset($menuPages['path']);

        foreach ($menuPages as $name => $args) {
            $name = spaceUpper($name);

            # Menu Page
            add_menu_page( 
                $args['pageTitle'],
                $args['menuTitle'],
                $args['capability'], 
                $args['menuSlug'], 
                [new RequestMenuPage(
                    $pluginPath,
                    $args['callable']['controller'],
                    $args['callable']['method']
                ), 'send'], 
                $args['icon'], 
                $args['position']
            );
            add_submenu_page( 
                $args['menuSlug'],
                $args['pageTitle'], 
                $args['menuTitle'], 
                $args['capability'], 
                $args['menuSlug'],
                '',
            );
            
            # Si cargar Sub Menu Page
            if ( isset($args['subMenuPages']) ) {
                foreach ($args['subMenuPages'] as $subArgs) {
                    add_submenu_page(
                        $args['menuSlug'],
                        $subArgs['pageTitle'], 
                        $subArgs['menuTitle'], 
                        $subArgs['capability'], 
                        $subArgs['menuSlug'], 
                        [new RequestMenuPage(
                            $pluginPath,
                            $subArgs['callable']['controller'],
                            $subArgs['callable']['method']
                        ), 'send'],
                        $subArgs['position']
                    );
                }
            }
        }
    }
    
}
